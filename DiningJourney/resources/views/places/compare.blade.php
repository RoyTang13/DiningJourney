@extends('layouts.app')

@section('title', 'Compare restaurants - DiningJourney')

@section('content')
    <main class="content-page">
        <div class="page-heading">
            <p class="eyebrow">Choose your next meal</p>
            <h1>Compare restaurants</h1>
            <p>Review ratings, location, cuisine, and price side by side.</p>
        </div>
        <div class="comparison-grid" style="--comparison-count: {{ $places->count() }}">
            @foreach ($places as $place)
                <article class="comparison-card">
                    <img src="{{ $place->image_path }}" alt="{{ $place->name }}" loading="lazy">
                    <h2>{{ $place->name }}</h2>
                    <dl>
                        <div><dt>Rating</dt><dd>★ {{ number_format((float) $place->rating, 1) }} ({{ number_format($place->review_count) }} reviews)</dd></div>
                        <div><dt>Cuisine / type</dt><dd>{{ $place->category }}</dd></div>
                        <div><dt>Area</dt><dd>{{ $place->area }}</dd></div>
                        <div><dt>Price</dt><dd>{{ $place->price_level ? str_repeat('$', $place->price_level) : 'Not listed' }}</dd></div>
                    </dl>
                    <a class="outline-button" href="{{ route('places.show', $place) }}">View details</a>
                </article>
            @endforeach
        </div>
    </main>
@endsection
