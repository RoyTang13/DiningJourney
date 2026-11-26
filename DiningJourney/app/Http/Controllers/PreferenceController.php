<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PreferenceController extends Controller
{
    public function edit(Request $request): View
    {
        return view('preferences.edit', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'travel_interests' => ['nullable', 'array'],
            'travel_interests.*' => ['string', 'max:40'],
            'dining_interests' => ['nullable', 'array'],
            'dining_interests.*' => ['string', 'max:40'],
            'dining_location' => ['nullable', 'string', 'max:100'],
            'budget' => ['nullable', 'integer', 'between:1,4'],
        ]);

        $request->user()->update([
            'travel_interests' => $data['travel_interests'] ?? [],
            'dining_interests' => $data['dining_interests'] ?? [],
            'dining_location' => $data['dining_location'] ?? null,
            'budget' => $data['budget'] ?? null,
        ]);

        return back()->with('status', 'Your preferences have been saved.');
    }
}
