@extends('auth-layouts.app')
@section('title','Login')
@section('content')
    <img class="login-logo img-fluid" src="{{ asset('img/logo.svg')}}"  />
        <div class="login-form">
          <img class="img-fluid" src="{{ asset('img/five-color.png')}}" />
           <h3 class="title">{{ __('Login') }}</h3>
          <p class="subtitle">Welcome, please login to your account.</p>
          <form method="POST" action="{{ route('login') }}" class="form user-login">
            @csrf
            <div class="form-group">
                <label class="login-lbl">{{ __('Email address') }}</label>  
                <input type="text" maxlength="50" placeholder="Enter email address" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email" autofocus />
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <label class="login-lbl">{{ __('Password') }}</label>  
                <input id="password" type="password" maxlength="15" placeholder="Enter password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="current-password">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group text-right">
                <button id="login-btn" type="submit" class="btn btn-color-theme btn-rounded">{{ __('Login') }}</button>  
            </div>
          </form>
          @if (Route::has('password.request'))
          <div class="forgot-url">
            <a href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a>
          </div>
          @endif
          <p class="privacy-term">Protected by Poundshop and subject to the Google <a>Privacy Policy</a> and <a>Terms of Service</a>.</p>
          <!-- Version & Copyright  --> 
          
        </div>
        <div class="version-copy">
            <span class="version">Version: 1.0</span>
            <span class="copyright">Copyright &copy; 2019 Poundshop</span>
          </div>
@endsection
@section('pageScript')
    <script src="{{ asset('js/auth/auth.js?v='.CSS_JS_VERSION) }}"></script>
@endsection