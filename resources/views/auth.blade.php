<div>
    <div class="auth-wrapper auth-v2">
        <div class="row auth-inner m-0">
            <a href="#" target="_self" class="brand-logo">
                <img src="/logo.png" width="25px" height="25px">
                <h2 class="brand-text text-primary ml-1 ">Bookus</h2>
            </a> 
            <div class="d-none d-lg-flex align-items-center p-5 col-lg-8">
                <div class="w-100 d-lg-flex align-items-center justify-content-center px-5">
                    <img src="/images/_/_/_/_/1-book-me/resources/js/src/assets/images/pages/login-v2.svg" alt="Login V2" class="img-fluid">
                </div>
            </div> 
            <div class="d-flex align-items-center auth-bg px-2 p-lg-5 col-lg-4">
                <div class="px-xl-2 mx-auto col-sm-8 col-md-6 col-lg-12">
                    <h2 class="card-title mb-1 font-weight-bold">Welcome to 4Bookus</h2> 
                    <p class="card-text mb-2"> Sign-in to your account </p> 
                    <div role="alert" aria-live="polite" aria-atomic="true" class="alert alert-primary">
                        <div class="alert-body font-small-2">
                            <p><small class="mr-50"><span class="font-weight-bold">Admin:</span> admin@luxury.com | password</small></p>
                        </div> 
                        <svg xmlns="http://www.w3.org/2000/svg" width="18px" height="18px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="position-absolute feather feather-help-circle" style="top: 10px; right: 10px;"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    </div> 
                    <span>
                        <form class="auth-login-form mt-2" action="" method="post" onsubmit="login(event)">
                            @csrf
                            <div role="group" class="form-group" id="__BVID__267">
                                <label for="login-email" class="d-block" id="__BVID__267__BV_label_">Email</label>
                                <div>
                                    <span>
                                        <input id="login-email" name="login-email" type="text" placeholder="john@example.com" value="admin@luxury.com" class="form-control">
                                        <small class="text-danger"></small>
                                    </span>
                                </div>
                            </div>
                            <fieldset class="form-group" id="__BVID__270">
                                <div>
                                    <div class="d-flex justify-content-between">
                                        <label for="login-password">Password</label> 
                                        <a href="/forgot-password" class="" target="_self">
                                            <small>Forgot Password?</small>
                                        </a>
                                    </div>
                                </div>
                                <div> 
                                    <span>
                                        <div role="group" class="input-group input-group-merge"><!---->
                                            <input id="login-password" name="login-password" type="password" placeholder="Password" value="password" class="form-control-merge form-control"> 
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14px" height="14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="cursor-pointer feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-danger"></small>
                                    </span>
                                </div>
                            </fieldset> 
                            <button type="submit" class="btn btn-primary btn-block">Sign in</button>
                        </form>
                    </span> 
                    <!--<p class="card-text text-center mt-2">-->
                    <!--    <span>New on our platform? </span> -->
                    <!--    <a href="/register" class="" target="_self">-->
                    <!--        <span>&nbsp;Create an account</span>-->
                    <!--    </a>-->
                    <!--</p> -->
                </div>
            </div>
        </div>
    </div>
</div>
                
<script>
    function login(e){
        e.preventDefault();
        let url = 'https://4bookus.com/api/admin/login';
        let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let email = document.querySelector('input[name="login-email"]').value;
        let password = document.querySelector('input[name="login-password"]').value;
        let status = 0;
        fetch(url, {
            headers: {
              "Content-Type": "application/json",
              "Accept": "application/json, text-plain, */*",
              "X-Requested-With": "XMLHttpRequest",
              "X-CSRF-TOKEN": token
              },
            method: 'post',
            credentials: "same-origin",
            body: JSON.stringify({
                  email: email,
                  password: password
                })
            }).then(res => {
                status = res.status;
                return res.json();
            }).then(data =>{
                if(status !== 200){
                    return alert(data.error_des['message']);
                }
                localStorage.setItem('userToken','"'+data.content.token+'"');
                localStorage.setItem('user',JSON.stringify(data.content['user']));
                return window.location.pathname = "/admin"; 
            }).catch(function(error) {
                console.log(error);
            });
    }
</script>
                
                
                
                
                
                
                
                
                
                
                
                