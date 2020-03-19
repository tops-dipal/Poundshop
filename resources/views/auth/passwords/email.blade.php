@extends('auth-layouts.app')
@section('title','Forgot Password')
@section('content')
<img class="login-logo img-fluid" src="{{ asset('img/logo.svg')}}"  />
<div class="login-form">
    <img class="img-fluid" src="{{ asset('img/five-color.png')}}" />
    <h3 class="title">{{ __('Recover your password?') }}</h3>
    <p class="subtitle">Please enter your email address and we'll send you instructions on how to reset your password.</p>
    <form id="forgot-form" class="form" method="POST" action="{{route('user.forgot-password')}}" >
        @csrf
        <div class="form-group">
            <label class="login-lbl">{{ __('Email address') }}</label>
            <input autofocus type="text" maxlength="50" placeholder="Enter email address" class="form-control" @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" />
                   @error('email')
                   <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group text-right">
            <button id="forgot-button" type="submit" class="btn btn-color-theme btn-rounded">Submit</button>
        </div>
    </form>
    <div class="forgot-url">
        <a href="{{route('login')}}">Existing User? Back to Login</a>
    </div>
    <p class="privacy-term">Protected by Poundshop and subject to the Google <a>Privacy Policy</a> and <a>Terms of Service</a>.</p>
</div>
@endsection
@section('pageScript')
<script src="{{ asset('js/auth/auth.js') }}"></script>
@endsection
