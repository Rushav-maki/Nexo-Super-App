<?php

namespace App\Http\Controllers;

use App\Models\CarBooking;
use App\Models\HotelBooking;
use App\Models\NexoPaisaTransaction;

class UserController extends Controller
{
    public function dashboard()
    {
        return view('user.dashboard');
    }

    public function profile()
    {
        $user = auth()->user();
        $carBookings = $user->carBookings()->with('car')->latest()->get();
        $hotelBookings = $user->hotelBookings()->latest()->get();
        $transactions = $user->nexoPaisaTransactions()->latest()->paginate(10);

        // Calculate transaction summary
        $totalLoaded = $user->nexoPaisaTransactions()->where('type', 'load')->sum('amount');
        $totalSpent = $user->nexoPaisaTransactions()->where('type', 'spend')->sum('amount');

        return view('user.profile', compact('user', 'carBookings', 'hotelBookings', 'transactions', 'totalLoaded', 'totalSpent'));
    }
}
