@extends('user.layouts.app')
@section('title') | Appointments @endsection
@section('content')
  <!-------------  Appointment card ---------------->
  <div class="history section-padding">
    <div class="container">
        <div class="h1-style section-heading">
            <h2>Appointments</h2>
            <span class="single-title-line"></span>
        </div>
        <div class="row">
            <div class="col-6">
              <div class="list-group-item row" aria-current="true">
                @forelse($appointments as $appo)
                <div class="col-12 card mb-3 ">
                  <div class="row g-0">
                    <div class="col-md-4">
                        @if( !is_null($appo->center->logo) && !empty($appo->center->logo) )
                          <img src="/storage/{{$appo->center->logo}}" class="img-appointment rounded-start" alt="...">
                        @else
                          <img src="{{asset('/user/imgs/about/2.png')}}" class="img-appointment rounded-start" alt="...">
                        @endif
                    </div>
                    <div class="col-md-8">
                      <div class="card-body appointment-items p-2">
                        <button class="{{$appo->status == 'completed' ? 'confirmed-button':'canceled-button'}}">
                          <i class="fa {{$appo->status == 'completed' ? 'fa-check-circle':'fa-ban'}} prohibition-sign" aria-hidden="true"><span> {{$appo->status}}</span></i>
                        </button>
                        <h5 class="card-title m-0">{{$appo->center->name}} - Price : {{$appo->total}}</h5>
                        <p class="card-text m-0">
                             Appointment date : {{$appo->appointment_date->format("Y-m-d")}}
                        <br> Appointment time : {{$appo->shift_start}}
                        <br> Appointment duration : {{floor($appo->total_time/60).'hr'.' '.floor($appo->total_time%60).'min' }}</p>
                        <p class="card-text m-0">
                            <small class="text-body-secondary">{{$appo->created_at->diffForHumans()}}</small>
                        </p>
                        <div class="d-inline-flex gap-1 booked-services my-0 py-1">
                            @forelse($appo->appointmentServices as $service)
                                @if($loop->index < 5)
                                    <button type="button" class="btn" disabled data-bs-toggle="button">{{$service->title}}</button>
                                @endif
                            @empty
                                <small>No Services</small>
                            @endforelse
                        </div>
                        <div class="my-1">
                            <button type="button" class="btn btn-sm btn-outline-primary w-100">
                              <i class="fas fa-check"></i> Rebook
                            </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                @empty
                    <small>No Appointments</small>
                @endforelse
              </div>
            </div>
            <div class="col-6">
                <div class="row">
                    @forelse($booked as $appo)
                        <div class="col-12 appointment-detailx card mx-1 p-1">
                            <div class="card card-appointment-body mb-3">
                                @if( !is_null($appo->center->logo) && !empty($appo->center->logo) )
                                    <img src="/storage/{{$appo->center->logo}}" class="appointment-img card-img-top" alt="...">
                                @else
                                    <img src="{{asset('/user/imgs/about/2.png')}}" class="appointment-img card-img-top" alt="...">
                                @endif
                                <div class="card-body p-2">
                                    <h5 class="card-title m-0">{{$appo->center->name}}  <i class="fa fa-check-circle text-info" aria-hidden="true">
                                          <span> {{$appo->status}}</span> </i></h5>
                                    <p class="card-text m-0">
                                        Price : {{$appo->total}} 
                                        <br> Appointment date : {{$appo->appointment_date->format("Y-m-d")}}
                                        <br> Appointment start at : {{$appo->shift_start}}
                                        <br> Appointment duration : {{floor($appo->total_time/60).'hr'.' '.floor($appo->total_time%60).'min' }}
                                    </p>
                                    <p class="card-text"><small class="text-body-secondary">{{$appo->created_at->diffForHumans()}}</small></p>
                                </div>
                                <div class="d-inline-flex gap-1 booked-services my-0 py-0">
                                    @forelse($appo->appointmentServices as $service)
                                        @if($loop->index < 5)
                                            <button type="button" class="btn" disabled data-bs-toggle="button">{{$service->title}}</button>
                                        @endif
                                    @empty
                                        <small>No Services</small>
                                    @endforelse
                                </div>
                                <div class="my-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger w-100">
                                      <i class="fas fa-trash"></i> Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <small>No booked appointment </small>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
  </div>
  <div class="empty"></div>
  <!------------- End Appointment card ---------------->
@endsection