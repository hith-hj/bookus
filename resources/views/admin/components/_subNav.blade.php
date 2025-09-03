<div class="col-sm-12 mb-1">
    <div class="row">
        @if(isset($route) && $search)
            <div class="col-6">
                <form id="search-form" action="{{$route ?? ''}}" method="get" class="m-0">
                    <div class="input-group input-group-merge">
                        <input type="text" name="search" class="form-control form-control-sm search-product" placeholder="{{ __('lang.Search') }}" 
                            title="Search here">
                        <span class="input-group-text" type="submit">
                        <i class="bi bi-search" onclick="document.querySelector('#search-form').submit()"></i></span>
                    </div>
                </form>
            </div>
        @endif
        <div class="col-6">
            <div class="btn-group w-100" role="group">
                
                @if(isset($filters) && count($filters)>0)
                    <button type="button" class="btn btn-icon btn-sm btn-outline-primary waves-effect"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-filter"></i>{{ __('lang.Filters') }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        @foreach ($filters as $filter )
                            <a class="dropdown-item {{request('filter') == $filter ? 'active' : '' }}" onclick="location.search = '?filter={{$filter}}'">
                                {{ucfirst($filter)}}
                            </a>
                        @endforeach
                    </div>
                @endif

                {{-- <button type="button" class="btn btn-icon btn-sm btn-outline-primary waves-effect"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bi bi-list"></i>Items
                </button>
                <div class="dropdown-menu">
                        <a class="dropdown-item {{request('perPage') == 5 ? 'active' : '' }}" onclick="location.search = 'perPage=5'">
                            5
                        </a>
                        <a class="dropdown-item {{request('perPage') == 10 ? 'active' : '' }}" onclick="location.search = 'perPage=10'">
                            10
                        </a>
                        <a class="dropdown-item {{request('perPage') == 25 ? 'active' : '' }}" onclick="location.search = 'perPage=25'">
                            25
                        </a>
                </div> --}}
            
                @if(isset($title))
                    <button class="btn btn-icon btn-sm btn-outline-primary waves-effect" 
                        data-bs-toggle="modal" data-bs-target="#addNew{{$title ?? ''}}">
                        <i class="bi bi-plus fa-lg"></i> {{ __('lang.New') }}  
                    </button>
                @endif
                <button class="btn btn-icon btn-sm btn-outline-primary view-btn grid-view-btn waves-effect"
                    onclick="location.replace('{{ $route ?? route('admin.home') }}')">
                    <i class="fa fa-recycle"></i> {{ __('lang.Reset') }}
                </button>
                <button class="btn btn-icon btn-sm btn-outline-primary view-btn list-view-btn waves-effect"
                    onclick="location.reload()">
                    <i class="fa fa-refresh"></i> {{ __('lang.Reload') }}
                </button>
                <button class="btn btn-icon btn-sm btn-outline-primary view-btn list-view-btn waves-effect"
                    onclick="history.back()">
                    <i class="fa fa-chevron-left"></i> {{ __('lang.Back') }}
                </button>
            </div>
        </div>
        
    </div>
</div>