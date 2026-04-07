<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role->name ?? 'Unknown';

        return view('dashboard.index', compact('user', 'role'));
    }

    public function attendance()
    {
        return view('dashboard.attendance');
    }

    public function logbook()
    {
        return view('dashboard.logbook');
    }

    public function activities()
    {
        return view('dashboard.activities');
    }

    public function documents()
    {
        return view('dashboard.documents');
    }

    public function reports()
    {
        return view('dashboard.reports');
    }
}
