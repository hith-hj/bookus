@extends('admin.layouts.app')
@section('title'){{$admin->fullName() ?? 'Admin'}} @endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row my-1">
            @if(!is_null($admin->cover_image) && file_exists('storage/'.$admin->cover_image))
            <div class="col-12 col-md-2 d-flex align-items-center justify-content-center mb-2 mb-md-0 p-0">
                <div class="d-flex align-items-center justify-content-center h-100">
                    <img src="{{asset('storage/'.$admin->cover_image)}}" class="img-fluid product-img w-100 h-100" alt="product image">
                </div>
            </div>
            @endif
            <div class="col-12 col-md-10">
                <div class="d-flex justify-content-between">
                    <div class="">
                        <h4 class="m-0">
                            {{$admin->fullName()}} - {{ $admin->roles()->first()->name ?? '' }}
                            @if($admin->status == 1)
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
                                data-bs-toggle="modal" data-bs-target="#editAdminInfo">
                                <i class="bi bi-pencil-square"></i> {{ __('lang.Edit') }}
                            </button>
                            <button class="dropdown-item w-100" form="admin-{{ $admin->id }}-delete">
                                <i class="bi bi-trash text-danger"></i> {{ __('lang.Delete') }}
                            </button>
                            <form id="admin-{{ $admin->id }}-delete" method="post" 
                                action="{{ route('admin.admin.delete',$admin->id) }}" hidden
                                onsubmit="if(confirm('Are you sure')){this.submit();}else{event.preventDefault();}">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
                @if(!is_null($admin->center))
                    <div class="ecommerce-details-price d-flex flex-wrap gap-1 mb-1">
                        <span class="card-text item-company"> 
                            <a href="{{route('admin.center',$admin->center?->id)}}" class="company-name">
                                {{$admin->center?->name}}
                            </a>
                        </span>
                        <span class="item-price me-1">
                            {{ __('lang.'.$admin->center_position) }}
                        </span>                   
                    </div>
                @endif
                @if(!is_null($admin->member_desc))
                    <p class="card-text">
                        <i class="bi bi-info"></i> {{$admin->member_desc}}
                    </p>
                @endif
                <ul class="product-features list-unstyled d-flex gap-1">
                    <li>
                        <i class="bi bi-envelope"></i>
                        <span>{{$admin->email}}</span>
                    </li>
                    @if(!is_null($admin->phone_number))
                        <li>
                            <i class="bi bi-phone"></i>
                            <span>{{$admin->phone_number ?? ''}}</span>
                        </li>
                    @endif
                    @if(!is_null($admin->address))
                        <li>
                            <i class="bi bi-map"></i>
                            <span>{{$admin->address ?? ''}}</span>
                        </li>
                    @endif
                </ul>
                {{-- <hr>
                <div class="d-flex flex-column flex-sm-row pt-1">
                    <a href="#" class="btn btn-primary btn-cart me-0 me-sm-1 mb-1 mb-sm-0 waves-effect waves-float waves-light">
                        <i class="bi bi-check"></i>
                        <span class="add-to-cart">Verify</span>
                    </a>
                </div> --}}
            </div>
        </div>
    </div>
    
    <div id="accordionWrapa1" role="tablist" aria-multiselectable="true">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="bi bi-intersect"></i> {{ __('lang.Sections') }}
                </h4>
            </div>
            <div class="card-body">
                <div class="accordion" id="admin-{{$admin->id}}-sections" data-toggle-hover="true">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            @if($admin->center_posisiont != 'owner' && !$admin->is_admin)
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#admin-{{$admin->id}}-appointments" aria-expanded="false" >
                                <i class="bi bi-"></i> {{ __('lang.Appointments') }}
                            </button>
                            @endif
                        </h2>
                        <div id="admin-{{$admin->id}}-appointments" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#admin-{{$admin->id}}-sections" style="">
                            <div class="accordion-body">
                                <div class="row">
                                    @forelse($admin->appointments as  $appo)
                                    <div class="col-sm-12 col-md-6 col-lg-4 item-options p-50">
                                        <p class="text-body m-0">
                                            @switch($appo->status)
                                                @case('booked')
                                                    <div class="badge text-secondary">
                                                        <i class="bi bi-box"></i> {{ $appo->status }}
                                                    </div>
                                                    @break
                                                @case('completed')
                                                    <div class="badge text-success">
                                                        <i class="bi bi-check"></i> {{ $appo->status }}
                                                    </div>
                                                    @break
                                                @case('cancelled')
                                                    <div class="badge text-danger">
                                                        <i class="bi bi-ban"></i> {{ __('lang.'.$appo->status) }}
                                                    </div>
                                                    @break                                    
                                                @default                                            
                                            @endswitch
                                        </p>
                                        <div class="d-flex flex-wrap gap-1">
                                            <p class="text-body m-0">
                                                <i class="bi bi-clock"></i> {{ $appo->total_time }}
                                            </p>
                                            <p class="text-body m-0">
                                                <i class="bi bi-currency-dollar"></i> {{ $appo->total }} 
                                            </p>
                                            <p class="text-body m-0">
                                                <i class="bi bi-journal"></i> {{ $appo->using_by }} 
                                            </p>
                                            <p class="text-body m-0">
                                                <i class="bi bi-calendar"></i> {{ $appo->appointment_date->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                    @empty
                                        <small>{{ __('lang.Not Found') }}</small>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editAdminInfo" tabindex="-1" aria-modal="true" role="dialog" >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-transparent p-0 m-0">
                    <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </div>
                <div class="modal-body px-sm-1 mx-50">
                    <h1 class="text-center mb-1">{{ __('lang.Edit') }} {{ $admin->fullName() }}</h1>
                    <form class="row gy-1 gx-2 mt-75 " 
                        action="{{ route('admin.admin.edit',$admin->id) }}" method="post" >
                        @csrf
                        <div class="col-6">
                            <label class="form-label">{{ __('lang.First name')}}</label>
                            <input type="text" name="first_name" value="{{  old('first_name') ?? $admin->first_name }}" class="form-control ">
                            @error('first_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ __('lang.Last name')}}</label>
                            <input type="text" name="last_name" value="{{  old('last_name') ?? $admin->last_name }}" class="form-control ">
                            @error('last_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ __('lang.Email')}}</label>
                            <input type="email" name="email" value="{{  old('email') ?? $admin->email }}" class="form-control ">
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ __('lang.About')}}</label>
                            <input type="text" name="member_desc" value="{{  old('member_desc') ?? $admin->member_desc }}" class="form-control ">
                            @error('member_desc')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ __('lang.Role')}}</label>
                            <select name="role" class="form-select ">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" 
                                        {{ $admin->roles()?->first()?->id == $role->id ? 'selected':'' }} >
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ __('lang.Phone')}}</label>
                            <input type="text" name="phone_number" value="{{  old('phone_number') ?? $admin->phone_number }}" class="form-control ">
                            @error('phone_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="col-12 text-center">
                            <button type="submit"
                                class="btn btn-primary me-1 mt-1 waves-effect waves-float waves-light">
                                <i class="bi bi-plus"></i> {{ __('lang.Create') }}
                            </button>
                            <button type="reset" class="btn btn-outline-secondary mt-1 waves-effect"
                                data-bs-dismiss="modal" aria-label="Close">
                                <i class="bi bi-x"></i> {{ __('lang.Cancel') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
