@extends('admin.layouts.app')
@section('title')
    {{ __('lang.Admins') }}
@endsection

@section('content')
    <section id="dashboard-ecommerce">
        <div class="row  match-height">
            @include('admin.components._subNav', [
                'search'=>true,
                'route' => route('admin.admins'),
                'title' => 'admin',
                'filters' => ['active', 'inactive', 'member', 'owner'],
            ])
            @forelse($admins as $admin)
                <div class="col-sm-12 col-md-6 col-lg-3">
                    <div class="card ">
                        <div class="item-img text-center">
                            <a href="{{route('admin.admin',$admin->id)}}">
                            @if(!is_null($admin->cover_image) && file_exists('storage/'.$admin->cover_image))
                                <img class="card-img-top" height="200px" 
                                    src="{{ asset('storage/'.$admin->cover_image) }}">
                            @else
                                View
                            @endif
                        </a>
                        </div>
                        <div class="p-50">
                            <div class="item-wrapper">
                                <h6 class="item-name">
                                    {{ $admin->fullName() }} - {{ $admin->roles()->first()->name ?? '' }}
                                    @if ($admin->status == 1)
                                        <span class="text-success"><i class="bi bi-patch-check"></i></span>
                                    @else
                                        <span class="text-danger"><i class="bi bi-patch-exclamation"></i></span>
                                    @endif
                                </h6>
                                <p class="text-body m-0">
                                    @if(!is_null($admin->center))
                                    <a href="{{ route('admin.center', $admin->center?->id) }}">
                                        <i class="bi bi-box"></i> {{ $admin->center?->name }}
                                    </a> - {{ __('lang.'.$admin->center_position) }} <br>
                                    @endif
                                    <i class="bi bi-envelope"></i> {{ $admin->email }}
                                </p>
                            </div>
                        </div>
                        <div class="item-options p-50">
                            @if (!is_null($admin->member_desc))
                                <p class="text-body m-0">
                                    {{ $admin->member_desc }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <small>{{ __('lang.Not found') }}</small>
            @endforelse
            <div class="">
                {{ $admins->links() }}
            </div>
        </div>

        <div class="modal fade" id="addNewAdmin" tabindex="-1" aria-modal="true" role="dialog" >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-transparent p-0 m-0">
                        <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                    <div class="modal-body px-sm-1 mx-50">
                        <h1 class="text-center mb-1">{{ __('lang.Add New') }}</h1>
                        <form class="row gy-1 gx-2 mt-75 " action="{{ route('admin.admin.store') }}" method="post" >
                            @csrf
                            <div class="col-6">
                                <label class="form-label">{{ __('lang.First name')}}</label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}" class="form-control ">
                                @error('first_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label">{{ __('lang.Last name')}}</label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}" class="form-control ">
                                @error('last_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label">{{ __('lang.Email')}}</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="form-control ">
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label">{{ __('lang.Password')}}</label>
                                <input type="password" name="password" value="{{ old('password') }}" class="form-control ">
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label">{{ __('lang.Role')}}</label>
                                <select name="role" class="form-select ">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role') == $role->id ? 'selected':'' }}>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label">{{ __('lang.Phone')}}</label>
                                <input type="text" name="phone_number" value="{{ old('phone_number') }}" class="form-control ">
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
    </section>
@endsection
