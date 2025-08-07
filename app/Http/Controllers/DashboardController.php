<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Office;
use Illuminate\Database\Eloquent\Builder;
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
        $totalEmployees = Employee::where('employment_type', '!=', 'Deleted')->count();
        $peCount = Employee::where('employment_type', 'PE')->count();
        $mrCount = Employee::where('employment_type', 'MR')->count();
        $deletedCount = Employee::where('employment_type', 'Deleted')->count();

        return inertia('Backend/Dashboard/Admin', [
            'totalEmployees' => $totalEmployees,
            'peCount' => $peCount,
            'mrCount' => $mrCount,
            'deletedCount' => $deletedCount,
        ]);
    }

    public function manager(Request $request)
    {
        return inertia('Backend/Dashboard/Manager', [
        ]);
    }



    public function officeStatistics(){
        $offices = Office::query()
            ->withCount([
                'employees as mr_count' => fn(Builder $q) => $q->where('employment_type', 'MR'),
                'employees as pe_count' => fn(Builder $q) => $q->where('employment_type', 'PE'),
            ])
            ->get();

        $labels = $offices->pluck('name');
        $mrCounts = $offices->pluck('mr_count');
        $peCounts = $offices->pluck('pe_count');

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Muster Roll (MR)',
                    'data' => $mrCounts,
                    'backgroundColor' => '#29ad3d',
                    'barThickness' => 8,
                    'borderRadius' => 4,
                    'categoryPercentage' => 0.6, // ⬅️ Lower = more space between bars
                ],
                [
                    'label' => 'Provisional (PE)',
                    'data' => $peCounts,
                    'backgroundColor' => '#1266ed',
                    'barThickness' => 8,
                    'borderRadius' => 4,
                    'categoryPercentage' => 0.6, // ⬅️ Lower = more space between bars

                ]
            ]
        ]);
    }
}
