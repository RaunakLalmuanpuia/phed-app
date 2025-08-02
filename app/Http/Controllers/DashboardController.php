<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('Admin')) {
            return to_route('dashboard.admin');
        }

        if ($user->hasRole('Manager')) {
            return to_route('dashboard.manager');
        }
        return inertia('Dashboard', [

        ]);
    }

    public function admin(Request $request)
    {
        return inertia('Backend/Dashboard/Admin', [
        ]);
    }

    public function manager(Request $request)
    {
        return inertia('Dashboard/Manager', [
        ]);
    }
}
