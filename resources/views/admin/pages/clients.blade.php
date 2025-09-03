@extends('admin.layouts.app')
@section('title')
    Clients
@endsection

@section('content')
    <section id="dashboard-ecommerce">
        <div class="row  match-height">
            @include('admin.components._subNav', [
                'search'=>true,
                'route' => route('admin.clients'),
                'title' => 'client',
                'filters' => ['male', 'female'],
            ])
            @forelse($clients as $client)
                <div class="col-sm-12 col-md-6 col-lg-3">
                    <div class="card ">
                        <a href="{{route('admin.client',$client->id)}}">
                            <div class="item-img text-center">
                                @if(!is_null($client->image) && file_exists('storage/'.$client->image))
                                    <img class="card-img-top" height="200px" 
                                        src="{{ asset('storage/'.$client->image) }}">
                                @else
                                    View
                                @endif
                            </div>
                        </a>
                        <div class="p-50">
                            <div class="item-wrapper">
                                <h6 class="item-name">
                                    {{ $client->fullName() }}
                                    @if ($client->is_verified == 1)
                                        <span class="text-success"><i class="bi bi-patch-check"></i></span>
                                    @else
                                        <span class="text-danger"><i class="bi bi-patch-exclamation"></i></span>
                                    @endif
                                </h6>
                                <p class="text-body m-0">
                                    <i class="bi bi-envelope"></i> {{ $client->email }} <br>
                                    <i class="bi bi-phone"></i> {{ $client->phone_number }}
                                </p>
                            </div>
                        </div>
                        <div class="item-options p-50">
                            @if (!is_null($client->member_desc))
                                <p class="text-body m-0">
                                    {{ $client->member_desc }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <small>No Clients Yet</small>
            @endforelse
            <div class="">
                {{ $clients->links() }}
            </div>
        </div>

        <div class="modal fade" id="addNewClient" tabindex="-1" aria-modal="true" role="dialog" >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-transparent p-0 m-0">
                    <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </div>
                <div class="modal-body px-sm-1 mx-50">
                    <h1 class="text-center mb-1">{{ __('lang.Add New') }}</h1>
                    <form class="row gy-1 gx-2 mt-75 " 
                        action="{{ route('admin.client.store') }}" method="post" 
                        enctype="multipart/form-data">
                        @csrf
                        <div class="col-6">
                            <label class="form-label">First name</label>
                            <input type="text" name="first_name" value="{{  old('first_name') }}" class="form-control ">
                            @error('first_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label">Last name</label>
                            <input type="text" name="last_name" value="{{  old('last_name') }}" class="form-control ">
                            @error('last_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{  old('email') }}" class="form-control ">
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone_number" value="{{  old('phone_number') }}" class="form-control ">
                            @error('phone_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control ">
                            @error('password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label">Gender</label>
                            <select  name="gender" class="form-select ">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                            @error('gender')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label">Birth Date</label>
                            <input type="date"  name="birth_date" class="form-control">
                            @error('gender')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label">Image</label>
                            <input type="file"  name="image" class="form-control">
                            @error('image')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
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
