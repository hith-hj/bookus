@extends('admin.layouts.app')
@section('title') {{__('lang.Settings')}} @endsection

@section('content')
    <section id="dashboard-ecommerce">
        <div class="row grid-view match-height">
            @include('admin.components._subNav', [
                'search'=>false,
                'route' => route('admin.settings'),
                // 'title' => 'setting',
            ])
            <form action="{{ route('admin.settings.update') }}" method="post">
            	@csrf
            	<div class="row">
            		<div class="col-sm-12 col-md-6 col-lg-3 my-1">
            			<label class="from-label">{{ __('lang.Name') }}</label>
            			<input class="form-control" type="text" name="name" value="{{ old('name') ??  $settings->name }}">
            		</div>
                    <div class="col-sm-12 col-md-6 col-lg-3 my-1">
                        <label class="from-label">{{ __('lang.Email') }}</label>
                        <input class="form-control" type="email" name="email" value="{{ old('email') ??  $settings->email }}">
                    </div>
            		<div class="col-sm-12 col-md-6 col-lg-3 my-1">
            			<label class="from-label">{{ __('lang.Phone') }}</label>
            			<input class="form-control" type="tel" name="phone" value="{{ old('phone') ??  $settings->phone }}">
            		</div>
                    <div class="col-sm-12 col-md-6 col-lg-3 my-1">
                        <label class="from-label">{{ __('lang.Location') }}</label>
                        <input class="form-control" type="text" name="location" value="{{ old('location') ??  $settings->location}}">
                    </div>
            		<div class="col-sm-12 col-md-6 col-lg-3 my-1">
            			<label class="from-label">{{ __('lang.Landline') }}</label>
            			<input class="form-control" type="tel" name="landline" value="{{ old('landline') ??  $settings->landline }}">
            		</div>
                    <div class="col-sm-12 col-md-6 col-lg-3 my-1">
                        <label class="from-label">{{ __('lang.Whatsapp') }}</label>
                        <input class="form-control" type="text" inputmode="numeric" name="whatsapp" value="{{ old('whatsapp') ??  $settings->whatsapp }}">
                    </div>
            		<div class="col-sm-12 col-md-6 col-lg-3 my-1">
            			<label class="from-label">{{ __('lang.Duration') }}</label>
            			<input class="form-control" type="text" inputmode="numeric" name="duration" value="{{ old('duration') ??  $settings->duration }}">
            		</div>
            		<div class="col-sm-12 col-md-6 col-lg-3 my-1">
            			<label class="from-label">{{ __('lang.Max price') }}</label>
            			<input class="form-control" type="text" inputmode="numeric" name="max_price" value="{{ old('max_price') ??  $settings->max_price }}">
            		</div>
            		{{-- <div class="col-sm-12 col-md-6 col-lg-3 my-1">
            			<label class="from-label">{{ __('lang.Open') }}</label>
            			<input class="form-control" type="time" step="3600" name="open" value="{{ old('open') ??  $settings->open }}">
            		</div>
            		<div class="col-sm-12 col-md-6 col-lg-3 my-1">
            			<label class="from-label">{{ __('lang.Close') }}</label>
            			<input class="form-control" type="time" step="3600" name="close" value="{{ old('close') ??  $settings->close }}">
            		</div> --}}
            		<div class="col-sm-12 col-md-6 col-lg-3 my-1">
            			<label class="from-label">{{ __('lang.Tax') }}</label>
            			<input class="form-control" type="text" inputmode="numeric" name="tax" value="{{ old('tax') ??  $settings->tax }}">
            		</div>
            		<div class="col-sm-12 col-md-6 col-lg-3 my-1">
            			<label class="from-label">{{ __('lang.Currency') }}</label>
            			<input class="form-control" type="text" name="currency" value="{{ old('currency') ??  $settings->currency }}">
            		</div>
                    <div class="col-sm-12 col-md-6 col-lg-3 my-1">
                        <label class="from-label">{{ __('lang.Version') }}</label>
                        <input class="form-control" type="text" inputmode="numeric" name="version" value="{{ old('version') ??  $settings->version }}">
                    </div>
            		<div class="col-12 py-2">
            			<button class="btn btn-primary waves-effect waves-float waves-light" type="submit">
                            <i class="bi bi-pencil-square"></i> {{ __('lang.Edit') }}
                        </button>
            		</div>
            	</div>
            </form>
        </div>

    </section>
@endsection
