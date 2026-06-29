@extends('layouts.app')

@section('title', 'Confirm Password')

@section('content')
<div class="auth-heading">
    <h1>Confirm access</h1>
    <p>Please confirm your password before continuing</p>
</div>

<form method="POST" action="{{ route('password.confirm') }}" class="auth-form">
    @csrf

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

    <button type="submit" class="auth-submit">Confirm password</button>
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
