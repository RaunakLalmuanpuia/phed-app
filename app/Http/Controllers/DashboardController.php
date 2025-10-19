<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Office;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        $user = auth()->user();

        $totalEmployees = Employee::where('employment_type', '!=', 'Deleted')->count();
        $wcCount = Employee::where('employment_type', 'WC')->count();
        $peCount = Employee::where('employment_type', 'PE')->count();
        $mrCount = Employee::where('employment_type', 'MR')->whereNull('scheme_id')->count();
        $schemeCount = Employee::where('employment_type', '!=', 'Deleted')->whereNotNull('scheme_id')->count();
        $deletedCount = Employee::where('employment_type', 'Deleted')->count();

        $notifications = [
            'editRequests' => 0,
            'transferRequests' => 0,
            'deletionRequests' => 0,
            'documentEditRequests' => 0,
            'documentDeleteRequests'=>0,
        ];

        if ($user && $user->hasRole('Admin')) {
            $statusFilter = ['pending'];

            $filterQuery = function ($q) {
                // Admin: no additional filtering
            };

            $notifications['editRequests'] = \App\Models\EditRequest::whereIn('approval_status', $statusFilter)
                ->where($filterQuery)
                ->count();

            $notifications['transferRequests'] = \App\Models\TransferRequest::whereIn('approval_status', $statusFilter)
                ->where($filterQuery)
                ->count();

            $notifications['deletionRequests'] = \App\Models\DeletionRequest::whereIn('approval_status', $statusFilter)
                ->where($filterQuery)
                ->count();

            $notifications['documentEditRequests'] = \App\Models\DocumentEditRequest::whereIn('approval_status', $statusFilter)
                ->where($filterQuery)
                ->count();
            $notifications['documentDeleteRequests'] = \App\Models\DocumentDeleteRequest::whereIn('approval_status', $statusFilter)
                ->where($filterQuery)
                ->count();
        }


        return inertia('Backend/Dashboard', [
            'totalEmployees' => $totalEmployees,
            'peCount' => $peCount,
            'mrCount' => $mrCount,
            'wcCount' => $wcCount,
            'schemeCount' => $schemeCount,
            'deletedCount' => $deletedCount,
            'notifications' => $notifications,
        ]);
    }
    //
//    public function index(Request $request)
//    {
//        $user = auth()->user();
//
//        if ($user->hasRole('Admin')) {
//            return to_route('dashboard.admin');
//        }
//
//        if ($user->hasRole('Manager')) {
//            return to_route('dashboard.manager');
//        }
//        return inertia('Dashboard', [
//
//        ]);
//    }

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
                'employees as wc_count' => fn(Builder $q) => $q->where('employment_type', 'WC'),
                'employees as pe_count' => fn(Builder $q) => $q->where('employment_type', 'PE'),
                'employees as mr_count' => fn(Builder $q) => $q->where('employment_type', 'MR')->whereNull('scheme_id'),

            ])
            ->get();

        $labels = $offices->pluck('name');
        $wcCounts = $offices->pluck('wc_count');
        $mrCounts = $offices->pluck('mr_count');
        $peCounts = $offices->pluck('pe_count');

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Work Charge (WC)',
                    'data' => $wcCounts,
                    'backgroundColor' => '#a859d9',
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

                ],
                [
                    'label' => 'Muster Roll (MR)',
                    'data' => $mrCounts,
                    'backgroundColor' => '#29ad3d',
                    'barThickness' => 8,
                    'borderRadius' => 4,
                    'categoryPercentage' => 0.6, // ⬅️ Lower = more space between bars
                ],
            ]
        ]);
    }
}
