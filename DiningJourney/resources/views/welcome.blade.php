@extends('layouts.app')

@section('title', 'DiningJourney - Kuala Lumpur')

@section('content')
    <header class="hero" id="home">
        <div class="hero-content">
            <div class="hero-copy">
                <h1>Discover<br>Kuala Lumpur,<br>Your Way</h1>
                <p class="hero-script">Explore. Taste. Experience.</p>
                <p class="hero-description">Find the best attractions and restaurants<br>in Kuala Lumpur with personalised recommendations.</p>
            </div>
            <p class="hero-note" aria-label="Kuala Lumpur Awaits">Kuala Lumpur<br><em>Awaits!</em></p>
        </div>
        <form class="search-bar" action="{{ route('places.search') }}" method="get">
            <div class="search-tabs" role="group" aria-label="Search category">
                <button class="search-tab active" type="button" data-category="attraction"><span aria-hidden="true">⌖</span> Attractions</button>
                <button class="search-tab" type="button" data-category="restaurant"><span aria-hidden="true">♨</span> Restaurants</button>
            </div>
            <input type="hidden" name="type" value="attraction">
            <label class="sr-only" for="search">Search attractions or restaurants</label>
            <input id="search" name="q" type="search" placeholder="Search for attractions or restaurants...">
            <button class="search-submit" type="submit" aria-label="Search">⌕</button>
        </form>
    </header>

    <main>
        <section class="category-strip" aria-label="Explore categories">
            @foreach (['Nature & Parks', 'Culture & Heritage', 'Shopping', 'Entertainment'] as $category)
                <a class="category-item" href="{{ route('places.attractions', ['category' => $category]) }}">
                    <span class="category-icon" aria-hidden="true">{{ ['Nature & Parks' => '♧', 'Culture & Heritage' => '⌂', 'Shopping' => '▣', 'Entertainment' => '✦'][$category] }}</span>
                    <span>{{ $category }}</span>
                </a>
            @endforeach
            <a class="category-item" href="{{ route('places.restaurants') }}">
                <span class="category-icon" aria-hidden="true">♨</span>
                <span>Food & Dining</span>
            </a>
            <a class="category-item" href="{{ route('places.attractions') }}">
                <span class="category-icon" aria-hidden="true">▦</span>
                <span>Others</span>
            </a>
        </section>

        <section class="listing-section" id="attractions">
            <div class="listing-copy attraction-copy">
                <h2>Explore<br>Attractions</h2>
                <p>Discover iconic landmarks and hidden gems in Kuala Lumpur.</p>
                <a class="outline-button" href="{{ route('places.attractions') }}">Explore All <span>→</span></a>
            </div>
            <div class="card-carousel">
                @forelse ($attractions as $place)
                    @include('components.place-card', ['place' => $place])
                @empty
                    <p class="empty-state">Attractions are coming soon.</p>
                @endforelse
            </div>
        </section>

        <section class="listing-section restaurant-section" id="restaurants">
            <div class="listing-copy restaurant-copy">
                <h2>Discover<br>Restaurants</h2>
                <p>Savour the best food in Kuala Lumpur.</p>
                <a class="filled-button" href="{{ route('places.restaurants') }}">Explore All <span>→</span></a>
            </div>
            <div class="card-carousel">
                @forelse ($restaurants as $place)
                    @include('components.place-card', ['place' => $place])
                @empty
                    <p class="empty-state">Restaurants are coming soon.</p>
                @endforelse
            </div>
        </section>

        <section class="recommendation-section" id="recommendations">
            <h2><span aria-hidden="true">☆</span> Personalised Recommendations</h2>
            <p>Get tailored suggestions based on your preferences!</p>
            <div class="recommendation-cards">
                <article class="recommendation-card">
                    <img src="/images/kuala-lumpur-skyline.jpg" alt="Kuala Lumpur city skyline" loading="lazy">
                    <div>
                        <h3>Recommended Attractions</h3>
                        <p>Explore attractions that match your interests and travel style.</p>
                        <a href="{{ route('recommendations.index', ['type' => 'attraction']) }}">Get Attraction Recommendations →</a>
                    </div>
                </article>
                <article class="recommendation-card">
                    <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=700&q=85" alt="Local Malaysian meal" loading="lazy">
                    <div>
                        <h3>Recommended Restaurants</h3>
                        <p>Discover dining spots that suit your taste and budget.</p>
                        <a href="{{ route('recommendations.index', ['type' => 'restaurant']) }}">Get Restaurant Recommendations →</a>
                    </div>
                </article>
            </div>
        </section>
    </main>
@endsection
