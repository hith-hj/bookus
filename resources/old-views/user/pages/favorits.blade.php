@extends('user.layouts.app')
@section('title') | Favorits @endsection
@section('content')
  <div class="container" >
    <div class="category">
      <div class="section-heading text-center">
        <h2 class="h2-p">Favorits</h2>
        <p class="m-t-30">this is a list of your favorite salons</p>
      </div>
    </div>
    <div class="row">
        @forelse($favorits as $center)
        <div class="col-sm-4 margin-top">
          <div class="card  ">
              <div class="image-overlay">
                @if( !is_null($center->logo) && !empty($center->logo) )
                    <img src="/storage/{{$center->logo}}" class="card-img hight-img" alt="...">
                @else 
                    <img src="{{asset('/user/imgs/about/2.png')}}" class="card-img hight-img" alt="...">
                @endif
                <a href="{{route('user.center.toggleFavorite',['id'=>$center->id])}}">
                    <i class="fas fa-heart favorite-icon text-danger"></i>
                </a>
              </div>
              <div class="card-body">
                <a href="{{route('user.center',['id'=>$center->id])}}">
                    <h4 class="card-title">{{$center->name}}</h4>
                </a>
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
            <small>No Favorits for you</small>
        @endforelse
    </div>
  </div>
@endsection