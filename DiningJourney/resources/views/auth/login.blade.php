@extends('layouts.app')

@section('title', 'Login - DiningJourney')

@section('content')
    <main class="content-page auth-page">
        <form class="account-form" action="{{ route('login.store') }}" method="post">
            @csrf
            <p class="eyebrow">Welcome back</p>
            <h1>Login</h1>
            @if ($errors->any())
                <div class="form-errors" role="alert">{{ $errors->first() }}</div>
            @endif
            <label>Email address <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required></label>
            <label>Password <input type="password" name="password" autocomplete="current-password" required></label>
            <label class="checkbox-label"><input type="checkbox" name="remember" value="1"> Remember me</label>
            <button class="filled-button" type="submit">Login</button>
            <p><a href="{{ route('password.request') }}">Forgot your password?</a></p>
            <p>New to DiningJourney? <a href="{{ route('register') }}">Create an account</a></p>
        </form>
    </main>
@endsection
