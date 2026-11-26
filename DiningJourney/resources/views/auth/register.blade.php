@extends('layouts.app')

@section('title', 'Register - DiningJourney')

@section('content')
    <main class="content-page auth-page">
        <form class="account-form" action="{{ route('register.store') }}" method="post">
            @csrf
            <p class="eyebrow">Plan your next journey</p>
            <h1>Create an account</h1>
            @if ($errors->any())
                <div class="form-errors" role="alert">{{ $errors->first() }}</div>
            @endif
            <label>Name <input type="text" name="name" value="{{ old('name') }}" autocomplete="name" required></label>
            <label>Email address <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required></label>
            <label>Password <input type="password" name="password" autocomplete="new-password" required></label>
            <label>Confirm password <input type="password" name="password_confirmation" autocomplete="new-password" required></label>
            <button class="filled-button" type="submit">Register</button>
            <p>Already have an account? <a href="{{ route('login') }}">Login</a></p>
        </form>
    </main>
@endsection
