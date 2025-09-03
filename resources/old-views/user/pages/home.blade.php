@extends('user.layouts.app')
@section('title') | Home @endsection
@section('content')
    <div class="container">
        <div class="section-heading text-center">
            <h2 class="h2-p m-0">Featured</h2>
            <p class="m-t-30 m-0">Top Featured Centers</p>
        </div>
        <div class="row">
            @forelse($featured as $center)
                <div class="col-sm-4 margin-topx">
                    <div class="card ">
                        <a href="{{route('user.center',['id'=>$center->id])}}">
                            <div class="image-overlay">
                                @if(!is_null($center->logo) && !empty($center->logo) )
                                    <img src="{{'/storage/'.$center->logo}}" class="card-img hight-img" alt="...">
                                @else
                                    <img src="{{asset('/user/imgs/about/2.png')}}" class="card-img hight-img" alt="...">
                                @endif
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
    <div class="empty"></div>
    
    <div class="container mb-5">
        <div class="section-heading text-center">
            <h2 class="h2-p m-0">Offers</h2>
            <p class="m-t-30 m-0">You may like this offers</p>
        </div>
        <div class="row">
            @forelse($offers as $center)
            <div class="col-sm-4 margin-topx">
                <div class="card ">
                    <a href="{{route('user.center',['id'=>$center->id])}}">
                        <div class="image-overlay">
                            @if(!is_null($center->logo) && !empty($center->logo) )
                                <img src="{{'/storage/'.$center->logo}}" class="card-img hight-img" alt="...">
                            @else
                                <img src="{{asset('/user/imgs/about/2.png')}}" class="card-img hight-img" alt="...">
                            @endif
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
    <div class="empty"></div>
    
    <div class="container">
        <div class="section-heading text-center">
            <h2 class="h2-p m-0">Centers</h2>
            <p class="m-t-30 m-0">Other centers you may like</p>
        </div>
        <div class="row">
            @forelse($centers as $center)
                <div class="col-sm-4 margin-top">
                    <div class="card ">
                        <a href="{{route('user.center',['id'=>$center->id])}}">
                            <div class="image-overlay">
                                @if(!is_null($center->logo) && !empty($center->logo) )
                                    <img src="{{'/storage/'.$center->logo}}" class="card-img hight-img" alt="...">
                                @else
                                    <img src="{{asset('/user/imgs/about/2.png')}}" class="card-img hight-img" alt="...">
                                @endif
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
            <div class="my-2 mx-auto">
                {{$centers->links()}}
            </div>
        </div>
    </div>
    <div class="empty"></div>
@endsection