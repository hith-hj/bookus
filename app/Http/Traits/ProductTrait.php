<?php

namespace App\Http\Traits;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;

trait ProductTrait
{
    public function getCategories()
    {
        return Category::query()->get();
    }

    public function testAttributes(Request $request)
    {
        foreach ($request->get('colors') as $color){
            $colors[] = $color['colors'];
        }

        return $colors;
    }

    public function addAttributes(Request $request, Product $product)
    {
        if($request->has('attributes'))
            foreach($request->get('attributes') as $key => $attribute){
                ProductAttribute::create([
                    'product_id' => $product->id,
                    'attribute_id' => $key,
                    'value' => $attribute
                ]);
            }
    }

    public function addColors(Request $request): array
    {
        $colors = [];
        if ($request->has('colors')){
            foreach ($request->get('colors') as $color){
                $colors[] = $color['colors'];
            }

        }
        return $colors;
    }

    public function addSizes(Request $request): array
    {
        $sizes = [];
        if ($request->has('sizes')){
            $sizes = [];
            foreach ($request->get('sizes') as $size){
                $sizes[] = $size['sizes'];
            }
        }
        return $sizes;
    }

    public function updateAttributes(Request $request, Product $product)
    {
        if ($request->has('attributes'))
        {
            $productAttributes = $product->attributes()->pluck('attributes.id')->toArray();
            foreach($request->get('attributes') as $key => $attribute){

                if ($attribute){

                    $attributeObject = Attribute::find($key);
                    if ($attributeObject->type == 'checkbox')
                        $attribute = $attribute == 'on' || $attribute == 1 ? '1' : '0';

                    $productAttributes = array_diff($productAttributes, [$key]);

                    ProductAttribute::updateOrCreate([
                        'product_id' => $product->id,
                        'attribute_id' => $key,
                    ],[
                        'product_id' => $product->id,
                        'attribute_id' => $key,
                        'value' => $attribute
                    ]);

                }
                ProductAttribute::where('product_id', $product->id)->whereIn('attribute_id', $productAttributes)->delete();
            }
        }
    }

    public function uploadMedia(Request $request, Product $product): Product
    {
        if ($request->hasFile('images')){
            $imagesNames = array();
            foreach ($request->file('images') as $image) {
                $imageName = Storage::disk('public')->put('products', $image);
                ImageOptimizer::optimize('storage/'.$imageName);

                array_push($imagesNames, $imageName);
            }
            $product->images = $imagesNames;
        }

        if ($request->hasFile('featured_image')) {
            $product->featured_image = Storage::disk('public')->put('products/featured', $request->file('featured_image'));
            ImageOptimizer::optimize('storage/'.$product->featured_image);
        }

        if ($request->hasFile('file2d')  && $request->get('model_type') == '1') {
            $product->file2d = Storage::disk('public')->put('products/2d', $request->file('file2d'));
            ImageOptimizer::optimize('storage/'.$product->file2d);
        }

        if ($request->hasFile('file3d') && $request->get('model_type') == '0') {
            $product->file3d = Storage::disk('public')->put('products/3d', $request->file('file3d'));
        }

        if($request->hasFile('video')){
            $file = $request->file('video');
            $extension = $file->extension();

            $filePath = 'videos/'.$extension;
            $product->video = Storage::disk('public')->put($filePath, $file);
        }

        return $product;
    }

    public function updateMedia(Request $request, Product $product){
        if ($request->hasFile('images')){
            // if there is an old image delete it
            if ($product->images != null){
                foreach ($product->images as $image){
                    Storage::disk('public')->delete($image);
                }
            }

            // store the new image
            $imageNames = array();
            foreach ($request->file('images') as $image) {
                $imageName = Storage::disk('public')->put('products', $image);
                ImageOptimizer::optimize('storage/'.$imageName);
                array_push($imageNames, $imageName);
            }
            $product->images = $imageNames;
        }
        if ($request->hasFile('featured_image')) {
            Storage::disk('public')->delete($product->featured_image);
            $product->featured_image = Storage::disk('public')->put('products/featured', $request->file('featured_image'));
            ImageOptimizer::optimize('storage/'.$product->featured_image);
        }

        if ($request->hasFile('file2d') && $request->get('model_type') == '1') {
            Storage::disk('public')->delete($product->file2d);
            Storage::disk('public')->delete($product->file3d);
            $product->file3d = null;
            $product->file2d = Storage::disk('public')->put('products/2d', $request->file('file2d'));
            ImageOptimizer::optimize('storage/'.$product->file2d);
        }

        if ($request->hasFile('file3d') && $request->get('model_type') == '0') {
            Storage::disk('public')->delete($product->file3d);
            Storage::disk('public')->delete($product->file2d);
            $product->file2d = null;
            $product->file3d = Storage::disk('public')->put('products/3d', $request->file('file3d'));
            ImageOptimizer::optimize('storage/'.$product->file3d);
        }

        if($request->hasFile('video')){
            Storage::disk('public')->delete($product->video);

            $file = $request->file('video');
            $extension = $file->extension();

            $filePath = 'videos/'.$extension;
            $product->video = Storage::disk('public')->put($filePath, $file);
        }

        return $product;
    }

    public function addRelatedProducts(Request $request, Product $product)
    {
        if ($request->has('relevant_products')){
            $product->relevantProducts()->attach($request->get('relevant_products'));
        }
    }

    public function updateRelatedProducts(Request $request, Product $product)
    {
        $product->relevantProducts()->detach();
        if ($request->has('relevant_products')){
            $product->relevantProducts()->attach($request->get('relevant_products'));
        }
    }

    public function addRecommendedContractors(Request $request, Product $product)
    {
        if ($request->has('recommended_contractors')){
            $product->recommendedContractors()->attach($request->get('recommended_contractors'));
        }
    }

    public function updateRecommendedContractors(Request $request, Product $product)
    {
        $product->recommendedContractors()->detach();
        if ($request->has('recommended_contractors')){
            $product->recommendedContractors()->attach($request->get('recommended_contractors'));
        }
    }

    public function getMostLikedProducts($category): Builder
    {
        return Product::query()->withCount(['wishListUsers' => function($query) use ($category){
            $query->where('products.created_at', '>=', now()->subMonth());
            if ($category)
                $query->where('products.category_id', $category);
        }])->where('status', 1)->orderBy('wish_list_users_count', 'desc');
    }

    public function likedByUserProducts($products, $user){
        return $products->map(function($product) use ($user){
            $product->liked_by = $product->isLikedBy($user);
            return $product;
        });
    }

    public function getMostSellProducts(Request $request)
    {
        $products =  Product::query()->withCount('items')
            ->orderBy('items_count','desc');

        if($request->has('status'))
            $products = $products
                ->where('status', 1);

        return $products
            ->take(10)
            ->get();
    }



}
