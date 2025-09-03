@extends('admin.layouts.app')
@section('title')
    {{ __('lang.Roles') }}
@endsection

@section('content')
    <section id="dashboard-ecommerce">
        <div class="row grid-view match-height">
            @include('admin.components._subNav', [
                'search'=>true,
                'route' => route('admin.roles'),
                'title' => 'role',
            ])
            @forelse($roles as $role)
                <div class="col-sm-12 col-md-6 col-lg-3">
                    <div class="card ">
                        <div class="p-50">
                            <div class="item-wrapper">
                                <h6 class="item-name">
                                    {{ $role->name }}
                                </h6>
                                <div class="item-rating">
                                    <button class="btn btn-flat-primary waves-effect" data-bs-toggle="modal"
                                        data-bs-target="#role-{{ $role->id }}-permissions">
                                        <i class="bi bi-universal-access-circle"></i>
                                        {{ __('lang.Permissions') }} : {{ count($role->permissions) }}
                                    </button>
                                    <button class="btn btn-flat-secondery waves-effect" data-bs-toggle="modal"
                                        data-bs-target="#role-{{ $role->id }}-users">
                                        <i class="bi bi-person"></i> {{ __('lang.Users') }} : {{ count($role->users) }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="item-options text-center p-50">
                            <div class="btn-group" role="group">
                                <button class="btn btn-flat-danger waves-effect waves-float waves-light rounded"
                                    form="role-{{ $role->id }}-delete" type="submit">
                                    <i class="bi bi-trash"></i> {{ __('lang.Delete') }}
                                </button>
                                <form action="{{ route('admin.role.delete',$role->id) }}" method="post" 
                                    id="role-{{ $role->id }}-delete" onsubmit="
                                        if(confirm('Are you sure?')){this.submit();}else{event.preventDefault();}
                                        ">@csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="role-{{ $role->id }}-permissions" tabindex="-1" aria-modal="true"
                    role="dialog">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header bg-transparent p-0 m-0">
                                <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </div>
                            <div class="modal-body px-sm-1 mx-50">
                                <h1 class="text-center mb-1">{{ $role->name }} {{ __('lang.Permissions') }}</h1>
                                @forelse ($role->permissions as $perm)
                                    <p>{{ $perm->name }}</p>
                                @empty
                                    <p>{{ __('Not found') }}</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="role-{{ $role->id }}-users" tabindex="-1" aria-modal="true"
                    role="dialog">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header bg-transparent p-0 m-0">
                                <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </div>
                            <div class="modal-body px-sm-1 mx-50">
                                <h1 class="text-center mb-1">{{ $role->name }} {{ __('lang.Users') }}</h1>
                                @forelse ($role->users as $user)
                                    <p>{{ $user->fullName() }}</p>
                                @empty
                                    <p>{{ __('Not found') }}</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <small>{{ __('Not found') }}</small>
            @endforelse
        </div>

        <div class="modal fade" id="addNewRole" tabindex="-1" aria-modal="true" role="dialog">
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
                            action="{{ route('admin.role.store') }}" method="post">
                            @csrf
                            <div class="col-12">
                                <label for=""> {{ __('lang.Name') }} </label>
                                <input type="text" name="name" class="form-control form-control-sm" value="{{ old('name') }}">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 text-center">
                                <button type="submit"
                                    class="btn btn-primary me-1 mt-1 waves-effect waves-float waves-light">
                                    <i class="bi bi-plus"></i> Create
                                </button>
                                <button type="reset" class="btn btn-outline-secondary mt-1 waves-effect"
                                    data-bs-dismiss="modal" >
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
