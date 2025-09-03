@extends('user.layouts.app')
@section('title') | Profile @endsection
@section('content')
<div class="history section-padding">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="h1-style section-heading">
                        <h2>Profile</h2>
                        <span class="single-title-line"></span>
                    </div>
                    <div class="profile-card">
                            <header class="profile-header">
                                <div class="profile-image-container">
                                @if(!is_null($user->image) )
                                    <img src="/storage/{{$user->image}}" alt="Profile Image" class="profile-image">
                                    <div class="edit-icon">
                                        <i class="fas fa-edit"></i>
                                    </div>
                                @else
                                    <img src="{{asset('/user/imgs/profile.png')}}" alt="Profile Image" class="profile-image">
                                @endif
                                </div>
                            </header>
                        <h2 class="name">{{$user->first_name.' '.$user->last_name}}</h2>
                        <ul class="contact-info">
                            <li><i class="fas fa-phone"></i> <span>{{$user->phone_number}}</span></li>
                            <li><i class="fas fa-envelope"></i> <span>{{$user->email}}</li>
                            <li><i class="fas fa-birthday-cake"></i> <span>{{$user->birth_date ?? 'not set'}}</span></li>
                            <li><i class="fas fa-venus-mars"></i> <span>{{$user->gender ?? 'not set'}}</span></li>
                        </ul>
                    </div>
                </div>
                <!--/ Column-->
                <div class="col-md-8 ">
                    <div class="col appointment-detail">
                        <div class="card card-appointment-body mb-3">
                            <form class="row g-3" method="post" action="{{route('user.profile.edit')}}">
                                @csrf
                                <div class="col-md-6">
                                    <label for="fname" class="form-label">First Name</label>
                                    <input type="text" name="first_name" class="form-control" id="fname" value="{{$user->first_name}}">
                                    @error('first_name')
                                        <small class="text-danger">{{$message}}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="lname" class="form-label">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" id="lname" value="{{$user->last_name}}">
                                    @error('last_name')
                                        <small class="text-danger">{{$message}}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="date" class="form-label">Date of birth</label>
                                    <input type="date" name="birth_date" class="form-control" id="date" value="{{$user->birth_date}}">
                                    @error('birth_date')
                                        <small class="text-danger">{{$message}}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select id="gender" name="gender" class="form-select">
                                        <option value="null" >Choose</option>
                                        <option value="female" {{$user->gender == 'female' ? 'selected':'' }} >Female</option>
                                        <option value="male" {{$user->gender == 'male' ? 'selected':'' }}>Male</option>
                                    </select>
                                    @error('gender')
                                        <small class="text-danger">{{$message}}</small>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="phone" class="form-label">Mobile Number</label>
                                    <input type="tel" class="form-control" id="phone" value="{{$user->phone_number}}" readonly>
                                </div>
                                <div class="col-6">
                                    <label for="email" class="form-label">E-mail</label>
                                    <input type="email" class="form-control" id="email" value="{{$user->email}}" readonly>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-sm w-50">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="empty"></div>
@endsection