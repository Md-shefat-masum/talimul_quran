@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="auth-heading">
    <h1>Create account</h1>
    <p>Start managing your admin workspace</p>
</div>

<form method="POST" action="{{ route('register') }}" class="auth-form">
    @csrf

    <div class="auth-field">
        <label for="name">Name</label>
        <div class="auth-control @error('name') is-invalid @enderror">
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Your name">
            <span class="auth-control__dot" aria-hidden="true"></span>
        </div>
        @error('name')
            <p class="auth-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="auth-field">
        <label for="email">Email address</label>
        <div class="auth-control @error('email') is-invalid @enderror">
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="hello@example.com">
        </div>
        @error('email')
            <p class="auth-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="auth-field">
        <label for="password">Password</label>
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

    <button type="submit" class="auth-submit">Create account</button>
</form>

<div class="auth-divider"><span>Switch form</span></div>

<div class="auth-switch">
    <a href="{{ route('login') }}">Login</a>
    <a class="is-active" href="{{ route('register') }}">Register</a>
    @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}">Forgot</a>
    @endif
</div>
@endsection
