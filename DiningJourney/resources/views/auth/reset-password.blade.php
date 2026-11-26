@extends('layouts.app')

@section('title', 'Choose a new password - DiningJourney')

@section('content')
    <main class="content-page auth-page">
        <form class="account-form" action="{{ route('password.update') }}" method="post">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <p class="eyebrow">Account access</p>
            <h1>Choose a new password</h1>
            @if ($errors->any())
                <div class="form-errors" role="alert">{{ $errors->first() }}</div>
            @endif
            <label>Email address <input type="email" name="email" value="{{ old('email', $email) }}" autocomplete="email" required></label>
            <label>New password <input type="password" name="password" autocomplete="new-password" required></label>
            <label>Confirm new password <input type="password" name="password_confirmation" autocomplete="new-password" required></label>
            <button class="filled-button" type="submit">Update password</button>
        </form>
    </main>
@endsection
