@if (app()->getLocale() == 'ar')
    <link rel="stylesheet" href="{{ asset('admin/vendors/css/vendors-rtl.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css-rtl/bootstrap.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css-rtl/bootstrap-extended.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css-rtl/components.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css-rtl/core/menu/menu-types/vertical-menu.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css-rtl/custom-rtl.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css-rtl/colors.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css-rtl/style-rtl.css') }}" />
@else
    <link rel="stylesheet" href="{{ asset('admin/vendors/css/vendors.min.css') }}" media="print" onload="this.media='all'" />
    <link rel="stylesheet" href="{{ asset('admin/css/base/core/menu/menu-types/vertical-menu.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
@endif
@yield('vendor-styles')
<link rel="stylesheet" href="{{ asset('admin/vendors/css/extensions/toastr.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin/css/base/themes/dark-layout.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/css/overrides.css') }}" />

@yield('page-styles')

<style>
    .logo-color{
        color:#3a9bdc !important;
    }
</style>