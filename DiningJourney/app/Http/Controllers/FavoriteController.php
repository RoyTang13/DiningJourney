<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(Request $request): View
    {
        return view('favorites.index', [
            'places' => $request->user()->favoritePlaces()->orderBy('name')->paginate(9),
        ]);
    }

    public function toggle(Request $request, Place $place): RedirectResponse
    {
        $favorites = $request->user()->favoritePlaces();

        if ($favorites->whereKey($place->id)->exists()) {
            $favorites->detach($place->id);
            $message = 'Removed from your favourites.';
        } else {
            $favorites->attach($place->id);
            $message = 'Added to your favourites.';
        }

        return back()->with('status', $message);
    }
}
