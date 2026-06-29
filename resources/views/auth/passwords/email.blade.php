@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="auth-heading">
    <h1>Forgot password?</h1>
    <p>Enter your email and we will send a reset link</p>
</div>

@if (session('status'))
    <div class="auth-alert auth-alert--success" role="alert">{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('password.email') }}" class="auth-form">
    @csrf

    <div class="auth-field">
        <label for="email">Email address</label>
        <div class="auth-control @error('email') is-invalid @enderror">
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="hello@example.com">
            <span class="auth-control__dot" aria-hidden="true"></span>
        </div>
        @error('email')
            <p class="auth-error">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="auth-submit">Send reset link</button>
</form>

<div class="auth-divider"><span>Switch form</span></div>

<div class="auth-switch">
    <a href="{{ route('login') }}">Login</a>
    @if (Route::has('register'))
        <a href="{{ route('register') }}">Register</a>
    @endif
    <a class="is-active" href="{{ route('password.request') }}">Forgot</a>
</div>
@endsection
