@extends('user.layouts.app')
@section('title') | Home @endsection

@section('content')

    @include('user.partials.header')

 <!----------- Features --------------->
    <div class="container">
        <div class="section-heading text-center">
            <h2 class="h2-p">Featured</h2>
            <span class="single-title-left"></span>
            <span class="single-title-center"></span>
            <span class="single-title-right"></span>
            <p class="m-t-30">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam venenatis est tortor, eu
              condimentum ante mollis eget.</p>
        </div>
        <div class="row">
            @forelse($featured as $center)
                <div class="col-sm-4 margin-top">
                    <div class="card ">
                      <a href="{{route('center').'?id='.$center->id}}">
                        <div class="image-overlay">
                          <img src="{{'storage/'.$center->logo}}" class="card-img hight-img" alt="...">
                        </div>
                      </a>
                      <div class="card-body">
                        <h4 class="card-title mb-1">{{$center->name}}</h4>
                        <div class="rating">
                            @for($i=0;$i< ceil($center->rated/2) ; $i++)
                              <span class="fa fa-xs fa-star checked"></span>
                            @endfor
                            @for($i=0;$i< 5 - ceil($center->rated/2); $i++)
                              <span class="fa fa-xs fa-star"></span>
                            @endfor
                            <span>({{ceil($center->rated/2)}})</span>
                        </div>
                        <span class="badge text-bg-secondary">{{$center->main_category}}</span>
                      </div>
                    </div>
                </div>
            @empty
                <small>No center Yet</small>
            @endforelse
        </div>
    </div>
  <!--<nav aria-label="Page navigation example ">-->
  <!--  <ul class="pagination pagination-style">-->
  <!--    <li class="page-item">-->
  <!--      <a class="page-link" href="#" aria-label="Previous">-->
  <!--        <span aria-hidden="true">&laquo;</span>-->
  <!--      </a>-->
  <!--    </li>-->
  <!--    <li class="page-item"><a class="page-link" href="#">1</a></li>-->
  <!--    <li class="page-item"><a class="page-link" href="#">2</a></li>-->
  <!--    <li class="page-item"><a class="page-link" href="#">3</a></li>-->
  <!--    <li class="page-item">-->
  <!--      <a class="page-link" href="#" aria-label="Next">-->
  <!--        <span aria-hidden="true">&raquo;</span>-->
  <!--      </a>-->
  <!--    </li>-->
  <!--  </ul>-->
  <!--</nav>-->
  <div class="empty"></div>
  <!----------- End Features --------------->


  <!----------------- Offers ------------------->
    <div class="container mb-5">
        <div class="section-heading text-center">
            <h2 class="h2-p">Offers</h2>
            <span class="single-title-left"></span>
            <span class="single-title-center"></span>
            <span class="single-title-right"></span>
            <p class="m-t-30">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam venenatis est tortor, eu
              condimentum ante mollis eget.</p>
        </div>
        <div class="row">
            @forelse($offers as $center)
            <div class="col-sm-4 margin-top">
                <div class="card ">
                    <a href="{{route('center').'?id='.$center->id}}">
                        <div class="image-overlay">
                            <img src="{{'storage/'.$center->logo}}" class="card-img hight-img" alt="...">
                          </div>
                    </a>
                  <span class="add-layer"></span>
                  <div class="offer-badge">Offer</div>
                  <div class="card-body">
                    <h4 class="card-title mb-1">{{$center->name}}</h4>
                    <div class="rating">
                        @for($i=0;$i< ceil($center->rated/2) ; $i++)
                          <span class="fa fa-xs fa-star checked"></span>
                        @endfor
                        @for($i=0;$i< 5 - ceil($center->rated/2); $i++)
                          <span class="fa fa-xs fa-star"></span>
                        @endfor
                      <span>({{ceil($center->rated/2)}})</span>
                    </div>
                    <span class="badge text-bg-secondary">{{$center->main_category}}</span>
                  </div>
                </div>
            </div>
            @empty 
                <small>No Offer yet</small>
            @endforelse
        </div>
    </div>
  <!--<nav aria-label="Page navigation example ">-->
  <!--  <ul class="pagination pagination-style">-->
  <!--    <li class="page-item">-->
  <!--      <a class="page-link" href="#" aria-label="Previous">-->
  <!--        <span aria-hidden="true">&laquo;</span>-->
  <!--      </a>-->
  <!--    </li>-->
  <!--    <li class="page-item"><a class="page-link" href="#">1</a></li>-->
  <!--    <li class="page-item"><a class="page-link" href="#">2</a></li>-->
  <!--    <li class="page-item"><a class="page-link" href="#">3</a></li>-->
  <!--    <li class="page-item">-->
  <!--      <a class="page-link" href="#" aria-label="Next">-->
  <!--        <span aria-hidden="true">&raquo;</span>-->
  <!--      </a>-->
  <!--    </li>-->
  <!--  </ul>-->
  <!--</nav>-->
  <div class="empty"></div>
  <!----------------- End Offers ------------------->

    <div class="history section-padding" id="about">
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
                      <a class="btn btn-outline-primary btn-sm w-50" href="{{route('about')}}">More About Us</a>
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

    <div class="container my-5" id="download_app">
        <div class="history section-padding">
            <div class="container">
              <div class="row">
                <div class="col-sm-6">
                  <div class="history-img text-center">
                    <img class="img-responsive" src="{{asset('user/imgs/home/application.png')}}" alt="" title="">
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="h1-style section-heading">
                    <h1>Try 4BookUs Application</h1>
                    <span class="single-title-line"></span>
                  </div>
                  <div class="history-content">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam venenatis est tortor, eu condimentum ante
                      mollis eget. Quisque hendrerit efficitur ante. Proin purus augue, tristique nec sapien ac, blandit
                      tristique nulla. Aliquam erat volutpat. Cras enim nunc, tincidunt nec fermentum ut, porta vel leo. Aliquam
                      id commodo odio. Donec vitae augue ac ligula pulvinar semper.</p>
                    <button class="btn btn-outline-primary btn-sm w-50" type="submit">Download</button>
                  </div>
                </div>
                <!--/ Column-->
              </div>
            </div>
        </div>
    </div>
    <div class="empty"></div>

@endsection