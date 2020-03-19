<!doctype html>
<html lang="en">
    <head>
        <!-- Header include -->
        @include('auth-layouts.header-include')
    </head>
    <body>
        <div class="page-loader" id="page-loader">
            <img src="{{ asset('img/loader.gif')}}" width="80" alt="loader" />
        </div>
    <div class="login-container">
    
     <!-- Container -->
     @yield('content')
    </div>
    <!-- Footer Include -->
    @include('auth-layouts.footer-include')
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
</body>
</html>