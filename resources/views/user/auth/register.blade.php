@extends('user.layouts.app')
@section('title') | Register @endsection
@section('content')

<div class="history section-padding py-1" id="about">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
              <div class="history-img text-center">
                <img class="img-responsive" src="{{asset('user/imgs/about/2.png')}}" alt="" title="">
              </div>
            </div>
            <div class="col-sm-6">
                <div class="h1-style section-heading">
                    <h1>Register</h1>
                    <span class="single-title-line"></span>
                </div>
                <div class="history-content">
                    <div class="card p-2">
                        <form action="{{route('user.register')}}" method="POST">
                            @csrf
                            <div class="form-group">
                                <input type="text" name="first_name" placeholder="First Name" 
                                    class="form-control my-1 @error('first_name') border-danger @enderror" value="{{old('first_name')}}">
                                @error('first_name')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <input type="text" name="last_name" placeholder="Last Name" 
                                    class="form-control my-1 @error('last_name') border-danger @enderror" value="{{old('last_name')}}">
                                @error('last_name')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <input type="text" name="phone_number" placeholder="Phone Number" 
                                    class="form-control my-1 @error('phone_number') border-danger @enderror" value="{{old('phone_number')}}">
                                @error('phone_number')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <input type="text" name="email" placeholder="Email" 
                                    class="form-control my-1 @error('email') border-danger @enderror" value="{{old('email')}}">
                                @error('email')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <input type="password" name="password" placeholder="Password" class="form-control my-1 @error('password') border-danger @enderror">
                                @error('password')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <input type="password" name="password_confirmation" placeholder="Confirm Password" class="form-control my-1 @error('password') border-danger @enderror">
                            </div>
                            <button class="btn btn-outline-primary btn-sm w-100 my-2" href="{{route('about')}}">Register</button>
                        </form>
                    </div>
                    <div class="my-2 ">Already have account <a href="{{route('user.loginPage')}}">Login Here</a></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
