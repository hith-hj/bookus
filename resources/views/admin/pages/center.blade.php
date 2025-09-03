@extends('admin.layouts.app')
@section('title'){{$center->name ?? 'Center'}} @endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between w-100">
                    <div class="">
                        <h4 class="m-0">
                            {{$center->name}}
                            @if($center->status == 1)
                                <span class="text-success"><i class="bi bi-patch-check"></i></span>
                            @else
                                <span class="text-danger"><i class="bi bi-patch-exclamation"></i></span>
                            @endif
                        </h4>
                    </div>
                    <div class="btn-group dropdown-icon-wrapper btn-share">
                        <button type="button" class="btn p-0 " data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <button class="dropdown-item w-100" 
                                data-bs-toggle="modal" data-bs-target="#editCenterInfo">
                                <i class="bi bi-pencil-square"></i> {{ __('lang.Edit') }}
                            </button>
                            @if($center->is_fullFilled && $center->status == 0)
                                <a href="#" class="dropdown-item">
                                    <i class="bi bi-pencil-square"></i> {{ __('lang.Verify') }}
                                </a>
                            @endif
                            @if($center->status == 1 && $center->is_fullFilled)
                                <button class="dropdown-item w-100" form="center-{{ $center->id }}-block">
                                    <i class="bi bi-ban text-danger"></i> {{ __('lang.Block') }}
                                </button>
                                <form id="center-{{ $center->id }}-block" method="post"
                                     action="{{ route('admin.center.block',$center->id) }}" hidden
                                     onsubmit="if(confirm('Are you sure?')){this.submit();}else{event.preventDefault();}">
                                    @csrf
                                </form>
                            @endif
                            @if($center->status == -1 && $center->is_fullFilled)
                                <button class="dropdown-item w-100" form="center-{{ $center->id }}-unblock">
                                    <i class="bi bi-check-circle text-success"></i> {{ __('lang.Un Block') }}
                                </button>
                                <form id="center-{{ $center->id }}-unblock" method="post"
                                     action="{{ route('admin.center.unblock',$center->id) }}" hidden
                                     onsubmit="if(confirm('Are you sure?')){this.submit();}else{event.preventDefault();}">
                                    @csrf
                                </form>
                            @endif
                            <button class="dropdown-item w-100" form="center-{{ $center->id }}-delete">
                                <i class="bi bi-trash text-danger"></i> {{ __('lang.Delete') }}
                            </button>
                            <form id="center-{{ $center->id }}-delete" method="post"
                                 action="{{ route('admin.center.delete',$center->id) }}" hidden
                                 onsubmit="if(confirm('Are you sure?')){this.submit();}else{event.preventDefault();}">@csrf
                            </form>
                        </div>
                    </div>
                </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-md-2 d-flex align-items-start justify-content-center mb-2 mb-md-0 p-0">
                @if(!is_null($center->logo) && file_exists('storage/'.$center->logo))
                <div class="d-flex align-items-center justify-content-center h-100">
                    <img src="{{asset('storage/'.$center->logo)}}" class="img-fluid product-img w-100 h-100" alt="product image">
                </div>
                @endif
            </div>
            <div class="col-12 col-md-10">        
                @if(!is_null($center->main_category))    
                    <span class="card-text item-company">{{ __('lang.Main Category') }}
                        <a href="#" class="company-name">{{$center->main_category}}</a>
                    </span>
                @endif
                <div class="ecommerce-details-price d-flex flex-wrap">
                    <h4 class="item-price me-1">
                        @if($center->isOpen)
                            <span class="text-success">{{ __('lang.Open') }}</span>
                        @else    
                            <span class="text-danger">{{ __('lang.Closed') }}</span>
                        @endif
                    </h4>
                    <h4 class="item-price me-1 ps-1 border-start">
                        @if($center->is_fullFilled)
                            <span class="text-success">{{ __('lang.Fullfilled') }}</span>
                        @else    
                            <span class="text-danger">{{ __('lang.Not Fullfilled') }}</span>
                        @endif
                    </h4>
                    <ul class="unstyled-list list-inline ps-1 border-start">
                        @for ($i = 0; $i < ceil($center->rated / 2); $i++)
                            <span class="ratings-list-item bi bi-star-fill fa-xs  text-primary checked"></span>
                        @endfor
                        @for ($i = 0; $i < 5 - ceil($center->rated / 2); $i++)
                            <span class="ratings-list-item bi bi-star fa-xs"></span>
                        @endfor
                        <span class="ratings-list-item">({{ ceil($center->rated / 2) }})</span>
                    </ul>                   
                </div>
                <p class="card-text">
                    {{$center->about}}
                </p>
                <ul class="product-features list-unstyled d-flex gap-1">
                    <li>
                        <i class="bi bi-envelope"></i>
                        <span>{{$center->email}}</span>
                    </li>
                    @if(!is_null($center->currency))
                    <li>
                        <i class="bi bi-currency-exchange"></i>
                        <span>{{$center->currency}}</span>
                    </li>
                    @endif
                    @if(!is_null($center->phone_number))
                        <li>
                            <i class="bi bi-phone"></i>
                            <span>{{$center->phone_number ?? ''}}</span>
                        </li>
                    @endif
                    @if(!is_null($center->address))
                        <li>
                            <i class="bi bi-map"></i>
                            <span>{{$center->address ?? ''}}</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    
    <div id="accordionWrapa1" role="tablist" aria-multiselectable="true">
        <div class="card mb-0">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="bi bi-intersect"></i> {{ __('lang.Sections') }}
                </h4>
            </div>
            <div class="card-body">
                <div class="accordion" id="center-{{$center->id}}-sections" data-toggle-hover="true">
                    @if(!is_null($center->center_images))
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#center-{{$center->id}}-images" aria-expanded="false" aria-controls="center-{{$center->id}}-images">
                                <i class="bi bi-"></i> {{ __('lang.Images') }}
                            </button>
                        </h2>
                        <div id="center-{{$center->id}}-images" class="accordion-collapse collapse" data-bs-parent="#center-{{$center->id}}-sections">
                            <div class="accordion-body">
                                <ul class="list-unstyled mb-0">
                                    @forelse($center->center_images as  $image)
                                        <li class="d-inline-block selected">
                                            <div class="color-option b-primary">
                                                <img src="{{asset('storage/'.$image)}}" width="150px" height="150px" alt="">
                                            </div>
                                        </li>
                                    @empty
                                        <small>{{ __('lang.Not found') }}</small>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if(!is_null($center->cetegories))
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#center-{{$center->id}}-categories" aria-expanded="false" aria-controls="center-{{$center->id}}-categories">
                                <i class="bi bi-"></i>{{ __('lang.Categories') }}
                            </button>
                        </h2>
                        <div id="center-{{$center->id}}-categories" class="accordion-collapse collapse" data-bs-parent="#center-{{$center->id}}-sections">
                            <div class="accordion-body">
                                <ul class="list-unstyled mb-0">
                                    @forelse($center->categories as  $category)
                                        <li class="d-inline-block text-center mx-2">
                                            <div class="color-option b-primary">
                                                <img src="{{asset('storage/'.$category->image)}}" class="rounded" width="50px" height="50px" alt="">
                                            </div>
                                            {{$category->name}}
                                        </li>
                                    @empty
                                        <small>{{ __('lang.Not found') }}</small>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if(!is_null($center->admins))
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#center-{{$center->id}}-members" aria-expanded="false" aria-controls="center-{{$center->id}}-members">
                                <i class="bi bi-"></i>{{ __('lang.Members') }}
                            </button>
                        </h2>
                        <div id="center-{{$center->id}}-members" class="accordion-collapse collapse"data-bs-parent="#center-{{$center->id}}-sections">
                            <div class="accordion-body">
                                <ul class="list-unstyled mb-0">
                                    @forelse($center->admins as $admin)
                                        <li class="d-inline-block text-center mx-1">
                                            <div class="color-option b-primary">
                                                <img src="{{asset('storage/'.$admin->cover_image)}}" class="rounded" width="50px" height="50px" alt="">
                                            </div>
                                            <a href="{{route('admin.admin',$admin->id)}}">
                                                {{$admin->first_name}}
                                            </a>
                                            @if($admin->is_admin)
                                                - {{$admin->center_position}}
                                            @endif
                                        </li>
                                    @empty
                                        <small>{{ __('lang.Not found') }}</small>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if(!is_null($center->services))
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#center-{{$center->id}}-services" aria-expanded="false" aria-controls="center-{{$center->id}}-services">
                                <i class="bi bi-"></i>{{ __('lang.Services') }}
                            </button>
                        </h2>
                        <div id="center-{{$center->id}}-services" class="accordion-collapse collapse"data-bs-parent="#center-{{$center->id}}-sections">
                            <div class="accordion-body">
                                <div class="row">
                                    @forelse($center->services as $service)
                                        <div class="col-3">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">
                                                        {{$service->name}}
                                                    </h4>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">
                                                        {{ Str::limit($service->description,40)}}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <small>{{ __('lang.Not found') }}</small>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if(!is_null($center->days))
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#center-{{$center->id}}-days" aria-expanded="false" aria-controls="center-{{$center->id}}-days">
                                <i class="bi bi-"></i>{{ __('lang.Days') }}
                            </button>
                        </h2>
                        <div id="center-{{$center->id}}-days" class="accordion-collapse collapse"data-bs-parent="#center-{{$center->id}}-sections">
                            <div class="accordion-body">
                                <div class="row">
                                    @forelse($center->days as $day)
                                        <div class="col-3">
                                            <div class="card m-0 p-1">
                                                <div class="card-header p-0">
                                                    <h4 class="card-title">
                                                        {{$day->day}}
                                                    </h4>
                                                </div>
                                                <div class="card-body p-0">
                                                    <p class="card-text">
                                                        {{ $day->open->format("H:i").' - '. $day->close->format("H:i")}}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <small>{{ __('lang.Not found') }}</small>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if(!is_null($center->contacts))
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#center-{{$center->id}}-contacts" aria-expanded="false" aria-controls="center-{{$center->id}}-contacts">
                                <i class="bi bi-"></i>{{ __('lang.Contacts') }}
                            </button>
                        </h2>
                        <div id="center-{{$center->id}}-contacts" class="accordion-collapse collapse"data-bs-parent="#center-{{$center->id}}-sections">
                            <div class="accordion-body">
                                <div class="row">
                                    @forelse($center->contacts as $contact)
                                        <div class="col-6">
                                            <div class="card m-0 p-1">
                                                <div class="card-header p-0">
                                                    <h4 class="card-title">
                                                        {{$contact->key}}
                                                    </h4>
                                                </div>
                                                <div class="card-body p-0">
                                                    <a href="{{ $contact->value }}" target="_blank" class="card-text">
                                                        {{ $contact->value }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <small>{{ __('lang.Not found') }}</small>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if(!is_null($center->reviews))
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#center-{{$center->id}}-reviews" aria-expanded="false" >
                                <i class="bi bi-"></i>{{ __('lang.Reviews') }}
                            </button>
                        </h2>
                        <div id="center-{{$center->id}}-reviews" class="accordion-collapse collapse"data-bs-parent="#center-{{$center->id}}-sections">
                            <div class="accordion-body">
                                <div class="row">
                                    @forelse($center->reviews as $review)
                                        <div class="col-6">
                                            <div class="card m-0 p-1">
                                                <div class="card-header p-0">
                                                    <a href="{{ route('admin.client',$review->id) }}">
                                                        <h4 class="card-title">
                                                            {{$review->fullName()}}
                                                        </h4>
                                                    </a>
                                                </div>
                                                <div class="card-body p-0">
                                                    <p class="card-text">
                                                        {{ $review->pivot->review}}
                                                        {{ $review->pivot->rate}}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <small>{{ __('lang.Not found') }}</small>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if(!is_null($center->branches))
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#center-{{$center->id}}-branches" aria-expanded="false" >
                                <i class="bi bi-"></i>{{ __('lang.Branches') }}
                            </button>
                        </h2>
                        <div id="center-{{$center->id}}-branches" class="accordion-collapse collapse"data-bs-parent="#center-{{$center->id}}-sections">
                            <div class="accordion-body">
                                <div class="row">
                                    @forelse($center->branches as $branch)
                                        <div class="col-6">
                                            <div class="card m-0 p-1">
                                                <div class="card-header p-0">
                                                    <a href="{{ route('admin.center',$branch->id) }}">
                                                        <h4 class="card-title">
                                                            {{$branch->name}}
                                                        </h4>
                                                    </a>
                                                </div>
                                                <div class="card-body p-0">
                                                    <p class="card-text">
                                                        {{ $branch->email}}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <small>{{ __('lang.Not found') }}</small>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if(!is_null($center->settings()))
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#center-{{$center->id}}-settings" aria-expanded="false" >
                                <i class="bi bi-"></i>{{ __('lang.Settings') }}
                            </button>
                        </h2>
                        <div id="center-{{$center->id}}-settings" class="accordion-collapse collapse"data-bs-parent="#center-{{$center->id}}-sections">
                            <div class="accordion-body">
                                <div class="row">
                                    @forelse($center->settings() as $setting)
                                        <div class="col-6">
                                            <div class="card m-0 p-1">
                                                <div class="card-header p-0">
                                                        <h4 class="card-title">
                                                            {{$setting->key}}
                                                        </h4>
                                                </div>
                                                <div class="card-body p-0">
                                                    <p class="card-text">
                                                        {{ $setting->value}}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <small>{{ __('lang.Not found') }}</small>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title"> <i class="bi bi-calendar-week"></i> {{ __('lang.Appointments') }}</h4>
        </div>
        <div class="card-body row">
            @forelse($center->appointments as $appo)
                <div class="col-sm-12 col-md-6 col-lg-4">
                    <div class="card ">
                        <div class="p-50">
                            <div class="item-wrapper d-flex align-items-center gap-1">
                                @if(!is_null($appo->user))
                                <a href="{{ route('admin.client',$appo->user?->id) }}">
                                    <h6 class="item-name m-0">
                                        <i class="bi bi-person"></i> {{ $appo->user?->fullName() }}
                                    </h6>
                                </a>
                                @else
                                    <h6 class="item-name">Deleted User</h6>
                                @endif
                                
                                <p class="text-body m-0"> {{ $appo->using_by }} 
                                </p>
                            </div>
                        </div>
                        <div class="item-options p-50">
                            <div class="d-flex flex-wrap gap-1">                                    
                                <p class="text-body m-0">
                                    <i class="bi bi-currency-dollar"></i> {{ $appo->total }} 
                                </p>
                                <p class="text-body m-0">
                                    <i class="bi bi-clock"></i> {{ $appo->total_time }}
                                </p>
                                <p class="text-body">
                                    <i class="bi bi-journal"></i> {{ $appo->using_by }} 
                                </p>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="text-body m-0">
                                    <i class="bi bi-calendar"></i> 
                                    {{ $appo->appointment_date->diffForHumans() }}
                                </p>
                                <p class="m-0 {{ $appo->status == 'booked' ? 'text-primary' : 
                                    ($appo->status == 'completed' ? 'text-success':'text-danger') }}">    
                                    {{ __('lang.'.$appo->status) }}
                                </p>
                                <button class="btn btn-flat-primary waves-effect" data-bs-toggle="modal"
                                    data-bs-target="#appo-{{ $appo->id }}-services">
                                     {{ __('lang.Services') }} 
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="appo-{{ $appo->id }}-services" tabindex="-1" aria-modal="true" role="dialog">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header bg-transparent p-0 m-0">
                                <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </div>
                            <div class="modal-body px-sm-1 mx-50">
                                <h1 class="text-center mb-1">{{ __('lang.Services') }}</h1>
                                @forelse ($appo->appointmentServices as $service)
                                    <p>{{ $service->title }}</p>
                                @empty
                                    <p>{{ __('Not found') }}</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-sm-12 px-2">
                    <small>{{ __('lang.Not found') }}</small>
                </div>
            @endforelse
        </div>
    </div>
    <div class="modal fade" id="editCenterInfo" tabindex="-1" aria-modal="true" role="dialog" >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-transparent p-0 m-0">
                        <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                    <div class="modal-body px-sm-1 mx-50">
                        <h1 class="text-center mb-1">{{ __('lang.Edit') }}</h1>
                        <form class="row gy-1 gx-2 mt-75 " action="{{ route('admin.center.edit',$center->id) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="col-6">
                                <label class="form-label">{{ __('lang.Name')}}</label>
                                <input type="text" name="name" value="{{ $center->name }}" class="form-control " required>
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label">{{ __('lang.Email')}}</label>
                                <input type="email" name="email" value="{{ $center->email }}" class="form-control " required>
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label">{{ __('lang.Phone')}}</label>
                                <input type="text" name="phone_number" value="{{ $center->phone_number }}" class="form-control " required>
                                @error('phone_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label">{{ __('lang.Admin')}}</label>
                                <select name="admin" class="form-select " required>
                                    <option value="{{ $center->admin()?->id }}">{{ $center->admin()?->fullName() }}</option>
                                    @foreach($admins as $admin)
                                        <option value="{{ $admin->id }}" >
                                            {{ $admin->fullName() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('admin')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('lang.About')}}</label>
                                <textarea name="about" class="form-control">{{ $center->about }}</textarea>
                                @error('about')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6 ">
                                <label class="form-label">{{ __('lang.Logo')}}</label>
                                <input type="file" name="logo"  class="form-control" >
                                @error('logo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6 ">
                                <label class="form-label">{{ __('lang.Images')}}</label>
                                <input type="file" name="images[]" multiple max="5"  class="form-control" >
                                @error('images')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 text-center">
                                <button type="submit"
                                    class="btn btn-primary me-1 mt-1 waves-effect waves-float waves-light">
                                    <i class="bi bi-edit"></i> {{ __('lang.Edit') }}
                                </button>
                                <button type="reset" class="btn btn-outline-secondary mt-1 waves-effect"
                                    data-bs-dismiss="modal" aria-label="Close">
                                    <i class="bi bi-x"></i>{{ __('lang.Cancel') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection
