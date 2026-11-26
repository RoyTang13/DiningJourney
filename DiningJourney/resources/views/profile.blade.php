@extends('layouts.app')

@section('title', 'My profile - DiningJourney')

@section('content')
    <main class="content-page">
        <div class="page-heading">
            <p class="eyebrow">Your account</p>
            <h1>Profile</h1>
            <p>Manage your account details and saved preferences.</p>
        </div>
        <form class="account-form profile-form" action="{{ route('profile.update') }}" method="post">
            @csrf
            @method('patch')
            @if ($errors->any())
                <div class="form-errors" role="alert">{{ $errors->first() }}</div>
            @endif
            <label>Name <input type="text" name="name" value="{{ old('name', $user->name) }}" autocomplete="name" required></label>
            <label>Email address <input type="email" name="email" value="{{ old('email', $user->email) }}" autocomplete="email" required></label>
            <button class="filled-button" type="submit">Save profile</button>
        </form>
        <form class="account-form profile-form" action="{{ route('profile.password') }}" method="post">
            @csrf
            @method('put')
            <h2>Change password</h2>
            @if ($errors->any())
                <div class="form-errors" role="alert">{{ $errors->first() }}</div>
            @endif
            <label>Current password <input type="password" name="current_password" autocomplete="current-password" required></label>
            <label>New password <input type="password" name="password" autocomplete="new-password" required></label>
            <label>Confirm new password <input type="password" name="password_confirmation" autocomplete="new-password" required></label>
            <button class="filled-button" type="submit">Update password</button>
        </form>
        <div class="account-links">
            <a class="outline-button" href="{{ route('preferences.edit') }}">Manage preferences</a>
            <a class="outline-button" href="{{ route('favorites.index') }}">View my favourites</a>
        </div>
    </main>
@endsection
