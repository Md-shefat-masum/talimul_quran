@extends('layouts.app')

@section('title', 'Verify Email')

@section('content')
<div class="auth-heading">
    <h1>Verify email</h1>
    <p>Please check your inbox for the verification link</p>
</div>

@if (session('resent'))
    <div class="auth-alert auth-alert--success" role="alert">
        A fresh verification link has been sent to your email address.
    </div>
@endif

<form class="auth-form" method="POST" action="{{ route('verification.resend') }}">
    @csrf
    <button type="submit" class="auth-submit">Send another link</button>
</form>

<div class="auth-divider"><span>Switch form</span></div>

<div class="auth-switch">
    <a href="{{ route('login') }}">Login</a>
    @if (Route::has('register'))
        <a href="{{ route('register') }}">Register</a>
    @endif
    @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}">Forgot</a>
    @endif
</div>
@endsection
