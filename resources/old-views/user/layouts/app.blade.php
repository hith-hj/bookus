<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>4bookus @yield('title')</title>
  <link rel="shortcut icon" href="{{asset('user/imgs/favicon.png')}}" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="{{asset('user/css/style.css')}}">
</head>

<body>
	@include('user.partials.navbar')
    @if(Session::has('error'))
        <div class="alert alert-danger p-1 mx-auto w-50">{{Session::get('error')}}</div>
    @endif
    @if(Session::has('success'))
        <div class="alert alert-success p-1 mx-auto w-50">{{Session::get('success')}}</div>
    @endif
 	@yield('content')

	@include('user.partials.footer')
	<script src="{{ asset('user/js/popper.min.js') }}"></script>
    <script src="{{ asset('user/js/jquery-3.6.3.min.js') }}"></script>
    <script src="{{ asset('user/js/bootstrap.min.js') }}"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Your other scripts -->
	<script src="{{ asset('user/js/popover.js') }}"></script>
</body>
</html>
