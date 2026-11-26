@extends('layouts.app')

@section('title', 'My Favourites - DiningJourney')

@section('content')
    <main class="content-page">
        <div class="page-heading">
            <p class="eyebrow">Your saved places</p>
            <h1>My Favourites</h1>
            <p>Keep the attractions and restaurants you want to visit in one place.</p>
        </div>
        <div class="place-grid">
            @forelse ($places as $place)
                @include('components.place-card', ['place' => $place])
            @empty
                <p class="empty-state">You haven’t saved any places yet. Explore attractions or restaurants and tap the heart to save them.</p>
            @endforelse
        </div>
        <div class="pagination">{{ $places->links() }}</div>
    </main>
@endsection
