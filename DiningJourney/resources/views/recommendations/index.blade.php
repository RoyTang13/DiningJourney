@extends('layouts.app')

@section('title', $title.' - DiningJourney')

@section('content')
    <main class="content-page">
        <div class="page-heading">
            <p class="eyebrow">{{ $personalized ? 'Based on your preferences' : 'Popular picks in Kuala Lumpur' }}</p>
            <h1>{{ $title }}</h1>
            <p>{{ $personalized ? 'Your interests, location, and budget are used to rank these places.' : 'Set your preferences to get suggestions selected for you.' }}</p>
            @auth
                <a class="outline-button" href="{{ route('preferences.edit') }}">Edit preferences</a>
            @else
                <a class="outline-button" href="{{ route('register') }}">Create an account for personalised suggestions</a>
            @endauth
        </div>
        <div class="place-grid">
            @forelse ($places as $place)
                @include('components.place-card', ['place' => $place])
            @empty
                <p class="empty-state">No matches yet. Update your preferences to see more suggestions.</p>
            @endforelse
        </div>
    </main>
@endsection
