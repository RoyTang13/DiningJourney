@extends('layouts.app')

@section('title', $place->name.' - DiningJourney')

@section('content')
    <main class="content-page">
        <article class="place-detail">
            <img class="detail-image" src="{{ $place->image_path }}" alt="{{ $place->name }}">
            <div class="detail-copy">
                <p class="eyebrow">{{ ucfirst($place->type) }} · {{ $place->category }}</p>
                <h1>{{ $place->name }}</h1>
                <p class="detail-rating"><span>★</span> {{ number_format((float) $place->rating, 1) }} · {{ number_format($place->review_count) }} reviews</p>
                <p>{{ $place->description }}</p>
                <dl class="place-facts">
                    <div><dt>Area</dt><dd>{{ $place->area }}</dd></div>
                    <div><dt>Address</dt><dd>{{ $place->address }}</dd></div>
                    @if ($place->price_level)
                        <div><dt>Budget</dt><dd>{{ str_repeat('$', $place->price_level) }}</dd></div>
                    @endif
                </dl>
                @if ($place->latitude && $place->longitude)
                    <a class="filled-button" href="https://www.google.com/maps/dir/?api=1&amp;destination={{ $place->latitude }},{{ $place->longitude }}" target="_blank" rel="noopener noreferrer">Get directions →</a>
                @endif
                @auth
                    <form class="detail-favorite" action="{{ route('favorites.toggle', $place) }}" method="post">
                        @csrf
                        <button class="outline-button" type="submit">Save to favourites</button>
                    </form>
                @else
                    <a class="outline-button" href="{{ route('login') }}">Login to save this place</a>
                @endauth
            </div>
        </article>
    </main>
@endsection
