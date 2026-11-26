@extends('admin.layouts.app')

@section('title', 'Admin dashboard · DiningJourney')

@section('content')
    <header class="admin-page-heading">
        <div>
            <p class="eyebrow">DiningJourney management</p>
            <h1>Dashboard</h1>
            <p>Manage user access and keep your destination listings up to date.</p>
        </div>
    </header>
    <section class="admin-stats" aria-label="Site totals">
        <a class="admin-stat" href="{{ route('admin.users.index') }}"><span>Total users</span><strong>{{ number_format($userCount) }}</strong></a>
        <a class="admin-stat" href="{{ route('admin.users.index', ['role' => 'admin']) }}"><span>Administrators</span><strong>{{ number_format($adminCount) }}</strong></a>
        <a class="admin-stat" href="{{ route('admin.places.index', 'attraction') }}"><span>Attractions</span><strong>{{ number_format($attractionCount) }}</strong></a>
        <a class="admin-stat" href="{{ route('admin.places.index', 'restaurant') }}"><span>Restaurants</span><strong>{{ number_format($restaurantCount) }}</strong></a>
    </section>
    <section class="admin-dashboard-grid">
        <article class="admin-panel">
            <div class="admin-panel-heading">
                <h2>Recent users</h2>
                <a href="{{ route('admin.users.index') }}">Manage users →</a>
            </div>
            <ul class="admin-recent-list">
                @forelse ($recentUsers as $user)
                    <li><span><strong>{{ $user->name }}</strong><small>{{ $user->email }}</small></span><span class="admin-role {{ $user->isAdmin() ? 'is-admin' : '' }}">{{ $user->isAdmin() ? 'Admin' : 'User' }}</span></li>
                @empty
                    <li>No users yet.</li>
                @endforelse
            </ul>
        </article>
        <article class="admin-panel">
            <div class="admin-panel-heading">
                <h2>Recently added places</h2>
                <a href="{{ route('admin.places.index', 'attraction') }}">Manage places →</a>
            </div>
            <ul class="admin-recent-list">
                @forelse ($recentPlaces as $place)
                    <li><span><strong>{{ $place->name }}</strong><small>{{ $place->area }}</small></span><span class="admin-role">{{ ucfirst($place->type) }}</span></li>
                @empty
                    <li>No places yet.</li>
                @endforelse
            </ul>
        </article>
    </section>
@endsection
