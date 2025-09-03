@extends('admin.layouts.auth')
@section('title')
    Login
@endsection

@section('content')
    <div class="auth-inner row m-0">
        <!-- Left Text-->
        <div class="d-none d-lg-flex col-lg-8 align-items-center p-5">
            <div class="w-100 d-lg-flex align-items-center justify-content-center px-5">
                <img class="img-fluid" src="{{ asset('admin/login-v2.svg') }}" alt="Login V2">
            </div>
        </div>
        <!-- /Left Text-->
        <!-- Login-->
        <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5">
            <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
                @if (Session::has('error'))
                    <div class="alert alert-danger alert-dismissible fade show m-1" role="alert">
                        <div class="alert-body">
                            {{ Session::get('error') }}
                        </div>
                    </div>
                @endif
                <a class="d-flex justify-content-center align-items-center my-2" href="#">
                    <img src="{{ asset('user/imgs/favicon.png') }}" width="22px" height="21px" alt="" srcset="">
                    <h2 class="brand-text text-primary m-0">Bookus</h2>
                </a>
                <h2 class="card-title fw-bold mb-1">Welcome to 4Bookus! 👋</h2>
                <p class="card-text mb-2">Please sign-in to your account and start</p>
                <form class="auth-login-form" action="{{ route('admin.login') }}" method="POST">
                    @csrf
                    <div class="mb-1">
                        <label for="email" class="form-label">{{ __('Email') }}</label>
                        <input type="text" name="email" tabindex="1" autofocus=""
                            class="form-control @error('email') is-invalid @enderror" id="email"
                            placeholder="{{ __('Email') }}" aria-describedby="email"
                            value="{{ old('email') ?? 'admin@luxury.com' }}" required>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-1">
                        <div class="d-flex justify-content-between">
                            <label class="form-label" for="password">{{ __('Password') }}</label>
                            <a href="">
                                <!--<small>{{ __('Forgot Password') }}</small>-->
                            </a>
                        </div>
                        <div class="input-group input-group-merge form-password-toggle">
                            <input type="password" id="password" name="password" tabindex="2"
                                placeholder="{{ __('Password') }}"aria-describedby="password"
                                class="form-control @error('password') is-invalid @enderror" required value="password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <button types="submit" class="btn btn-primary w-100 waves-effect waves-float waves-light">Sign in</button>
                </form>
            </div>
        </div>
    </div>
@endsection
