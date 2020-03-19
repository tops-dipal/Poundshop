@extends('auth-layouts.app')
@section('title','Reset Password')
@section('content')
<img class="login-logo img-fluid" src="{{ asset('img/logo.svg')}}"  />
<div class="login-form">
    <img class="img-fluid" src="{{ asset('img/five-color.png')}}" />
    <h3 class="title">{{ __('Reset Password?') }}</h3>
    <form id="reset-password-form" class="form" method="POST" action="{{route('password.update')}}" >
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="form-group">
            <label class="login-lbl">{{ __('Email address') }}</label>
            <input autofocus type="text" maxlength="50" placeholder="Enter email address" class="form-control" @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" />
                   @error('email')
                   <span class="invalid-feedback" role="alert" style="display: block;">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group">
            <label class="login-lbl">{{ __('Password') }}</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
            @error('password')
            <span class="invalid-feedback" role="alert" style="display: block;">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group">
            <label class="login-lbl">{{ __('Confirm Password') }}</label>
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
        </div>
        <div class="form-group text-right">
            <button id="reset-button" type="submit" class="btn btn-color-theme btn-rounded">Submit</button>
        </div>
    </form>
    <div class="forgot-url">
        <a href="{{route('login')}}">Login</a>
    </div>
</div>
@endsection
@section('pageScript')
<script src="{{ asset('js/auth/auth.js') }}"></script>
@endsection
