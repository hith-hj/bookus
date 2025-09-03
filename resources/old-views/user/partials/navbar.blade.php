 <!------------- Navbar ---------------->
  <nav class="navbar navbar-expand-lg bg-body-tertiaryx">
    <div class="container-fluid">
      <img class="logo" src="{{asset('user/imgs/home/logo.png')}}">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
        aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon">
            <i class="fa fa-barsx" aria-hidden="true"></i>
        </span>
      </button>
      <div class="collapse navbar-collapse n-flex nav-padding" id="navbarNavDropdown">
        <ul class="navbar-nav ">
            @guest
                <li class="nav-item ">
                    <a class="nav-link {{request()->is('/') ? 'active':''}} " aria-current="page" href="/">Home</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link {{request()->is('about') ? 'active':''}} " aria-current="page" href="/#about">About</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link {{request()->is('user/login') ? 'active':''}}" aria-current="page" href="{{route('user.loginPage')}}">Login</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link {{request()->is('#download_app') ? 'active':''}}" aria-current="page" href="/#download_app">Download App</a>
                </li>
            @else
                <li class="nav-item ">
                    <a class="nav-link {{request()->is('/') ? 'active':''}} " aria-current="page" href="{{route('user.home')}}">Home</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link {{request()->is('user/appointments') ? 'active':''}} " aria-current="page" href="{{route('user.appointments')}}">Appointments</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link {{request()->is('user/favorites') ? 'active':''}} " aria-current="page" href="{{route('user.favorites')}}">Favorits</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" aria-current="page" href="#"><i class="fa fa-bell"></i></a>
                </li>
                <li class="nav-item dropdown"> 
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ucfirst(Auth::user()->first_name)}}
                    </a>
                    <ul class="dropdown-menu">
                      <li><a href="{{route('user.profile')}}" class="dropdown-item">Profile</a></li>
                      <li><button class="dropdown-item" type="submit" form="logoutForm">Logout</button></li>
                    </ul>
                    <form id="logoutForm" method="post" action="{{route('user.logout')}}">@csrf</form>
                </li> 
            @endguest
          
          <!--<li class="nav-item dropdown"> <a class="nav-link dropdown-toggle" href="#" role="button"-->
          <!--    data-bs-toggle="dropdown" aria-expanded="false">Categories <i class="ti-angle-down"></i></a>-->
          <!--  <ul class="dropdown-menu">-->
          <!--    <li><a href="categories.html" class="dropdown-item"><span>All categories</span></a></li>-->
          <!--    <li><a href="#" class="dropdown-item"><span>category 1-1</span></a></li>-->
          <!--    <li><a href="#" class="dropdown-item"><span>category 1-1</span></a></li>-->
          <!--    <li><a href="#" class="dropdown-item"><span>category 1-1</span></a></li>-->
          <!--    <li><a href="#" class="dropdown-item"><span>category 1-1</span></a></li>-->
          <!--    <li><a href="#" class="dropdown-item"><span>category 1-1</span></a></li>-->
          <!--    <li><a href="#" class="dropdown-item"><span>category 1-1</span></a></li>-->
          <!--  </ul>-->
          <!--</li>      -->
          <!--<li class="nav-item dropdown"> <a class="nav-link dropdown-toggle" href="#" role="button"-->
          <!--    data-bs-toggle="dropdown" aria-expanded="false">Options<i class="ti-angle-down"></i></a>-->
          <!--  <ul class="dropdown-menu">-->
          <!--    {{-- <li><a href="#" class="dropdown-item"><span>Settings</span></a></li> --}}-->
          <!--    <li><a href="#" class="dropdown-item"><span>Rate us</span></a></li>-->
          <!--    {{-- <li><a href="#" class="dropdown-item"><span>4BookUs for business</span></a></li> --}}-->
          <!--  </ul>-->
          <!--</li>-->
          <!--<li class="nav-item dropdown"> -->
          <!--  <a class="nav-link dropdown-toggle" href="#" role="button"-->
          <!--    data-bs-toggle="dropdown" aria-expanded="false">-->
          <!--      <img style="width: 25px;" src="img/profile.png" alt="User"> -->
          <!--          <i class="ti-angle-down"></i>-->
          <!--  </a>-->
          <!--  <ul class="dropdown-menu">-->
          <!--    <li><a class="dropdown-item" href="#">Edit information</a></li>-->
          <!--    <li><a class="dropdown-item" href="#">Vouchers</a></li>-->
          <!--    <li><a class="dropdown-item" href="appointments.html">Appointments</a></li>-->
          <!--    <li><a class="dropdown-item" href="giftcards.html">Gift cards</a></li>-->
          <!--    <li><a class="dropdown-item" href="memberships.html">Memberships</a></li>-->
          <!--    <li><a class="dropdown-item" href="#">Addresses</a></li>-->
          <!--  </ul>-->
          <!--</li>-->
        </ul>
      </div>
    </div>
  </nav>
  <!------------- End Navbar ---------------->