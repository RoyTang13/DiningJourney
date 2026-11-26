<article class="place-card">
    <a class="card-image" href="{{ route('places.show', $place) }}">
        <img src="{{ $place->image_path }}" alt="{{ $place->name }}" loading="lazy">
        <span class="place-category">{{ $place->category }}</span>
    </a>
    @auth
        <form class="favorite-form" action="{{ route('favorites.toggle', $place) }}" method="post">
            @csrf
            <button class="heart-button {{ auth()->user()->favoritePlaces()->whereKey($place->id)->exists() ? 'active' : '' }}" type="submit" aria-label="Toggle {{ $place->name }} in favourites">
                {{ auth()->user()->favoritePlaces()->whereKey($place->id)->exists() ? '♥' : '♡' }}
            </button>
        </form>
    @else
        <a class="heart-button favorite-link" href="{{ route('login') }}" aria-label="Login to save {{ $place->name }}">♡</a>
    @endauth
    <h3><a href="{{ route('places.show', $place) }}">{{ $place->name }}</a></h3>
    <div class="card-meta">
        <span class="rating"><span aria-hidden="true">★</span> {{ number_format((float) $place->rating, 1) }} ({{ number_format($place->review_count) }}+)</span>
        <span>{{ $place->area }}</span>
    </div>
    @if (request()->routeIs('places.restaurants'))
        <label class="compare-option">
            <input type="checkbox" name="places[]" value="{{ $place->id }}" form="compare-places">
            <span>Compare</span>
        </label>
    @endif
</article>
