@extends('admin.layouts.app')
@section('title')
    Services
@endsection

@section('content')
    <section id="dashboard-ecommerce">
        <div class="row grid-view match-height">
            @include('admin.components._subNav', [
                'search'=>true,
                'route' => route('admin.services'),
                'title' => 'service',
                'filters' => ['Males', 'Females', 'Everyone'],
            ])
            @forelse($services as $service)
                <div class="col-sm-12 col-md-6 col-lg-4">
                    <div class="card ">
                        <div class="p-50">
                            <div class="item-wrapper">
                                <h6 class="item-name">
                                    {{ $service->name }} 
                                    @if ($service->status == 1)
                                        <i class="bi bi-check-circle text-success"></i>
                                    @else
                                        <i class="bi bi-x-circle text-danger"></i>
                                    @endif
                                </h6>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.center',$service->center?->id)}}">{{$service->center?->name}}</a>
                                    <p class="text-body m-0">
                                        <i class="bi bi-clock"></i> {{ $service->Duration }}
                                    </p>
                                    <p class="text-body m-0">
                                        <i class="bi bi-person"></i> {{ $service->service_gender }}
                                    </p>
                                    <p class="text-body m-0">
                                        <i class="bi bi-currency-dollar"></i> {{ $service->retail_price }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="item-options p-50">
                            <p class="text-body">
                                {{$service->description}}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <small>No Services Yet</small>
            @endforelse
            <div class="d-flex justify-content-between">
                <div class="">
                    {{ $services->links() }}
                </div>
                <div class="">
                    Showing {{ count($services) }} result
                </div>
            </div>
        </div>
        <div class="modal fade " id="addNewService" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-transparent p-0 m-0">
                        <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                    <div class="modal-body px-sm-1 mx-50">
                        <h1 class="text-center mb-1">Add New Service</h1>
                        <form class="row gy-1 gx-2 mt-75 " action="" method="" enctype="multipart/form-data">
                            {{-- @csrf --}}
                            <div class="col-12">
                                <label for="">
                                    Service name
                                </label>
                                <input type="text" name="name" class="form-control form-control-sm">
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
