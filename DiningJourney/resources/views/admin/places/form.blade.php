@extends('admin.layouts.app')

@php
    $editing = $place->exists;
    $action = $editing
        ? route('admin.places.update', [$type, $place->id])
        : route('admin.places.store', $type);
@endphp

@section('title', ($editing ? 'Edit ' : 'Add ').rtrim($title, 's').' · DiningJourney')

@section('content')
    <header class="admin-page-heading">
        <div>
            <p class="eyebrow">{{ $title }} listings</p>
            <h1>{{ $editing ? 'Edit '.$place->name : 'Add '.rtrim($title, 's') }}</h1>
            <p>Complete the listing details shown to visitors.</p>
        </div>
        <a class="outline-button" href="{{ route('admin.places.index', $type) }}">← Back to {{ strtolower($title) }}</a>
    </header>
    <form class="admin-form" action="{{ $action }}" method="post">
        @csrf
        @if ($editing)
            @method('put')
        @endif
        <div class="admin-form-grid">
            <label>Name <input name="name" value="{{ old('name', $place->name) }}" maxlength="255" required></label>
            <label>URL slug <input name="slug" value="{{ old('slug', $place->slug) }}" maxlength="255" pattern="[A-Za-z0-9_-]+" required></label>
            <label>Category <input name="category" value="{{ old('category', $place->category) }}" maxlength="100" required></label>
            <label>Area <input name="area" value="{{ old('area', $place->area) }}" maxlength="150" required></label>
            <label class="form-span-2">Address <input name="address" value="{{ old('address', $place->address) }}" maxlength="500" required></label>
            <label class="form-span-2">Description <textarea name="description" rows="4" maxlength="5000" required>{{ old('description', $place->description) }}</textarea></label>
            <label class="form-span-2">Image URL or public path <input name="image_path" value="{{ old('image_path', $place->image_path) }}" maxlength="2048" placeholder="/images/place.jpg or https://..." required><small>Use an https:// URL or a file under public/images.</small></label>
            <label>Rating (0–5) <input name="rating" type="number" min="0" max="5" step="0.1" value="{{ old('rating', $place->rating) }}" required></label>
            <label>Review count <input name="review_count" type="number" min="0" step="1" value="{{ old('review_count', $place->review_count ?? 0) }}" required></label>
            <label>Price level
                <select name="price_level">
                    <option value="">Not listed</option>
                    @foreach ([1 => '$ · Budget', 2 => '$$ · Moderate', 3 => '$$$ · Premium', 4 => '$$$$ · Luxury'] as $level => $label)
                        <option value="{{ $level }}" @selected((string) old('price_level', $place->price_level) === (string) $level)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label>Tags <input name="tags" value="{{ old('tags', implode(', ', $place->tags ?? [])) }}" placeholder="culture, family, local food"></label>
            <label>Latitude <input name="latitude" type="number" min="-90" max="90" step="any" value="{{ old('latitude', $place->latitude) }}"></label>
            <label>Longitude <input name="longitude" type="number" min="-180" max="180" step="any" value="{{ old('longitude', $place->longitude) }}"></label>
        </div>
        <div class="admin-form-actions">
            <button class="filled-button" type="submit">{{ $editing ? 'Save changes' : 'Create listing' }}</button>
            <a class="outline-button" href="{{ route('admin.places.index', $type) }}">Cancel</a>
        </div>
    </form>
@endsection
