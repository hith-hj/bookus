@extends('admin.layouts.app')
@section('title')
    Charts
@endsection
@section('vendor-styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
@endsection
@section('page-styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/base/plugins/forms/pickers/form-flat-pickr.css') }}">
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="card">
            <div
                class="card-header d-flex justify-content-between align-items-sm-center align-items-start flex-sm-row flex-column">
                <h4 class="card-title">
                    <i class="bi bi-calendar-week"></i> {{ __('lang.Appointments') }}
                </h4>
                <div class="header-right d-flex align-items-center mt-sm-0 mt-1 gap-1">
                  <button type="button" class="btn btn-icon btn-sm btn-outline-primary waves-effect"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-calendar-month"></i>
                    </button>
                    <div class="dropdown-menu">
                        @for($i=1;$i<=12;$i++)
                            @if( DateTime::createFromFormat('F',date('F', mktime(0, 0, 0, $i, 1)) )  < DateTime::createFromFormat('F',date('F') ) )
                                <a class="dropdown-item {{request('month') == $i ? 'active' : '' }}" 
                                onclick="location.search = 'month={{ $i }}' ">
                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }} 
                                </a>
                            @endif
                        @endfor
                    </div>
                    <button type="button" class="btn btn-icon btn-sm btn-outline-primary waves-effect"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-filter"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item {{request('status') == 'completed' ? 'active' : '' }}" 
                        onclick="location.search = 'status=completed'">completed </a>
                        <a class="dropdown-item {{request('status') == 'booked' ? 'active' : '' }}" 
                        onclick="location.search = 'status=booked'">booked </a>
                        <a class="dropdown-item {{request('status') == 'cancelled' ? 'active' : '' }}" 
                        onclick="location.search = 'status=cancelled'">cancelled </a>
                    </div>
                    <button class="btn btn-icon btn-sm btn-outline-primary view-btn grid-view-btn waves-effect"
                    onclick="location.replace('{{ route('admin.charts') }}')">
                        <i class="fa fa-recycle"></i> 
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="AppoCtx" style="max-height:400px !important"></canvas>
                @if (count($appointments) < 1)
                    <small>No Appointments</small>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-md-12">
        <div class="card">
            <div
                class="card-header d-flex justify-content-between align-items-sm-center align-items-start flex-sm-row flex-column">
                <div class="header-left">
                    <h4 class="card-title">
                       <i class="bi bi-people"></i> {{ __('lang.Users') }}
                    </h4>
                </div>
                <div class="header-right d-flex align-items-center mt-sm-0 mt-1">
                    <i class="bi bi-calendar"></i>
                    <label class="form-control border-0 bg-transparent " >XX
                    </label>
                </div>
            </div>
            <div class="card-body">
                <canvas id="UserCtx" style="max-height:400px !important"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
    <script src="{{ asset('admin/vendors/js/charts/chart.min.js') }}"></script>
@endsection
@section('page-script')
    <script>
        const AppoCtx = document.getElementById('AppoCtx');
        const UserCtx = document.getElementById('UserCtx');
        
        new Chart(AppoCtx, {
            type: 'bar',
            data: {
                @if(isset($appointments) && count($appointments)>0 )
                labels: @json($appointments['labels']),
                datasets: [
                    {
                        backgroundColor: '#257fdb',
                        barThickness:15,
                        label:@json($appointments['type']) ,
                        data: @json($appointments['data'])
                    },
                    
                ]
                @endif
            },
            options: {
                responsive: true,
                scales: {
                  x: {
                    beginAtZero: true,
                    stacked: true,
                  },
                  y: {
                    beginAtZero: true,
                    stacked: true,
                  }
                }
            }
        });

        new Chart(UserCtx, {
            type: 'bar',
            data: {
                @if(isset($users['labels'],$users['data'] ) )      
                labels: @json($users['labels']),
                datasets: [
                    {
                        backgroundColor: '#9ef345',
                        barThickness: 10,
                        minBarLength: 1,
                        label: 'users',
                        data:  @json($users['data']) ,
                    }
                ]
                @endif
            }
        });
    </script>
@endsection
