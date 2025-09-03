<!DOCTYPE html>
<html class="loading dark-layout"lang="@if (session()->has('locale')) {{ session()->get('locale') }} @endif"
    data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ env('APP_NAME', 'APP') }} | Dashboard">
    <meta name="keywords" content="{{ env('APP_NAME', 'APP') }} | Dashboard">
    <meta name="author" content="Darbi">
    <title>{{ env('APP_NAME', 'APP') }} | @yield('title')</title>
    <link rel="apple-touch-icon" href="{{ asset('user/imgs/favicon.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('user/imgs/favicon.png') }}">


    {{-- Include core + vendor Styles --}}
    @include('admin.layouts.styles')
    <link rel="stylesheet" href="{{ asset('admin/css/base/pages/authentication.css') }}">

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern"data-open="click">
    <!-- BEGIN: Content-->
    <div class="app-content">
        <!-- BEGIN: Header-->
        <div class="content-wrapper ">
            {{-- Include Breadcrumb --}}
            <div class="content-body ">
                {{-- Include Page Content --}}
                <div class="auth-wrapper auth-cover">
                    @yield('content')
                </div>
            </div>
        </div>

    </div>
    <!-- End: Content-->


</body>

</html>
