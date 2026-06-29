@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="auth-heading">
    <h1>Welcome</h1>
    <p>Sign in to your admin account</p>
</div>

@if (session('status'))
    <div class="auth-alert auth-alert--success" role="alert">{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('login') }}" class="auth-form">
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

    <div class="auth-field">
        <div class="auth-label-row">
            <label for="password">Password</label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Forgot password?</a>
            @endif
        </div>
        <div class="auth-control @error('password') is-invalid @enderror">
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Enter password">
        </div>
        @error('password')
            <p class="auth-error">{{ $message }}</p>
        @enderror
    </div>

    <label class="auth-check" for="remember">
        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
        <span>Keep me signed in</span>
    </label>

    <button type="submit" class="auth-submit">Sign in</button>
</form>

<div class="auth-divider"><span>Switch form</span></div>

<div class="auth-switch">
    <a class="is-active" href="{{ route('login') }}">Login</a>
    @if (Route::has('register'))
        <a href="{{ route('register') }}">Register</a>
    @endif
    @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}">Forgot</a>
    @endif
</div>
@endsection
