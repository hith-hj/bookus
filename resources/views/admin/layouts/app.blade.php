<html class="loading" data-textdirection="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Include core + vendor Styles --}}
    @include('admin.layouts.styles')
</head>

<body
    class="vertical-layout vertical-menu-modern navbar-floating footer-static default menu-expanded"
    data-open="click" data-menu="vertical-menu-modern"
    data-col="default" data-framework="laravel"
    data-asset-path="{{ asset('/') }}">


    <!-- BEGIN: Header-->
    @include('admin.layouts.navbar')

    <!-- END: Header-->

    <!-- BEGIN: Main Menu-->
    @include('admin.layouts.menu_aside')
        
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <!-- BEGIN: Header-->
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>       
        <div class="content-wrapper container-xxl p-0">
            <div class="content-body">
                @yield('content')
            </div>
        </div>

    </div>
    <!-- End: Content-->
    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    {{-- include footer --}}
    @include('admin.layouts.footer')

    {{-- include default scripts --}}
    @include('admin.layouts.scripts')

    <script type="text/javascript">
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        })
    </script>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $key => $error)
                    <script>
                        toastr['error']({!! json_encode($error) !!}, {
                            closeButton: true,
                            tapToDismiss: false,
                            timeOut: 5,
                        });
                    </script>
                @endforeach
            </ul>
        </div>
    @endif
    @if (Session::has('error'))
        <script>
            toastr['error']({!! json_encode(Session::get('error')) !!}, {
                closeButton: true,
                tapToDismiss: false,
                timeOut: 5,
            });
        </script>
    @endif
    @if (Session::has('success'))
        <script>
            toastr['success']({!! json_encode(Session::get('success')) !!}, {
                closeButton: true,
                tapToDismiss: false,
                timeOut: 5,
            });
        </script>
    @endif
    <script>
        window.onload = ()=>{ 
            let theme = localStorage.getItem('4bookus-theme');
            if(theme == null){
                setTheme('dark-layout')
            }else{
                setTheme(theme);
            }
        };
        function toggleTheme(){
            let oldTheme = localStorage.getItem('4bookus-theme');
            let theme = 'light';
            if(oldTheme == 'light'){
                theme = 'dark-layout';
            }
            localStorage.setItem('4bookus-theme',theme);
            setTheme(theme);
        }
        function setTheme(theme){
            let menu = document.querySelector('#menu_aside');
            let icon = document.querySelector('#theme_icon');
            localStorage.setItem('4bookus-theme',theme);
            document.querySelector('html').setAttribute('class',`loaded ${theme}`);
            if(theme == 'light'){
                menu.classList.add('menu-light');
                menu.classList.remove('menu-dark');
                icon.setAttribute('class','bi bi-moon fa-lg')
            }else{
                menu.classList.add('menu-dark')
                menu.classList.remove('menu-light')
                icon.setAttribute('class','bi bi-sun fa-lg')
            }
        }
    </script>

</body>

</html>

