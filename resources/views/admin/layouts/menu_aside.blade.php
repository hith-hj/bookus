<div id="menu_aside" class="main-menu menu-fixed menu-accordion menu-shadow expanded" data-scroll-to-active="true"
    style="touch-action: none; user-select: none; -webkit-user-drag: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
    
    <div class="navbar-header expanded">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto">
                <a href="{{route('admin.home')}}" class="navbar-brand " target="_self">
                    {{-- <img src="{{ asset('user/imgs/favicon.png') }}" width="19px" height="19px"> --}}
                    <h2 class="brand-text logo-color ps-50 fs-2">4Bookus</h2>
                </a>
            </li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <ul class="ps-container main-menu-content scroll-area ps">
        <ul class="navigation navigation-main">
            <li class="nav-item {{request()->is('admin/home') ? 'active':''}}">
                <a href="{{route('admin.home')}}" class="d-flex align-items-center">
                    <i class="bi bi-house"></i>
                    <span class="menu-title text-truncate">{{ __('lang.Home') }}</span>
                </a>
            </li>
            {{-- <li class="nav-item has-sub {{request()->is('admin/categories') || request()->is('admin/services') ? 'open':''}}">
                <a href="#" target="_self" class="d-flex align-items-center">
                    <i class="bi bi-journals"></i>
                    <span class="menu-title text-truncate">Catalog</span>
                </a>
                <ul class="menu-content ">
                    <li class="nav-item {{request()->is('admin/categories') ? 'active':''}}">
                        <a href="{{route('admin.categories')}}" class="d-flex align-items-center">
                            <i class="bi bi-journal"></i>
                            <span class="menu-title text-truncate">Categories</span>
                        </a>
                    </li>
                    <li class="nav-item {{request()->is('admin/services') ? 'active':''}}">
                        <a href="{{route('admin.services')}}" class="d-flex align-items-center">
                            <i class="bi bi-journal"></i>
                            <span class="menu-title text-truncate">Services</span>
                        </a>
                    </li>
                </ul>
            </li> --}}
            <li class="nav-item {{request()->is('admin/categories') ? 'active':''}}">
                <a href="{{route('admin.categories')}}" class="d-flex align-items-center">
                    <i class="bi bi-bookmark"></i>
                    <span class="menu-title text-truncate">{{ __('lang.Categories')}}</span>
                </a>
            </li>
            <li class="nav-item {{request()->is('admin/services') ? 'active':''}}">
                <a href="{{route('admin.services')}}" class="d-flex align-items-center">
                    <i class="bi bi-journal"></i>
                    <span class="menu-title text-truncate">{{ __('lang.Services')}}</span>
                </a>
            </li>
            <li class="nav-item {{request()->is('admin/centers') ? 'active':''}}">
                <a href="{{route('admin.centers')}}" class="d-flex align-items-center">
                    <i class="bi bi-boxes"></i>
                    <span class="menu-title text-truncate">{{ __('lang.Centers')}}</span>
                </a>
            </li>
            
            <li class="nav-item {{request()->is('admin/admins') ? 'active':''}}">
                <a href="{{ route('admin.admins') }}" class="d-flex align-items-center">
                    <i class="bi bi-person-vcard"></i>
                    <span class="menu-title text-truncate">{{ __('lang.Admins')}}</span>
                </a>
            </li>
            <li class="nav-item {{request()->is('admin/roles') ? 'active':''}}">
                <a href="{{ route('admin.roles') }}" class="d-flex align-items-center">
                    <i class="bi bi-shield"></i>
                    <span class="menu-title text-truncate">{{ __('lang.Roles')}}</span>
                </a>
            </li>
            <li class="nav-item {{request()->is('admin/clients') ? 'active':''}}">
                <a href="{{route('admin.clients')}}" class="d-flex align-items-center">
                    <i class="bi bi-people"></i>
                    <span class="menu-title text-truncate">{{ __('lang.Clients')}}</span>
                </a>
            </li>
            <li class="nav-item {{request()->is('admin/appointments') ? 'active':''}}">
                <a href="{{route('admin.appointments')}}" class="d-flex align-items-center">
                    <i class="bi bi-calendar-week"></i>
                    <span class="menu-title text-truncate">{{ __('lang.Appointments')}}</span>
                </a>
            </li>
            <li class="nav-item {{request()->is('admin/calender') ? 'active':''}}">
                <a href="{{route('admin.charts')}}" class="d-flex align-items-center">
                    <i class="bi bi-bar-chart"></i>
                    <span class="menu-title text-truncate">{{ __('lang.Charts')}}</span>
                </a>
            </li>
            {{-- <li class="nav-item {{request()->is('admin/requests') ? 'active':''}}">
                <a href="{{ route('admin.requests')}}" class="d-flex align-items-center">
                    <i class="bi bi-person-down"></i>
                    <span class="menu-title text-truncate">{{ __('lang.Requests')}}</span>
                </a>
            </li> --}}
            <li class="nav-item {{request()->is('admin/settings') ? 'active':''}}">
                <a href="{{ route('admin.settings') }}" class="d-flex align-items-center">
                    <i class="bi bi-gear"></i>
                    <span class="menu-title text-truncate">{{ __('lang.Settings')}}</span>
                </a>
            </li>
        </ul>
    </ul>
</div>
