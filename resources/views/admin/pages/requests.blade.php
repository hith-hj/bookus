@extends('admin.layouts.app')
@section('title') Requests @endsection

@section('content')
    <section id="dashboard-ecommerce">
        <div class="row grid-view match-height">
            @include('admin.components._subNav', [
                'search'=>false,
                'route' => route('admin.requests'),
                // 'title' => 'service',
                'filters' => ['Males', 'Females'],
            ])
            @forelse($requests as $request)
                <div class="col-sm-12 col-md-6 col-lg-4">
                    <div class="card ">
                        <div class="p-50">
                            <div class="item-wrapper">
                                <h6 class="item-name">
                                    {{ $request->name }} - 
                                    <a href="{{ route('admin.center',$request->center?->id) }}">
                                        {{$request->center?->name}}
                                    </a>
                                </h6>
                                <div class="item-rating">
                                    <button class="btn btn-flat-primary waves-effect" data-bs-toggle="modal">
                                        <i class="bi bi-clock"></i> Requester {{ $request->requester }}
                                    </button>
                                    <button class="btn btn-flat-secondery waves-effect" data-bs-toggle="modal">
                                        <i class="bi bi-person"></i> Requested {{ $request->requested }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="item-options p-50">
                            <h6>{{$request->type}}</h6>
                            <p class="text-body">
                                {{json_decode($request->payload)}}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <small>No Request </small>
            @endforelse
            <div class="d-flex justify-content-between">
                <div class="">
                    {{-- {{ $requests->links() }} --}}
                </div>
                <div class="">
                    Showing {{ count($requests) }} result
                </div>
            </div>
        </div>
    </section>
@endsection
