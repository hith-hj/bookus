<nav
    class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow  container-xxl">
    <div class="navbar-container d-flex content">
        <div class="bookmark-wrapper d-flex align-items-center">
            <ul class="nav navbar-nav d-xl-none">
                <li class="nav-item">
                    <a class="nav-link menu-toggle is-active" href="javascript:void(0);">
                        <i class="fa-lg bi bi-list"></i>
                    </a>
                </li>
            </ul>
            {{-- <ul class="nav navbar-nav bookmark-icons">
                <li class="nav-item d-none d-lg-block">
                    <a class="nav-link" href="http://127.0.0.1:8000/app/email"
                        data-bs-toggle="tooltip" data-bs-placement="bottom" title=""
                        data-bs-original-title="Email" aria-label="Email">
                        <i class="fa-lg bi bi-calendar"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-lg-block">
                    <a class="nav-link" href="http://127.0.0.1:8000/app/chat"
                        data-bs-toggle="tooltip" data-bs-placement="bottom" title="" data-bs-original-title="Chat"
                        aria-label="Chat">
                        <i class="fa-lg bi bi-chat-left"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-lg-block">
                    <a class="nav-link" href="http://127.0.0.1:8000/app/calendar"
                        data-bs-toggle="tooltip" data-bs-placement="bottom" title=""
                        data-bs-original-title="Calendar" aria-label="Calendar">
                        <i class="fa-lg bi bi-calendar-check"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-lg-block">
                    <a class="nav-link" href="http://127.0.0.1:8000/app/todo"
                        data-bs-toggle="tooltip" data-bs-placement="bottom" title=""
                        data-bs-original-title="Todo" aria-label="Todo">
                        <i class="fa-lg bi bi-star"></i>
                    </a>
                </li>
            </ul> --}}
        </div>
        @include('admin.components._breadCrumps')
        <ul class="nav navbar-nav align-items-center ms-auto">
            <li class="nav-item d-none d-lg-block" onclick="toggleTheme()">
                <a class="nav-link nav-link-style">
                    <i id="theme_icon" ></i>
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle"  data-bs-toggle="dropdown">
                    <i class="bi bi-translate fa-lg"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end " >
                    <a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active':'' }}" href="{{ route('lang','en') }}">
                        <i class="bi bi-us"></i> English
                    </a>
                    <a class="dropdown-item {{ app()->getLocale() == 'ar' ? 'active':'' }}" href="{{ route('lang','ar') }}">
                        <i class="bi bi-sy"></i> Arabic
                    </a>
                </div>
            </li>
            <li class="nav-item nav-search">
                <a class="nav-link nav-link-search">
                    <i class="bi bi-search fa-lg"></i>
                </a>
                <div class="search-input h-100">
                    {{-- <div class="search-input-icon"><i class="bi bi-search"></i></div> --}}
                    <form action="{{ route('admin.centers') }}" method="get" class="m-0">
                    <input class="form-control  p-2" name="search" type="text" placeholder="Search..." 
                    oninput ="event.preventDefault()">
                    </form>
                    <div class="search-input-close"> <i class="bi bi-x"></i> </div>
                    {{-- <ul class="search-list search-list-main ps">
                        result
                    </ul> --}}
                </div>
            </li>
            {{-- <li class="nav-item dropdown dropdown-notification me-25">
                <a class="nav-link" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <i class="bi bi-bell fa-lg"></i>
                    <span class="badge rounded-pill bg-danger badge-up">2</span>
                </a>
            </li> --}}
            <li class="nav-item dropdown dropdown-user">
                <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);"
                    data-bs-toggle="dropdown" aria-haspopup="true">
                    <span class="avatar">
                        <img class="round" src="{{asset('user/imgs/profile.png')}}"
                            alt="avatar" height="30" width="30">
                        <span class="avatar-status-online"></span>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user">
                    <h6 class="dropdown-header">{{auth()->user()->first_name}}</h6>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-left"></i> {{ __('lang.Logout') }}
                    </a>
                    <form method="POST" id="logout-form" action="{{route('admin.logout')}}" hidden>
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
    </div>
</nav>
