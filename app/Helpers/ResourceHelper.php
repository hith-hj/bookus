<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\Image;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;


if (!function_exists('uploadImage')) {
    function uploadImage($key = "avatar", $folder = 'users')
    {

        $request = \request();
        if ($request->hasFile($key)) {
            $file = $request->file($key);
            $format = $file->getClientOriginalExtension();

            $name = time() . ".$format";
            Storage::put($name, $file->getContent());
            if (Storage::move($name, "public/$folder/" . $name)) {
                $optimizerChain = OptimizerChainFactory::create();
                $optimizerChain->optimize(storage_path('app/public/' . $folder . '/' . $name));
                return "/$folder/" . $name;
            }
        }
        return false;
    }
}

function updateUploadImage($item, $key = 'avatar', $folder = 'users')
{
    if ($item->{$key} != null){
        Storage::disk('public')->delete($item->{$key});
    }

    $request = request();
    $file = $request->file($key);
    $format = $file->getClientOriginalExtension();

    $name = time() . ".$format";
    Storage::put($name, $file->getContent());
    if (Storage::move($name, "public/$folder/" . $name)) {
        $optimizerChain = OptimizerChainFactory::create();
        $optimizerChain->optimize(storage_path('app/public/' . $folder . '/' . $name));
        return "/$folder/" . $name;
    }

    return false;
}

function deleteMedia($item, $key = 'avatar')
{
    if ($item->{$key} != null){
        Storage::disk('public')->delete($item->{$key});
    }
}

if (!function_exists('showImage')) {
    function showImage($folder, $image)
    {
        $path = storage_path('app/public/images/' . $folder . '/' . $image);
        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type)->send();

        return $response;
    }
}

if (!function_exists('showImage2')) {
    function showImage2($folder, $image)
    {
        $path = storage_path('app/public/' . $folder . '/' . $image);
        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type)->send();

        return $response;
    }
}

if (!function_exists('uploadFile')) {
    function uploadFile($key = "file", $folder = 'projects')
    {
        $request = \request();
        if ($request->hasFile($key)) {

            $uploadedFile = $request->file($key);
            $moved = Storage::disk('public')->put($folder, $uploadedFile);

            if ($moved)
                return $moved; // url to file
        }
        return false;
    }
}

if (!function_exists('uploadMultiImages')) {
    function uploadMultiImages($key = "photos", $folder = 'projects')
    {
        $request = \request();
        $imagesNames = array();
        foreach ($request->file($key) as $image) {
            Storage::disk('public')->exists($folder) or Storage::disk('public')->makeDirectory($folder);
            $imageName = Storage::disk('public')->put($folder, $image);
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->optimize(storage_path("app/public/$imageName"));
            array_push($imagesNames, $imageName);
        }
        return $imagesNames;
    }
}


if (!function_exists('uploadMultiImages')) {
    function uploadMultiImages($key = "photos", $folder = 'projects')
    {
        $request = \request();
        $imagesNames = array();
        foreach ($request->file($key) as $image) {
            Storage::disk('public')->exists($folder) or Storage::disk('public')->makeDirectory($folder);
            $imageName = Storage::disk('public')->put($folder, $image);
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->optimize(storage_path("app/public/$imageName"));
            array_push($imagesNames, $imageName);
        }
        return $imagesNames;
    }
}
