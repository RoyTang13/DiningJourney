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
                @auth
                    <a href="{{ route('favorites.index') }}">My Favourites</a>
                    <a href="{{ route('profile.edit') }}">Profile</a>
                @else
                    <a href="{{ route('login') }}">Profile</a>
                @endauth
            </div>
            <div class="nav-actions">
                @auth
                    <span class="nav-user">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button class="login-button" type="submit">Logout</button>
                    </form>
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
            <a class="brand" href="{{ route('home') }}"><span class="brand-mark" aria-hidden="true"></span><span>DiningJourney</span></a>
            <p>Explore More. Eat Better.</p>
            <small class="photo-credit">Hero photo cropped: <a href="https://commons.wikimedia.org/wiki/File:The_Twins_SE_Asia_2019_(49171985716).jpg" target="_blank" rel="noopener noreferrer">James Kerwin</a> · <a href="https://creativecommons.org/licenses/by/2.0/" target="_blank" rel="noopener noreferrer">CC BY 2.0</a></small>
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
