@extends('user.layouts.app')
@section('title')| About @endsection
@section('content')
  <!------------- Header Section ---------------->
  <div class="history section-padding">
    <div class="container">
      <div class="row">
        <div class="col-sm-6">
          <div class="h1-style section-heading">
            <h1>About Us</h1>
            <span class="single-title-line"></span>
          </div>
          <div class="history-content">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam venenatis est tortor, eu condimentum ante
              mollis eget. Quisque hendrerit efficitur ante. Proin purus augue, tristique nec sapien ac, blandit
              tristique nulla. Aliquam erat volutpat. Cras enim nunc, tincidunt nec fermentum ut, porta vel leo. Aliquam
              id commodo odio. Donec vitae augue ac ligula pulvinar semper.</p>
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
  <div class="empty"></div>
  <!------------- End Header Section ---------------->

  <!------------ our vision section ----------------->
  <div class="history section-padding">
    <div class="container">
      <div class="row">
        <div class="col-sm-6">
          <div class="history-img text-center">
            <img class="img-responsive" src="{{asset('user/imgs/about/2.png')}}" alt="" title="">
          </div>
        </div>
        <div class="col-sm-6">
          <div class="h1-style section-heading">
            <h1>Our Vision</h1>
            <span class="single-title-line"></span>
          </div>
          <div class="history-content">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam venenatis est tortor, eu condimentum ante
              mollis eget. Quisque hendrerit efficitur ante. Proin purus augue, tristique nec sapien ac, blandit
              tristique nulla. Aliquam erat volutpat. Cras enim nunc, tincidunt nec fermentum ut, porta vel leo. Aliquam
              id commodo odio. Donec vitae augue ac ligula pulvinar semper.</p>
          </div>
        </div>
        <!--/ Column-->
      </div>
    </div>
  </div>
  <div class="empty"></div>
  <!------------ End vision section ------------>

  <!----------- mission section ----------->
  <div class="history section-padding">
    <div class="container">
      <div class="row">
        <div class="col-sm-6">
          <div class="h1-style section-heading">
            <h1>Our Mission</h1>
            <span class="single-title-line"></span>
          </div>
          <div class="history-content">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam venenatis est tortor, eu condimentum ante
              mollis eget. Quisque hendrerit efficitur ante. Proin purus augue, tristique nec sapien ac, blandit
              tristique nulla. Aliquam erat volutpat. Cras enim nunc, tincidunt nec fermentum ut, porta vel leo. Aliquam
              id commodo odio. Donec vitae augue ac ligula pulvinar semper. Suspendisse fringilla placerat velit et
              fermentum. Vivamus aliquet mi et risus interdum, a aliquam velit hendrerit. Nullam mattis turpis diam, vel
              suscipit lacus cursus id.</p>
          </div>
        </div>
        <!--/ Column-->
        <div class="col-sm-6">
          <div class="history-img text-center">
            <img class="img-responsive" src="{{asset('user/imgs/about/3.png')}}" alt="" title="">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="empty"></div>
  <!-----------End Admin section ----------->
@endsection