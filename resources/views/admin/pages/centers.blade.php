@extends('admin.layouts.app')
@section('title')
    {{ __('lang.Centers') }}
@endsection
@section('content')
    <section id="dashboard-ecommerce">
        <div class="row match-height">
            @include('admin.components._subNav', [
                'search'=>true,
                'route' => route('admin.centers'),
                'title' => 'center',
                'filters' => ['lowest', 'highest', 'verified', 'unverified'],
            ])
            @forelse($centers as $center)
                <div class="col-sm-12 col-md-6 col-lg-3">
                    <div class="card ">
                        <a href="{{ route('admin.center', ['id' => $center->id]) }}">
                            <div class="item-img text-center">
                                @if(!is_null($center->logo) && file_exists('storage/'.$center->logo))
                                    <img class="card-img-top" height="200px" 
                                            src="{{ asset('storage/'.$center->logo) }}">
                                @else
                                    View
                                @endif  
                            </div>
                        </a>
                        
                        <div class="p-50">
                            <div class="item-wrapper">
                                <p class="text-body m-0">
                                    {{ $center->name }}
                                    @if ($center->status == 1)
                                        <span class="text-success"><i class="bi bi-patch-check"></i></span>
                                    @else
                                        <span class="text-danger"><i class="bi bi-patch-exclamation"></i></span>
                                    @endif
                                </p>
                                <div class="item-rating d-flex gap-1">
                                    @if ($center->isOpen)
                                        <span class="text-success">{{ __('lang.Open') }}</span>
                                    @else
                                        <span class="text-danger">{{ __('lang.Closed') }}</span>
                                    @endif
                                    <ul class="unstyled-list list-inline m-0">
                                        @for ($i = 0; $i < ceil($center->rated / 2); $i++)
                                            <span class="ratings-list-item bi bi-star-fill text-primary checked"></span>
                                        @endfor
                                        @for ($i = 0; $i < 5 - ceil($center->rated / 2); $i++)
                                            <span class="ratings-list-item bi bi-star"></span>
                                        @endfor
                                        <span class="ratings-list-item">({{ ceil($center->rated / 2) }})</span>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <small>{{ __('lang.Not found') }}</small>
            @endforelse
            <div class="d-flex justify-content-between">
                <div>
                    {{ $centers->links() }}
                </div>
                <div class="">
                    <small>{{ count($centers) }} {{ __('lang.Results') }}</small>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addNewCenter" tabindex="-1" aria-modal="true" role="dialog" >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-transparent p-0 m-0">
                        <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                    <div class="modal-body px-sm-1 mx-50">
                        <h1 class="text-center mb-1">{{ __('lang.Add New') }}</h1>
                        <form class="row gy-1 gx-2 mt-75 " action="{{ route('admin.center.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="col-6">
                                <label class="form-label">{{ __('lang.Name') }}</label>
                                <input type="text" name="name" value="{{ old('name') }}" class="form-control " required>
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label">{{ __('lang.Email') }}</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="form-control " required>
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label">{{ __('lang.Phone') }}</label>
                                <input type="text" name="phone_number" value="{{ old('phone_number') }}" class="form-control " required>
                                @error('phone_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label">{{ __('lang.Admin') }}</label>
                                <select name="admin" class="form-select " required>
                                    <option value="">{{ __('lang.Choose') }}</option>
                                    @foreach($admins as $admin)
                                        <option value="{{ $admin->id }}">{{ $admin->fullName() }}</option>
                                    @endforeach
                                </select>
                                @error('admin')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('lang.About') }}</label>
                                <textarea name="about" class="form-control">{{ old('about') }}</textarea>
                                @error('about')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6 ">
                                <label class="form-label">{{ __('lang.Logo') }}</label>
                                <input type="file" name="logo"  class="form-control">
                                @error('logo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6 ">
                                <label class="form-label">{{ __('lang.Images') }}</label>
                                <input type="file" name="images[]" multiple max="5"  class="form-control">
                                @error('images')
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
