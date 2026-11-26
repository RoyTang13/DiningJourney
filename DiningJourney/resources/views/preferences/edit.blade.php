@extends('layouts.app')

@section('title', 'My preferences - DiningJourney')

@section('content')
    @php
        $travelOptions = ['culture', 'architecture', 'city views', 'family', 'outdoor', 'photography', 'nature', 'history'];
        $diningOptions = ['malaysian', 'local food', 'street food', 'halal', 'vegetarian', 'seafood', 'cafe', 'buffet'];
    @endphp
    <main class="content-page">
        <div class="page-heading">
            <p class="eyebrow">Tailor your journey</p>
            <h1>Travel & dining preferences</h1>
            <p>Choose what you enjoy. Recommendations will use your interests, preferred area, and budget.</p>
        </div>
        <form class="preferences-form" action="{{ route('preferences.update') }}" method="post">
            @csrf
            @method('put')
            @if ($errors->any())
                <div class="form-errors" role="alert">{{ $errors->first() }}</div>
            @endif
            <fieldset>
                <legend>Travel interests</legend>
                <div class="interest-grid">
                    @foreach ($travelOptions as $interest)
                        <label class="interest-option">
                            <input type="checkbox" name="travel_interests[]" value="{{ $interest }}" @checked(in_array($interest, old('travel_interests', $user->travel_interests ?? []), true))>
                            <span>{{ Str::headline($interest) }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>
            <fieldset>
                <legend>Dining interests</legend>
                <div class="interest-grid">
                    @foreach ($diningOptions as $interest)
                        <label class="interest-option">
                            <input type="checkbox" name="dining_interests[]" value="{{ $interest }}" @checked(in_array($interest, old('dining_interests', $user->dining_interests ?? []), true))>
                            <span>{{ Str::headline($interest) }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>
            <label class="form-field">Preferred dining area
                <input type="text" name="dining_location" value="{{ old('dining_location', $user->dining_location) }}" placeholder="For example, Bukit Bintang">
            </label>
            <label class="form-field">Restaurant budget
                <select name="budget">
                    <option value="">Any budget</option>
                    @foreach ([1 => 'Budget', 2 => 'Moderate', 3 => 'Premium', 4 => 'Luxury'] as $level => $label)
                        <option value="{{ $level }}" @selected((string) old('budget', $user->budget) === (string) $level)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <button class="filled-button" type="submit">Save preferences</button>
        </form>
    </main>
@endsection
