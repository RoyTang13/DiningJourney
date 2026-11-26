@extends('layouts.app')

@section('title', $title.' - DiningJourney')

@section('content')
    <main class="content-page">
        <div class="page-heading">
            <p class="eyebrow">Kuala Lumpur</p>
            <h1>{{ $title }}</h1>
            <p>Search local places, narrow the list by area, type, and budget.</p>
        </div>
        <form class="filter-form" action="{{ route('places.'.($type === 'attraction' ? 'attractions' : 'restaurants')) }}" method="get">
            <label>Search <input name="q" value="{{ request('q') }}" placeholder="Name, category or keyword"></label>
            <label>Category
                <select name="category">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </label>
            <label>Area <input name="location" value="{{ request('location') }}" placeholder="Any area"></label>
            <label>Maximum budget
                <select name="budget">
                    <option value="">Any budget</option>
                    @foreach ([1 => 'Budget', 2 => 'Moderate', 3 => 'Premium', 4 => 'Luxury'] as $level => $label)
                        <option value="{{ $level }}" @selected((string) request('budget') === (string) $level)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <button class="filled-button" type="submit">Apply filters</button>
        </form>
        <p class="result-count">{{ $places->total() }} {{ $places->total() === 1 ? 'place' : 'places' }} found</p>
        @if ($type === 'restaurant')
            <form class="compare-form" id="compare-places" action="{{ route('places.compare') }}" method="get">
                <p>Select two or three restaurants from the results to compare them.</p>
                <button class="outline-button" type="submit">Compare selected</button>
            </form>
        @endif
        <div class="place-grid">
            @forelse ($places as $place)
                @include('components.place-card', ['place' => $place])
            @empty
                <p class="empty-state">No places matched those filters. Try changing your search.</p>
            @endforelse
        </div>
        <div class="pagination">{{ $places->links() }}</div>
    </main>
@endsection
