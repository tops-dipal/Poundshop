<!DOCTYPE html>
<html>
	<head>
		<title>@yield('title') - {{ config('app.name') }}</title>
		@include('layouts.header')
		@yield('css')
	</head>
	<body>	
            <div class="page-loader" id="page-loader">
                <img src="{{ asset('img/loader.gif')}}" width="80" alt="loader" />
            </div>
		<div class="page-wrapper animsition">
			@include('layouts.sidebar')
			<div class="content-area">
			@yield('content')
			</div>	
		</div>
        @include('layouts.footer')
	</body>
	
</html>		
