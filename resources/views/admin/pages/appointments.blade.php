@extends('admin.layouts.app')
@section('title')
    Appointments
@endsection

@section('content')
    <section id="dashboard-ecommerce">
        <div class="row  match-height">
            @include('admin.components._subNav', [
                'search'=>false,
                'route' => route('admin.appointments'),
                'title' => 'appointment',
                'filters' => ['booked','completed','cancelled','online','gift','membership'],
            ])
            @forelse($appointments as $appo)
                <div class="col-sm-12 col-md-6 col-lg-4">
                    <div class="card ">
                        <div class="p-50">
                            <div class="item-wrapper d-flex align-items-center gap-1">
                                @if(!is_null($appo->user))
                                <a href="{{ route('admin.client',$appo->user->id) }}">
                                    <h6 class="item-name m-0">
                                        <i class="bi bi-person"></i> {{ $appo->user->fullName() }}
                                    </h6>
                                </a>
                                @else
                                    <h6 class="item-name">Deleted User</h6>
                                @endif
                                
                                @if(!is_null($appo->center) )
                                <p class="text-body m-0">
                                    <a href="{{ route('admin.center', $appo->center->id) }}">
                                        {{ $appo->center?->name }}
                                    </a>
                                </p>
                                @else
                                    <h6 class="item-name">Deleted Center</h6>
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
                <div class="modal fade" id="appo-{{ $appo->id }}-services" tabindex="-1" aria-modal="true"
                    role="dialog">
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
                <small>{{ __('lang.Not found') }}</small>
            @endforelse
            <div class="">
                {{ $appointments->links() }}
            </div>
        </div>
        <div class="modal fade" id="addNewAppointment" tabindex="-1" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-transparent p-0 m-0">
                        <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                    <div class="modal-body px-sm-1 mx-50">
                        <h1 class="text-center mb-1">{{ __('lang.Add New') }}</h1>
                        <form class="row gy-1 gx-2 mt-75 " action="" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="col-12">
                                <label for="">
                                    {{ __('lang.Name') }}
                                </label>
                                <input type="text" name="name" class="form-control form-control-sm">
                            </div>
                            <div class="col-12 text-center">
                                <button type="submit"
                                    class="btn btn-primary me-1 mt-1 waves-effect waves-float waves-light">
                                    <i class="bi bi-plus"></i> Create
                                </button>
                                <button type="reset" class="btn btn-outline-secondary mt-1 waves-effect"
                                    data-bs-dismiss="modal" aria-label="Close">
                                    <i class="bi bi-x"></i>Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
