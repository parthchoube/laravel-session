<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel Setup</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/icheck-bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
        <script nonce="cb42c422-4722-49ed-8833-4c044a4388d5">(function(w,d){!function(a,e,t,r,z){a.zarazData=a.zarazData||{},a.zarazData.executed=[],a.zarazData.tracks=[],a.zaraz={deferred:[]};var s=e.getElementsByTagName("title")[0];s&&(a.zarazData.t=e.getElementsByTagName("title")[0].text),a.zarazData.w=a.screen.width,a.zarazData.h=a.screen.height,a.zarazData.j=a.innerHeight,a.zarazData.e=a.innerWidth,a.zarazData.l=a.location.href,a.zarazData.r=e.referrer,a.zarazData.k=a.screen.colorDepth,a.zarazData.n=e.characterSet,a.zarazData.o=(new Date).getTimezoneOffset(),a.dataLayer=a.dataLayer||[],a.zaraz.track=(e,t)=>{for(key in a.zarazData.tracks.push(e),t)a.zarazData["z_"+key]=t[key]},a.zaraz._preSet=[],a.zaraz.set=(e,t,r)=>{a.zarazData["z_"+e]=t,a.zaraz._preSet.push([e,t,r])},a.dataLayer.push({"zaraz.start":(new Date).getTime()}),a.addEventListener("DOMContentLoaded",(()=>{var t=e.getElementsByTagName(r)[0],z=e.createElement(r);z.defer=!0,z.src="/cdn-cgi/zaraz/s.js?z="+btoa(encodeURIComponent(JSON.stringify(a.zarazData))),t.parentNode.insertBefore(z,t)}))}(w,d,0,"script");})(window,document);</script>
    </head>
    <body class="hold-transition login-page">
        <div class="login-box">
			<div class="card">
				<div class="login-logo mt-4"><b>Login</b></div>
				<div class="card-body login-card-body">
					@if ($errors->any())
					    <div class="alert alert-danger">
					        <ul>
					            @foreach ($errors->all() as $error)
					                <li>{{ $error }}</li>
					            @endforeach
					        </ul>
					    </div>
					@endif
					<form action="{{ route('admin.doLogin') }}" method="post">
						@csrf
						<div class="input-group mb-3">
							<input type="email" name="email" class="form-control input" placeholder="Email">
							<div class="input-group-append">
								<div class="input-group-text">
									<span class="fa fa-envelope"></span>
								</div>
							</div>
						</div>
						<div class="input-group mb-3">
							<input type="password" name="password" class="form-control input" placeholder="Password">
							<div class="input-group-append">
								<div class="input-group-text">
									<span class="fa fa-lock"></span>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-12">
								<button type="submit" class="btn btn-primary btn-block">Sign In</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
        <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('js/adminlte.min.js?v=3.2.0') }}"></script>
    </body>
    <script>
    	let input = document.querySelector(".input");
    	let button = document.querySelector(".btn");
    	button.disabled = true;
    	input.addEventListener("change", stateHandle);
    	function stateHandle() {
		    if(document.querySelector(".input").value === "") {
		        button.disabled = true;
		    } else {
	        button.disabled = false;
	    	}
		}
    </script>
</html>