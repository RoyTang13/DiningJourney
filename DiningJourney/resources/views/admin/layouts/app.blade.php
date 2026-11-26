<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin · DiningJourney')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-body">
    <header class="admin-header">
        <a class="brand" href="{{ route('admin.dashboard') }}"><span class="brand-mark" aria-hidden="true"></span><span>DiningJourney Admin</span></a>
        <a class="admin-view-site" href="{{ route('home') }}">View site ↗</a>
    </header>
    <div class="admin-shell">
        <nav class="admin-nav" aria-label="Admin navigation">
            <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">Users</a>
            <a class="{{ request()->routeIs('admin.places.*') && request()->route('type') === 'attraction' ? 'active' : '' }}" href="{{ route('admin.places.index', 'attraction') }}">Attractions</a>
            <a class="{{ request()->routeIs('admin.places.*') && request()->route('type') === 'restaurant' ? 'active' : '' }}" href="{{ route('admin.places.index', 'restaurant') }}">Restaurants</a>
            <a class="{{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}">My profile</a>
        </nav>
        <main class="admin-main">
            @if (session('status'))
                <div class="flash-message" role="status">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="admin-errors" role="alert">
                    <strong>Please check the following:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>
