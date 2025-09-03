<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>4bookus @yield('title')</title>
  <link rel="shortcut icon" href="{{asset('user/imgs/favicon.png')}}" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="{{asset('user/css/style.css')}}">
</head>
<style>
    .hidden{
        display:none;
    }
    .search-list{
        position: absolute;
        width: 100%;
        max-height: 300px;
        overflow-y: auto;
        z-index: 10;
        background-color: #fff;
    }
</style>
<body>
	@include('user.partials.navbar')
    @if(Session::has('error'))
        <div class="alert alert-danger p-1 mx-auto w-50">{{Session::get('error')}}</div>
    @endif
    @if(Session::has('success'))
        <div class="alert alert-success p-1 mx-auto w-50">{{Session::get('success')}}</div>
    @endif
 	@yield('content')

	@include('user.partials.footer')
	<script src="{{ asset('user/js/popper.min.js') }}"></script>
    <script src="{{ asset('user/js/jquery-3.6.3.min.js') }}"></script>
    <script src="{{ asset('user/js/bootstrap.min.js') }}"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Your other scripts -->
	<script src="{{ asset('user/js/popover.js') }}"></script>
	<script>
	    function centerSearch(event, el){
	        let list = document.querySelector("#searchResult");
	        listClear(list);
	        if(event.key == 'Enter' && el.value != ''){
	            if(el.value == ''){
    	            return;
    	        }
	            event.preventDefault();
	            ccc(list,el.value, parent);
	        }
	        if(event.key == 'Escape'){
	            listClear(list);
	            itemHide(list);
	            let span = document.querySelector("#searchStatus");
    	        itemHide(span);
	            el.value= '';
	        }
	    } 
	    function itemShow(item){
	        item.classList.remove('hidden');
	    }
	    function itemHide(item){
	        item.classList.add('hidden');
	    }
	    function listClear(list){
	        while (list.hasChildNodes ()) 
	        { list.removeChild (list.firstChild);}
	    }
	    function ccc(ul, value, parent){
	        let span = document.querySelector("#searchStatus");
	        itemShow(span);
	        let url = "{{route('search')}}"+'?q='+value;
	        let status = 0;
	        fetch(url).then((res)=>{
	            status = res.status;
                return res.json();
	        }).then((data) =>{
	            if(status !== 200){
                    return alert('Error');
                }
                data['centers'].forEach((center)=>{
                    let li = document.createElement('li');
    	            li.classList = 'list-group-item m-0 p-1';
    	            let a = document.createElement('a');
    	            a.href = "{{route('center')}}"+'?id='+center.id;
    	            a.innerText = `Salon - ${center.name}`;
    	            li.appendChild(a);
    	            ul.appendChild(li);
                });
	            data['services'].forEach((service)=>{ 
                    let li = document.createElement('li');
    	            li.classList = 'list-group-item m-0 p-1';
    	            let a = document.createElement('a');
    	            a.href = "{{route('center')}}"+'?id='+service.center_id;
    	            a.innerText = `Service - ${service.name}`;
    	            li.appendChild(a);
    	            ul.appendChild(li);
                });
                itemShow(ul);
                itemHide(span);
	        }).catch((err)=>{
	            console.log(err); 
	        });
	    }
	</script>
</body>
</html>
