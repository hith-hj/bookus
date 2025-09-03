<?php

use App\Models\{Category, Attribute, Country, Order, Product, Profession, Project, ProjectStatus, Setting, Style, Tag};
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

function localImage($file, $default = '')
{
    if (!empty($file)) {
        return Str::of(Storage::disk('local')->url($file))->replace('storage', 'uploads');
    }

    return $default;
}

function storageImage($file, $default = '')
{
    if (Str::contains($file, 'picsum.photos'))
        return $file;
    if (!empty($file)) {
        return str_replace('\\', '/', Storage::disk('public')->url($file));
    }

    return $default;
}

function secToMin($seconds)
{
    return $seconds / 60;
}

function newStd($array = []): stdClass
{
    $std = new \stdClass();
    foreach ($array as $key => $value) {
        $std->$key = $value;
    }
    return $std;
}

function modelName($string = ''): string
{
    return 'App\Models\\'.Str::of($string)->camel()->ucfirst();
}

function getRoles()
{
    Return Role::all();
}

function getStatusVariables(): array
{
    $active = newStd(['name' => __('admin.active'), 'value'=> 1]);
    $inactive = newStd(['name' => __('admin.inactive'), 'value'=> 0]);
    return [$active, $inactive];
}

function getBlogTypesVariables(): array
{
    $active = newStd(['name' => __('admin.story'), 'value'=> 'story']);
    $inactive = newStd(['name' => __('admin.tools'), 'value'=> 'tools']);
    return [$active, $inactive];
}

function getModelTypeVariables(): array
{
    $active = newStd(['name' => __('admin.file2d'), 'value'=> 1, 'id' => 1]);
    $inactive = newStd(['name' => __('admin.file3d'), 'value'=> 0, 'id' => 0]);
    return [$active, $inactive];
}

function getCountries()
{
    return Country::all();
}

function getOrderStatusClass($status): string
{
    switch ($status){
        case Order::STATUS_PENDING:
            return 'warning';
        case Order::STATUS_DELIVERED:
            return 'success';
        case Order::STATUS_ONGOING:
            return 'info';
        case Order::STATUS_CANCELLED:
            return 'danger';
        default:
            return '';
    }
}

function getProjectStatusClass($status): string
{
    switch ($status){
        case ProjectStatus::PENDING_ID:
            return 'warning';
        case ProjectStatus::NEGOTIATION_ID:
            return 'default';
        case ProjectStatus::DELIVERED_ID:
        case ProjectStatus::ONGOING_ID:
            return 'info';
        case ProjectStatus::ACCEPTED_ID:
            return 'success';
        case ProjectStatus::REJECTED_ID:
            return 'danger';
        default:
            return '';
    }
}

function getStatusProject($status)
{
    switch ($status){
        case ProjectStatus::PENDING_ID:
            return __('project.pending');
        case ProjectStatus::NEGOTIATION_ID:
            return __('project.negotiating');
        case ProjectStatus::ONGOING_ID:
            return __('project.ongoing');
        case ProjectStatus::DELIVERED_ID:
            return __('project.delivered');
        case ProjectStatus::ACCEPTED_ID:
            return __('project.accepted');
        case ProjectStatus::REJECTED_ID:
            return __('project.rejected');
        default:
            return __('admin.empty');
    }
}

function getStatusOrder($status)
{
    switch ($status){
        case Order::STATUS_PENDING:
            return __('order.pending');
        case Order::STATUS_DELIVERED:
            return __('order.delivered');
        case Order::STATUS_ONGOING:
            return __('order.ongoing');
        case Order::STATUS_CANCELLED:
            return __('order.cancelled');
        default:
            return __('admin.empty');
    }
}

function getStatusOrderVariables(): array
{
    $pending = newStd(['name' => __('order.pending'), 'value'=> Order::STATUS_PENDING]);
    $delivered = newStd(['name' => __('order.delivered'), 'value'=> Order::STATUS_DELIVERED]);
    $ongoing = newStd(['name' => __('order.ongoing'), 'value'=> Order::STATUS_ONGOING]);
    $cancelled = newStd(['name' => __('order.cancelled'), 'value'=> Order::STATUS_CANCELLED]);

    return [$pending, $delivered, $ongoing, $cancelled];
}

function getBannerModelTypes(): array
{
    $vendor = newStd(['name' => __('admin.vendor'), 'value'=> 'App\Models\Admin']);
    $designer = newStd(['name' => __('admin.designer'), 'value'=> 'App\Models\User']);
    $product = newStd(['name' => __('admin.product'), 'value'=> 'App\Models\Product']);
    return [$vendor, $designer, $product];
}

function getTypesVariables(): array
{
    $text = newStd(['name' => __('admin.text'), 'value'=> 'text']);
    $number = newStd(['name' => __('admin.number'), 'value'=> 'number']);
    $checkbox = newStd(['name' => __('admin.checkbox'), 'value'=> 'checkbox']);
    $color = newStd(['name' => __('admin.color'), 'value'=> 'color']);
    return [$text, $number, $checkbox, $color];
}

function getRequiredSelect(): array
{
    $yes = newStd(['id' => 'Yes']);
    $no = newStd(['id' => 'NO']);
    return [$yes, $no];
}


function getCurrentLanguageSymbol(){
    return Session::get('applocale');
}

function getStringBetween($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function getCategories($id = null)
{
    if ($id != null)
        return Category::all()->except($id);
    return Category::all();
}

function getParentsCategories($id = null)
{
    if ($id != null)
        return Category::whereNull('parent_category')->get()->except($id);
    return Category::whereNull('parent_category')->get();
}

function getSubCategories()
{
    $categories = Category::query()->whereNotNull('parent_category');
    return $categories->get();
}

function getProfessions()
{
    return Profession::all();
}

function getTags()
{
    return Tag::all();
}

function getStyles()
{
    return Style::query()->where('status', 1)->get();
}

function renderAttributeInput(Attribute $attribute, $oldValue = null)
{
    $name = 'attributes['.$attribute->id.']';
    $title = $attribute->name;
    $decimal = true;

    switch ($attribute->type) {
        case 'text':
            return view('admin.components.text-attribute', compact('title', 'name', 'oldValue'));
        case 'color':
            return view('admin.components.color-attribute', compact('title', 'name', 'oldValue'));
        case 'checkbox':
            return view('admin.components.switch-form-attribute', compact('title', 'name', 'oldValue'));
        case 'number':
            return view('admin.components.number-attribute', compact('title', 'name', 'oldValue', 'decimal'));
        default:
            return '';

    }
}

function getBannerApplicableName($appliesTo): string
{
    switch ($appliesTo){
        case 'AppliesToVendors':
            return 'admin';
        case 'AppliesToDesigners':
            return 'user';
        case 'AppliesToProducts':
            return 'product';
    }
}

function setting($key, $type): string
{
    if ($type == 'rich_text_box')
        $type = 'richTextBox';
    $setting = Setting::where('key', $key);
    if ($setting)
        return $setting->with($type)->get()->first()->{$type}->value;
    else
        return '';
}

function getActiveProducts()
{
    return Product::query()->where('status', 1)->get();
}

