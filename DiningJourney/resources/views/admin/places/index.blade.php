@extends('admin.layouts.app')

@section('title', 'Manage '.$title.' · DiningJourney')

@section('content')
    <header class="admin-page-heading">
        <div>
            <p class="eyebrow">Place listings</p>
            <h1>Manage {{ $title }}</h1>
            <p>Add, update, and remove places displayed on DiningJourney.</p>
        </div>
        <a class="filled-button" href="{{ route('admin.places.create', $type) }}">＋ Add {{ rtrim($title, 's') }}</a>
    </header>
    <form class="admin-search" action="{{ route('admin.places.index', $type) }}" method="get">
        <label class="sr-only" for="place-search">Search {{ strtolower($title) }}</label>
        <input id="place-search" name="q" value="{{ request('q') }}" placeholder="Search by name, category, or area">
        <button class="filled-button" type="submit">Search</button>
        @if (request()->filled('q'))
            <a class="outline-button" href="{{ route('admin.places.index', $type) }}">Clear</a>
        @endif
    </form>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr><th>Place</th><th>Category</th><th>Area</th><th>Rating</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse ($places as $place)
                    <tr>
                        <td data-label="Place"><strong>{{ $place->name }}</strong><small>{{ $place->slug }}</small></td>
                        <td data-label="Category">{{ $place->category }}</td>
                        <td data-label="Area">{{ $place->area }}</td>
                        <td data-label="Rating">★ {{ number_format((float) $place->rating, 1) }}</td>
                        <td data-label="Actions">
                            <div class="admin-row-actions">
                                <a class="admin-text-button" href="{{ route('admin.places.edit', [$type, $place->id]) }}">Edit</a>
                                <form action="{{ route('admin.places.destroy', [$type, $place->id]) }}" method="post" onsubmit="return confirm('Delete {{ addslashes($place->name) }}? This cannot be undone.');">
                                    @csrf
                                    @method('delete')
                                    <button class="admin-text-button danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="admin-empty">No {{ strtolower($title) }} found. Add the first one to get started.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $places->links() }}</div>
@endsection
