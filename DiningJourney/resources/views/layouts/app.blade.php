<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'DiningJourney - Kuala Lumpur')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <header class="site-header">
        <nav class="navbar" aria-label="Main navigation">
            <a class="brand" href="{{ route('home') }}" aria-label="DiningJourney home">
                <span class="brand-mark" aria-hidden="true"></span>
                <span>DiningJourney</span>
            </a>
            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="nav-links">Menu</button>
            <div class="nav-links" id="nav-links">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('places.attractions') }}">Attractions</a>
                <a href="{{ route('places.restaurants') }}">Restaurants</a>
                <a href="{{ route('recommendations.index') }}">Recommendations</a>
            </div>
            <div class="nav-actions @auth has-profile-menu @endauth">
                @auth
                    @if (auth()->user()->isAdmin())
                        <a class="login-button" href="{{ route('admin.dashboard') }}">Admin</a>
                    @endif
                    <div class="profile-menu" data-profile-menu>
                        <button class="profile-menu-trigger" type="button"
                            aria-label="Open profile menu for {{ auth()->user()->name }}" aria-expanded="false"
                            aria-controls="profile-menu-dropdown">
                            <img src="{{ auth()->user()->profile_photo_path ? \Illuminate\Support\Facades\Storage::disk('public')->url(auth()->user()->profile_photo_path) : asset('images/default-avatar.svg') }}"
                                alt="">
                            <span class="profile-menu-name">{{ auth()->user()->name }}</span>
                            <svg aria-hidden="true" viewBox="0 0 16 16" fill="none">
                                <path d="m4 6 4 4 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                        <div class="profile-menu-dropdown" id="profile-menu-dropdown" hidden>
                            <a href="{{ route('profile.edit') }}">My Profile</a>
                            <a href="{{ route('favorites.index') }}">My Favourites</a>
                            <form class="profile-menu-logout" action="{{ route('logout') }}" method="post">
                                @csrf
                                <button type="submit">Logout</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a class="login-button" href="{{ route('login') }}">Login</a>
                    <a class="register-button" href="{{ route('register') }}">Register</a>
                @endauth
            </div>
        </nav>
    </header>

    @if (session('status'))
        <div class="flash-message" role="status">{{ session('status') }}</div>
    @endif

    @yield('content')

    <footer class="footer">
        <div class="footer-brand">
            <a class="brand" href="{{ route('home') }}"><span class="brand-mark"
                    aria-hidden="true"></span><span>DiningJourney</span></a>
            <p>Explore More. Eat Better.</p>
            <small class="photo-credit">Created by Leyi and Zhan Foong </small>
            <small>© {{ now()->year }} DiningJourney. All rights reserved.</small>
        </div>
        <div class="footer-links">
            <div>
                <h3>Quick Links</h3>
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('places.attractions') }}">Attractions</a>
                <a href="{{ route('places.restaurants') }}">Restaurants</a>
                <a href="{{ route('recommendations.index') }}">Recommendations</a>
            </div>
            <div>
                <h3>Account</h3>
                @auth
                    <a href="{{ route('profile.edit') }}">Profile</a>
                    <a href="{{ route('preferences.edit') }}">Preferences</a>
                    <a href="{{ route('favorites.index') }}">My Favourites</a>
                @else
                    <a href="{{ route('login') }}">Login</a>
                    <a href="{{ route('register') }}">Create an account</a>
                @endauth
            </div>
        </div>
    </footer>
</body>

</html>