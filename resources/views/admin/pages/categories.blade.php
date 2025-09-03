@extends('admin.layouts.app')
@section('title')
    Categories
@endsection

@section('content')
    <section id="dashboard-ecommerce">
        <div class="row grid-view match-height">
            @include('admin.components._subNav', [
                'search'=>true,
                'route' => route('admin.categories'),
                'title' => 'category',
                'filters' => ['active', 'inactive'],
            ])
            @forelse($categories as $category)
                <div class="col-sm-12 col-md-6 col-lg-3">
                    <div class="card ">
                        @if(!is_null($category->image) && file_exists('storage/'.$category->image))
                        <div class="item-img text-center">
                            <img class="card-img-top" height="200px"
                                src="{{ asset('storage/'.$category->image) }}" >
                        </div>
                        @endif
                        <div class="p-50">
                            <div class="item-wrapper">
                                <h6 class="item-name">
                                    {{ $category->name }}
                                    @if ($category->status == 1)
                                        <i class="bi bi-check-circle text-success"></i>
                                    @else
                                        <i class="bi bi-x-circle text-danger"></i>
                                    @endif
                                </h6>
                            </div>
                        </div>
                        <div class="item-options text-center p-50">
                            <div class="btn-group" role="group">
                                
                                <button class="btn btn-sm btn-flat-primary waves-effect" data-bs-toggle="modal"
                                    data-bs-target="#category-{{ $category->id }}-centers">
                                    <i class="bi bi-box"></i> {{ __('lang.Centers') }}
                                </button>

                                <button class="btn btn-sm btn-flat-secondary waves-effect waves-float waves-light " type="submit"
                                    form="category-{{ $category->id }}-toggle">
                                        @if ($category->status == 1)
                                            <i class="bi bi-x-circle"></i> OFF
                                        @else
                                            <i class="bi bi-check-circle"></i> ON
                                        @endif
                                </button>
                                <form action="{{ route('admin.toggleCategory', $category->id) }}" method="get"
                                    id="category-{{ $category->id }}-toggle" hidden
                                    onsubmit="
                                        if(confirm('Are you sure?')){this.submit();}else{event.preventDefault();}
                                        ">
                                </form>

                                <button class="btn btn-sm btn-flat-danger waves-effect waves-float waves-light rounded"
                                    form="category-{{ $category->id }}-delete" type="submit">
                                    <i class="bi bi-trash"></i> 
                                </button>
                                <form action="{{ route('admin.category.delete', $category->id) }}" method="post"
                                    id="category-{{ $category->id }}-delete" hidden
                                    onsubmit="
                                        if(confirm('Are you sure you eant to delete this?')){this.submit();}else{event.preventDefault();}
                                        ">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="category-{{ $category->id }}-centers" aria-modal="true" role="dialog">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header bg-transparent p-0 m-0">
                                    <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </div>
                                <div class="modal-body px-sm-1 mx-50">
                                    <h1 class="text-center mb-1">{{ $category->name }} {{ __('lang.Centers') }}</h1>
                                    @forelse ($category->centers as $center)
                                        <a href="{{route('admin.center',$center->id)}}">
                                            <h4 class="item-name text-primary">{{ $center->name }}</h4>
                                        </a>
                                    @empty
                                        <p>{{ __('lang.Not found') }}</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <small>{{ __('lang.Not found') }}</small>
            @endforelse
        </div>
        <div class="modal fade " id="addNewCategory"  aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-transparent p-0 m-0">
                        <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                    <div class="modal-body px-sm-1 mx-50">
                        <h1 class="text-center mb-1" >
                            {{ __('lang.Add New') }}
                        </h1>
                        <form class="row gy-1 gx-2 mt-75 " action="{{ route('admin.category.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="col-12">
                                <label for="">{{ __('lang.Name') }} </label>
                                <input type="text" name="name" class="form-control form-control-sm" required
                                value="{{ old('name') }}">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 ">
                                <label class="form-label" for="category_img">{{ __('lang.Image') }}</label>
                                <input type="file" name="image" id="category_img"  required class="form-control">
                                @error('image')
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
