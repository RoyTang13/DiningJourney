@extends('layouts.app')

@section('title', 'Reset password - DiningJourney')

@section('content')
    <main class="content-page auth-page">
        <form class="account-form" action="{{ route('password.email') }}" method="post">
            @csrf
            <p class="eyebrow">Account access</p>
            <h1>Reset your password</h1>
            @if ($errors->any())
                <div class="form-errors" role="alert">{{ $errors->first() }}</div>
            @endif
            <label>Email address <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required></label>
            <button class="filled-button" type="submit">Email reset link</button>
            <p><a href="{{ route('login') }}">Back to login</a></p>
        </form>
    </main>
@endsection
