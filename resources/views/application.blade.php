<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- <link rel="icon" href="<%= BASE_URL %>favicon.ico"> -->

  <title>4bookus | Admin </title>

  <!-- Splash Screen/Loader Styles -->
  <link rel="stylesheet" type="text/css" href="{{ asset(mix('css/loader.css')) }}" />

  <!-- Styles -->
  <link rel="stylesheet" href="{{ asset(mix('css/core.css')) }}">

  <!-- Favicon -->
  <link rel="shortcut icon" href="{{ asset('logo.png') }}">

  <!-- Font -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400&display=swap"
    rel="stylesheet">
</head>

<body>
    <noscript>
        <strong>4bookus Admin doesn't work properly without JavaScript enabled. Please enable it to continue.</strong>
    </noscript>
    <div id="loading-bg">
        <div class="loading-logo">
          {{-- <img src="{{ asset('logo.png') }}" alt="Logo" /> --}}
        </div>
        <div class="loading">
          <div class="effect-1 effects"></div>
          <div class="effect-2 effects"></div>
          <div class="effect-3 effects"></div>
        </div>
    </div>
    <div id="app"></div>
    <div id="blade_auth">
        @include('auth')
    </div>
    <script src="{{ asset(mix('js/app.js')) }}"></script>
    <script>
        const user = localStorage.getItem('user');
        const token = localStorage.getItem('userToken');
        if(user == null || token == null){
            let app = document.querySelector("#app");
            app.remove();
        }else{
            let auth = document.querySelector("#blade_auth");
            auth.remove();
        }
        
        window.navigation.addEventListener("navigate", (event) => {
            // setInterval(()=>{setStuff()},10000)
            if( event.destination.url ==  'https://4bookus.com/login'){
                event.preventDefault();
                window.location.replace('https://4bookus.com/login'); 
            } 
            setStuff()
        });
        
        window.onload = (event) => {setStuff()}
        
        function setStuff(){
            getLogo()
            if( window.location.pathname == '/dashboard/ecommerce' || window.location.pathname !==  '/login'){
                removeContent();
            } 
        }
        
        function getLogo(){
            const logo = document.querySelector("a.brand-logo");
            const menu = document.querySelector("a.navbar-brand");
            if(logo !== null){
                let ti = document.querySelector("h2.card-title.mb-1.font-weight-bold");
                if(ti !== null){
                    ti.innerText = "Welcome to 4Bookus"; 
                }
                // setInterval(setLogo(logo), 1000);
                setLogo(logo);
            }
            if(menu !== null){
                setLogo(menu);
            }
        }
        
        function setLogo(el){
            while (el.firstChild) {
                el.removeChild(el.firstChild);
            }
            const classes = "brand-text text-primary ml-1 ";
            let h2 = document.createElement("h2");
            let img = document.createElement("img");
            h2.setAttribute('class',classes);
            h2.innerText = "Bookus";
            img.setAttribute('src','/logo.png')
            img.setAttribute('width','25px')
            img.setAttribute('height','25px')
            el.appendChild(img);
            el.appendChild(h2);
        }
        
        function removeContent(){
            let el = document.querySelector("section#dashboard-ecommerce");
            if(el !== null){
                el.style.display = 'none';
                // el.remove();
            }
            
            let ul1 = document.querySelector("#bookmark-0");
            if(ul1 !== null && ul1.parentElement !== null){
                ul1.parentElement.style.display = 'none' ;
                // ul1.parentElement.remove() ;
            }
            
            let footer = document.querySelector("footer.footer");
            if(footer !== null){
                footer.style.display = 'none';
                // footer.remove();
            }
            
            let ul = document.querySelector("ul.navbar-nav.nav.align-items-center.ml-auto");
            if(ul.children !== null ){
                ul.children.forEach((child,index)=>{
                    if(index != 1 && index != 5){
                        child.style.display = "none";
                    }
                })   
            }
        }
    </script>
</body>
</html>
