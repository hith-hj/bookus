<?php

namespace App\Http\Traits;


trait ReviewTrait {

    public function getModelType($model_type): string
    {
        switch ($model_type){
            case 'product':
                return 'App\Models\Product';
            case 'designer':
                return 'App\Models\User';
            case 'project':
                return 'App\Models\Project';
            case 'vendor':
                return 'App\Models\Admin';
            default:
                return '';
        }
    }

    public function getFavoritesByModelName($user, $type)
    {
        switch ($type){
            case 'product':
                return $user->ProductReviews();
            case 'designer':
                return $user->DesignerReviews();
            case 'vendor':
                return $user->VendorReviews()->role('vendor');
            default:
                return $user->VendorReviews();
        }
    }
}
