@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<div class="auth-heading">
    <h1>Reset password</h1>
    <p>Choose a fresh password for your account</p>
</div>

<form method="POST" action="{{ route('password.update') }}" class="auth-form">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    <div class="auth-field">
        <label for="email">Email address</label>
        <div class="auth-control @error('email') is-invalid @enderror">
            <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus placeholder="hello@example.com">
            <span class="auth-control__dot" aria-hidden="true"></span>
        </div>
        @error('email')
            <p class="auth-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="auth-field">
        <label for="password">New password</label>
        <div class="auth-control @error('password') is-invalid @enderror">
            <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Create password">
        </div>
        @error('password')
            <p class="auth-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="auth-field">
        <label for="password-confirm">Confirm password</label>
        <div class="auth-control">
            <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat password">
        </div>
    </div>

    <button type="submit" class="auth-submit">Reset password</button>
</form>

<div class="auth-divider"><span>Switch form</span></div>

<div class="auth-switch">
    <a href="{{ route('login') }}">Login</a>
    @if (Route::has('register'))
        <a href="{{ route('register') }}">Register</a>
    @endif
    <a href="{{ route('password.request') }}">New link</a>
    <span class="is-active">Reset</span>
</div>
@endsection
