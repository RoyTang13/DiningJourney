@extends('layouts.app')

@section('title', 'My profile - DiningJourney')

@section('content')
    <main class="content-page">
        <div class="page-heading">
            <p class="eyebrow">Your account</p>
            <h1>Profile</h1>
            <p>Manage your account details and saved preferences.</p>
        </div>
        <section class="profile-photo-section" aria-labelledby="profile-photo-heading">
            <div class="profile-avatar-wrap">
                <img class="profile-avatar" data-profile-photo src="{{ $profilePhotoUrl }}" alt="{{ $user->name }}'s profile photo">
                <button class="profile-photo-camera" type="button" data-open-photo-editor aria-label="Edit profile photo" title="Edit profile photo">
                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none">
                        <path d="M4 8.5h3l1.5-2h7l1.5 2h3v10H4v-10Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        <circle cx="12" cy="13.5" r="3.2" stroke="currentColor" stroke-width="1.8"/>
                    </svg>
                </button>
            </div>
            <div class="profile-photo-copy">
                <h2 id="profile-photo-heading">Profile photo</h2>
                <p>Choose a JPG, PNG, or WEBP image (up to 10 MB).</p>
                <button class="outline-button" type="button" data-open-photo-editor>Edit Profile Photo</button>
                <p class="profile-photo-feedback" data-photo-feedback role="status" aria-live="polite"></p>
            </div>
        </section>
        <form class="visually-hidden" id="profile-photo-upload" data-upload-url="{{ route('profile.photo') }}" aria-hidden="true">
            @csrf
        </form>
        <input class="visually-hidden" id="profile-photo-input" type="file" accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp" aria-label="Choose profile photo">
        <dialog class="photo-editor-modal" id="photo-editor-modal" aria-labelledby="photo-editor-heading">
            <div class="photo-editor-content">
                <div class="photo-editor-heading">
                    <div>
                        <p class="eyebrow">Make it yours</p>
                        <h2 id="photo-editor-heading">Edit profile photo</h2>
                    </div>
                    <button class="photo-editor-close" type="button" data-cancel-photo-editor aria-label="Close photo editor">&times;</button>
                </div>
                <p class="photo-editor-hint">Drag the image to reposition it. Your selection stays square.</p>
                <div class="photo-crop-stage">
                    <img id="photo-crop-image" alt="Selected image to crop">
                </div>
                <div class="photo-editor-controls" aria-label="Photo zoom controls">
                    <button class="outline-button" type="button" data-zoom-photo="-1" aria-label="Zoom out">−</button>
                    <span>Zoom</span>
                    <button class="outline-button" type="button" data-zoom-photo="1" aria-label="Zoom in">+</button>
                    <button class="text-button" type="button" data-reset-photo>Reset crop</button>
                </div>
                <div class="photo-editor-preview-row">
                    <span>Circle preview</span>
                    <div class="photo-circle-preview" id="photo-circle-preview" aria-hidden="true"></div>
                </div>
                <p class="photo-editor-feedback" data-editor-feedback role="status" aria-live="polite"></p>
                <div class="photo-editor-actions">
                    <button class="outline-button" type="button" data-cancel-photo-editor>Cancel</button>
                    <button class="filled-button" type="button" data-save-photo>Save Photo</button>
                </div>
            </div>
        </dialog>
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
