<div class="container" id="#header">    
    <div class="history section-padding">
        <div class="container">
          <div class="row">
            <div class="col-sm-6">
              <div class="h1-style section-heading">
                <h1>Book local beauty and wellness services</h1>
                <span class="single-title-line"></span>
              </div>
              <div class="history-content" style="position: relative;">
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam venenatis est tortor, eu condimentum ante
                  mollis eget. Quisque hendrerit efficitur ante. Proin purus augue, tristique nec sapien ac, blandit
                  tristique nulla. Aliquam erat volutpat. Cras enim nunc, tincidunt nec fermentum ut, porta vel leo. Aliquam
                  id commodo odio. Donec vitae augue ac ligula pulvinar semper.Suspendisse fringilla placerat velit et fermentum. Vivamus aliquet mi et risus interdum, a aliquam velit
                  hendrerit. Nullam mattis turpis diam, vel suscipit lacus cursus id.</p>
                <form class="d-flex" role="search" onsubmit="event.preventDefault()">
                  <div class="search-wrapper">
                      
                    <input class="form-control search-input" type="text" name="search" placeholder="Search for a service or salon..."
                      onchange="centerSearch(event, this)" onkeydown="centerSearch(event, this)">
                    <span class="fa fa-search search-icon"></span>
                  </div>
                  <!--<button class="btn btn-outline-primary mx-2" type="submit">Search</button>-->
                </form>
                <span class="hidden" id="searchStatus"> Loading...</span>
                <ul id="searchResult" class="list-group list-group-flush border rounded p-1 search-list hidden">
                    <!--<li class="list-group-item d-flex justify-content-between align-items-center">-->
                    <!--    Salons-->
                    <!--    <span class="badge badge-primary badge-pill">14</span>-->
                    <!--</li>-->
                </ul>
              </div>
            </div>
            <!--/ Column-->
            <div class="col-sm-6">
              <div class="history-img text-center">
                <img class="img-responsive" src="{{asset('user/imgs/home/header.png')}}" alt="" title="">
              </div>
            </div>
          </div>
        </div>
      </div>
</div>
<div class="empty"></div>