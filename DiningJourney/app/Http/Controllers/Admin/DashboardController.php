<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'userCount' => User::count(),
            'adminCount' => User::where('is_admin', true)->count(),
            'attractionCount' => Place::where('type', 'attraction')->count(),
            'restaurantCount' => Place::where('type', 'restaurant')->count(),
            'recentUsers' => User::latest()->take(5)->get(),
            'recentPlaces' => Place::latest()->take(5)->get(),
        ]);
    }
}
