@extends('user.layouts.app')
@section('title') | {{$center->name ?? 'Center'}} @endsection
@section('content') 
    
    @if(is_null($center))
        <p>Center is not found</p>
    @else
    
    <div class="container salon-header-style">
        <div class="row flex-rowx">
            <div class="col-lg-6 col-sm-12 salon-details">
                <h1 class="mb-1">{{$center->name}}</h1>
                <div class="d-flex justidy-content-between gap-2">
                    <div class="rating">
                        @for($i=0;$i< ceil($center->rated/2) ; $i++)
                          <span class="fa fa-xs fa-star checked"></span>
                        @endfor
                        @for($i=0;$i< 5 - ceil($center->rated/2); $i++)
                          <span class="fa fa-xs fa-star"></span>
                        @endfor
                        <span>({{ceil($center->rated/2)}})</span>
                    </div>
                    <div>
                        @if($center->isOpen)
                          <p style="color: #48dc3a;">Open</p>
                        @else
                          <p style="color: #fd2222;">closed</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-sm-12 salon-details">
                <div class="rating-and-iconsx">
                    <div class="icon-container">
                        @if($center->isFavorite)
                            <a href="{{route('user.center.toggleFavorite',['id'=>$center->id])}}">
                                <i class="fas fa-heart icon-style text-danger"></i>
                            </a>
                        @else
                            <a href="{{route('user.center.toggleFavorite',['id'=>$center->id])}}">
                                <i class="far fa-heart icon-style"></i>
                            </a>
                        @endif
                        <a href="#">
                          <i class="fas fa-share icon-style"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div>
          <p>{{$center->about}}</p>
        </div>
    </div>
    <div class="emptyx"></div>


    @if(!is_null($center->center_images))
    <div class="container">
        <div class="row">
            @forelse($center->center_images as $image)
            <div class="col-lg-4 col-md-6 col-sm-12 img-salon my-2">
                <img src="{{asset("/storage/$image")}}" class="card-img hight-img" alt="...">
            </div>
            @empty
                <small>No images</small>
            @endif
        </div>
    </div>
    <div class="empty"></div>
    @endif

    @if(!is_null($center->categories))
    <div class="images-container">
        <div class="container ">
          <div class="categories">
            <div class="section-heading text-center">
              <h2 class="h2-p">Categories ({{ count($center->categories) }})</h2>
              <span class="single-title-left"></span>
              <span class="single-title-center"></span>
              <span class="single-title-right"></span>
              <p class="m-t-30">Lorem ipsum dolor </p>
            </div>
          </div>
          <div class="row ">
            @forelse($center->categories as $category)
                @if($loop->index < 10)
                    <div class="col img-category ">
                      <div class="row row-images-category card img-category-card">
                        @if( empty($category->image) || is_null($category->image) )
                            <img src="{{asset('/user/imgs/about/2.png')}}" class="card-img-top" style="height: 210px;" alt="">
                        @else
                            <img src="{{asset('/storage/'.$category->image)}}" class="{{$center->main_category == $category->name ? 'image-border':''}} card-img" alt="...">
                        @endif
                        <div class="card-body">
                          <h5 class="card-title {{$center->main_category == $category->name ? 'text-info':''}}">{{$category->name}}</h5>
                        </div>
                      </div>
                    </div>                    
                @endif    
            @empty
                <small>No Categories</small>
            @endforelse
          </div>
        </div>
    </div>
    <div class="empty"></div>
    @endif
    
    @if(!is_null($center->centerServices))
    <div class="container">
        <div class="categories">
            <div class="section-heading text-center">
              <h2 class="h2-p">Services ({{count($center->centerServices)}})</h2>
              <span class="single-title-left"></span>
              <span class="single-title-center"></span>
              <span class="single-title-right"></span>
              <p class="m-t-30">Lorem ipsum </p>
            </div>
        </div>
        <div class="services" style="height:500px;max-height:500px !important;overflow-y:auto;overflow-x:hidden;">
          <ul class="rowx list-group">
            @forelse($center->centerServices as $service)
            <li class=" col-6x list-group-item d-flex justify-content-between align-items-center">
              <div class="">
                <!--<input class="form-check-input me-1" type="checkbox" value="" id="firstCheckbox">-->
                <label class="form-check-label" for="firstCheckbox">{{ $service->name }} - {{ $service->Duration.'min' }}</label>
                <p class="form-check-label service-duration" for="firstCheckbox">{{ $service->description }}</p>
              </div>
              <label class="form-check-label service-price" for="firstCheckbox">{{ $service->retail_price }}</label>
            </li>
            @empty
                <small>No services</small>
            @endforelse
          </ul>
        </div>
        <button class="btn btn-primary w-100 my-4 px-4" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#offcanvasExample" aria-controls="offcanvasExample" {{count($center->centerServices) < 1 ? 'disabled':'' }}>Book</button>
    </div>
    <div class="empty"></div>
    @endif

    @if(!is_null($center->admins) && count($center->admins)>0)
    <section class="container">
        <div class="categories">
            <div class="section-heading text-center">
              <h2 class="h2-p">Team Members ({{count($center->admins)}}) </h2>
              <p class="m-t-30">The team we are proud off</p>
            </div>
        </div>
        <div class="row active-with-click">
            @forelse($center->admins as $admin)
          <div class="col-lg-3 col-sm-6 col-xs-12">
            <div class="material-card Blue">
              <h2>{{$admin->first_name}}</h2>
              <div class="mc-content">
                <div class="img-container">
                @if(!is_null($admin->cover_image) && !empty($admin->cover_image) )
                  <img class="img-responsive img-membership" src='{{asset("/storage/$admin->cover_image")}}'>
                @else
                  <img class="img-responsive img-membership" src='{{asset("/user/imgs/about/1.png")}}'>
                @endif
                </div>
                <div class="mc-description">{{$admin->member_desc}}</div>
              </div>
            </div>
          </div>
          @empty
            <small>No Members</small>
          @endforelse
        </div>
    </section>
    <div class="empty"></div>
    @endif

    @if(!is_null($center->days) && count($center->days)>0)
    <div class="container">
        <div class="categories">
          <div class="section-heading text-center">
            <h2 class="h2-p">Opening Days ({{count($center->days)}})</h2>
            <p class="m-t-30">Lorem ipsum dolor sit amet</p>
          </div>
        </div>
        <div class="row row-days days">
            @forelse($center->days as $day)
                <button type="button" class="btn btn-secondary" data-bs-container="body" data-bs-toggle="popover" data-bs-placement="bottom"
                    data-bs-content="{{date_format(date_create($day->open),"H:i")}} - {{date_format(date_create($day->close),"H:i")}}">
                    {{$day->day}} 
                    <span class="day-time">{{date_format(date_create($day->open),"H:i")}} - {{date_format(date_create($day->close),"H:i")}}</span>
                </button>
            @empty
                <small>No days</small>
            @endforelse
        </div>
    </div>
    <div class="empty"></div>
    @endif
    
    <!-----------Gifts ---------->
    @if(!is_null($center->cGifts) && count($center->cGifts)>0)
    <div class="container">
        <div class="categories">
          <div class="section-heading text-center">
            <h2 class="h2-p">Gifts ({{count($center->gifts)}})</h2>
            <p class="m-t-30">Lorem gifts</p>
          </div>
        </div>
        <div class="row">
            @forelse($center->cGifts as $gift)
                <div class="col-6 col-md-4 text-over-shape">
                    <h2 class="text gift-text">{{$gift->value}} / Value</h2>
                    <h5 class="text duration-text">{{$gift->duration}} / Duration</h5>
                    <div class="shape"></div>
                    <img src="{{asset('/user/imgs/gift1.svg')}}" class="rectangle-image" alt="Descriptive text">
                </div>
            @empty
                <small>No gifts</small>
            @endforelse
        </div>
    </div>
    <div class="empty"></div>
    @endif
    
    <!-----------Memberships ---------->
    @if(!is_null($center->cMemberships) && count($center->cMemberships)>0)
    <div class="container">
        <div class="categories">
          <div class="section-heading text-center">
            <h2 class="h2-p">Memberships ({{count($center->memberships)}})</h2>
            <p class="m-t-30">Lorem memberships</p>
          </div>
        </div>
        <div class="row">
            @forelse($center->cMemberships as $membership)
                <div class="col-12 col-md-4 text-over-shape">
                    <h5 class="text gift-text">{{$membership->name}}</h5>
                    <h5 class="text duration-text">{{$membership->duration}} Duration / {{$membership->session}} Sessions</h5>
                    <div class="shape-membership"></div>
                    <img src="{{asset('/user/imgs/membership2.svg')}}" class="rectangle-image" alt="Descriptive text">
                </div>
            @empty
                <small>No memberships</small>
            @endforelse
        </div>
    </div>
    <div class="empty"></div>
    @endif
    
    <!----------- reviews section ----------->
    @if( !is_null($center->reviews) && count($center->reviews)>0 )
    <div class="container">
        <div class="section-heading text-center">
          <h2 class="h2-p">Reviews ({{$center->number_reviews}})</h2>
          <p class="m-t-30"></p>
        </div>
        <div class="row">
            @forelse($center->review as $review)
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="card border-primary mb-3">
                        <div class="card-header">
                            @if( empty($review->user->image) )
                                <img class="client-name-pic" src="{{asset('/user/imgs/about/1.png')}}" alt="">
                            @else
                                <img class="client-name-pic" src="{{asset('/storage/'.$review->user->image)}}" alt="">
                            @endif
                            <span class="client-name">{{$review->user->first_name}}</span>
                        </div>
                        <div class="card-body text-primary">
                            <h5 class="card-title">rate {{ $review->rate}}</h5>
                            <p class="card-text">{{$review->review}}</p>
                        </div>
                        <div class="card-footer">{{$review->created_at->diffForHumans()}}</div>
                    </div>
                </div>
            @empty
                <small>No reviews</small>
            @endforelse
        </div>
    </div>
    <div class="empty"></div>
    @endif
    
    <!----------- Resources ---------->
    @if( !is_null($center->resources) && count($center->resources)>0 )
    <div class="container ">
    <div class="categories">
      <div class="section-heading text-center">
        <h2 class="h2-p">Resources ({{count($center->resources)}})</h2>
        <span class="single-title-left"></span>
        <span class="single-title-center"></span>
        <span class="single-title-right"></span>
        <p class="m-t-30">Lorem ipsum dolor sit amet</p>
      </div>
    </div>
    <div class="row row-resources">
      <button class="arrow right-arrow"><i class="fa fa-arrow-left"></i> </button>
      @forelse($center->resources as $resource)
      <div class="col active">
        <div class="card" style="width: 18rem;">
            @if( empty($resource->image) || is_null($resource->image) )
                <img src="{{asset('/user/imgs/about/1.png')}}" class="card-img-top" style="height: 210px;" alt="">
            @else
                <img src="{{'/storage/'.$resource->image}}" class="card-img-top" style="height: 210px;" alt="...">
            @endif
          <div class="card-body">
            <h5 class="card-title">{{$resource->title}}</h5>
            <p class="card-text">{{$resource->description}}</p>
          </div>
        </div>
      </div>
      @empty
        <small>No resources</small>
        @endforelse
      <button class="arrow right-arrow">
        <i class="fa fa-arrow-right"></i>
      </button>
    </div>
    </div>
    <div class="empty"></div>
    @endif

    <!----------- Branches ---------->
    @if( !is_null($center->branches) && count($center->branches)>0)
    <div class="container ">
    <div class="categories">
      <div class="section-heading text-center">
        <h2 class="h2-p">Branches ({{count($center->branches)}})</h2>
        <span class="single-title-left"></span>
        <span class="single-title-center"></span>
        <span class="single-title-right"></span>
        <p class="m-t-30">Our Branches</p>
      </div>
    </div>
        <div class="row">
            @forelse($center->branches as $branch)
                <div class="col-sm-4 margin-top">
                    <div class="card">
                        <div class="image-overlay">
                            @if(empty($branch->logo) || is_null($branch->logo) )
                                <img src="{{asset('/user/imgs/about/2.png')}}" class="card-img hight-img" alt="...">
                            @else
                                <img src="{{asset('/storage/center_logo/'.$branch->logo)}}" class="card-img hight-img" alt="...">
                            @endif
                        </div>
                        <div class="card-body">
                            <h4 class="card-title">{{$branch->name}}</h4>
                            <div class="rating">
                                @for($i=0;$i< ceil($branch->rated/2) ; $i++)
                                  <span class="fa fa-xs fa-star checked"></span>
                                @endfor
                                @for($i=0;$i< 5 - ceil($branch->rated/2); $i++)
                                  <span class="fa fa-xs fa-star"></span>
                                @endfor
                                <span>({{ceil($branch->rated/2)}})</span>
                            </div>
                            <span class="badge text-bg-secondary">{{$branch->main_category}}</span>
                        </div>
                    </div>
                </div>
            @empty
                <small>No Branches</small>
            @endforelse
        </div>
    </div>
    <div class="empty"></div>
    @endif

    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
        <div class="offcanvas-header">
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
          <div class="container my-5">
            <h2 class="mb-4">Select a method</h2>
            <div class="list-group">
              <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                <i class="fa fa-calendar-check"></i> Online booking
              </a>
              <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                <i class="fa fa-gift"></i>Gift cards
              </a>
              <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                <i class="fa fa-id-card"></i> Membership cards
              </a>
            </div>
            <button type="button" class="btn btn-primary btn-block mt-4" data-bs-toggle="offcanvas"
              data-bs-target="#offcanvasExample1" aria-controls="offcanvasExample1">Continue</button>
          </div>
        </div>
    </div>
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample1" aria-labelledby="offcanvasExampleLabel">
        <div class="offcanvas-header">
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
          <div class="container my-5">
            <h2 class="mb-4">Select a method</h2>
            <div class="list-group">
              <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                <i class="fa fa-calendar-check"></i> Online booking
              </a>
            </div>
            <button type="button" class="btn btn-primary btn-block mt-4" data-bs-toggle="offcanvas"
              data-bs-target="#offcanvasExample1" aria-controls="offcanvasExample1">Continue</button>
          </div>
        </div>
      </div>
    @endif
@endsection