@extends('user.layouts.app')
@section('title') | Login @endsection
@section('content')

<div class="history section-padding py-3 my-3" id="about">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <div class="h1-style section-heading">
                    <h1>Login</h1>
                    <span class="single-title-line"></span>
                </div>
                <div class="history-content">
                    <div class="card p-2">
                        <form action="{{route('user.login')}}" method="POST">
                            @csrf
                            <input type="text" name="email" placeholder="Email" 
                                class="form-control my-4 @error('email') border-danger @enderror" value="{{old('email')}}">
                            @error('email')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                            <input type="password" name="password" placeholder="Password" class="form-control my-4 @error('password') border-danger @enderror">
                            @error('password')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                            <button class="btn btn-outline-primary btn-sm w-100 my-2" href="{{route('about')}}">Login</button>
                        </form>
                    </div>
                    <div class="my-2 ">Don't have account yet <a href="{{route('user.registerPage')}}">Register Here</a></div>
                </div>
            </div>
            <!--/ Column-->
            <div class="col-sm-6">
              <div class="history-img text-center">
                <img class="img-responsive" src="{{asset('user/imgs/about/1.png')}}" alt="" title="">
              </div>
            </div>
        </div>
    </div>
</div>

@endsection
