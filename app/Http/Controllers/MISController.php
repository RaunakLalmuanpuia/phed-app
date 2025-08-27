<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Office;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EmployeesImport;
use Carbon\Carbon;
class MISController extends Controller
{

    public function import(Request $request){

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('import-employee'),403,'Access Denied');

        $office = Office::all();

        return Inertia::render('Backend/MIS/Import', [
            'office' => $office,
            'canImport'=>$user->can('import-employee'),
        ]);
    }

    public function importEmployee(Request $request){

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('import-employee'),403,'Access Denied');

        $request->validate([
            'document' => 'required|file|mimes:xlsx,csv,xls',
            'office' => 'required|integer|exists:offices,id',
            'type' => 'required|string|in:MR,PE',
        ]);
        Excel::import(new EmployeesImport($request->office, $request->type), $request->file('document'));

        return redirect()->back()->with('message','Employee data imported successfully');

    }

    public function export(Request $request){

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('export-employee'),403,'Access Denied');

        $office = Office::all();

        return Inertia::render('Backend/MIS/Export', [
            'office' => $office,
            'canExport'=>$user->can('export-employee'),
        ]);
    }

    public function exportEmployee(Request $request){

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('export-employee'),403,'Access Denied');

        $request->validate([

            'office' => 'required|integer|exists:offices,id',
            'type' => 'required|string|in:MR,PE',
        ]);

        return redirect()->back()->with('message','Employee data imported successfully');

    }

    public function remuneration(Request $request){
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('generate-remuneration'),403,'Access Denied');

        $office = Office::all();



        return Inertia::render('Backend/MIS/Remuneration', [
            'office' => $office,
            'canGenerateRemuneration'=>$user->can('generate-remuneration'),
        ]);
    }

    public function jsonRemuneration(Request $request)
    {
        $perPage = $request->integer('rowsPerPage', 5);
        $filter  = $request->get('filter', []);
        $search  = $request->get('search');
        $officeIds = $filter['offices'] ?? [];

        $employees = Employee::with(['office','remunerationDetail'])
            ->whereIn('office_id', (array) $officeIds)
            ->where('employment_type', 'PE')

            // Search
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('mobile', 'LIKE', "%{$search}%")
                        ->orWhere('designation', 'LIKE', "%{$search}%")
                        ->orWhere('date_of_birth', 'LIKE', "%{$search}%")
                        ->orWhere('name_of_workplace', 'LIKE', "%{$search}%")
                        ->orWhereHas('remunerationDetail', function ($rem) use ($search) {
                            $rem->where('remuneration', 'LIKE', "%{$search}%")
                                ->orWhere('medical_amount', 'LIKE', "%{$search}%")
                                ->orWhere('total', 'LIKE', "%{$search}%")
                                ->orWhere('round_total', 'LIKE', "%{$search}%");
                        });
                });
            })

            // Extra filters
            ->when($filter['designation'] ?? null, function ($query, $designation) {
                $query->where('designation', $designation);
            })
            ->when($filter['education_qln'] ?? null, function ($query, $educationQln) {
                $query->where('educational_qln', $educationQln);
            })

            // Sort by closest next_increment_date to today
            ->orderByRaw("
            (
                SELECT MIN(ABS(DATEDIFF(rd.next_increment_date, CURDATE())))
                FROM remuneration_details rd
                WHERE rd.employee_id = employees.id
            )
        ");

        return response()->json([
            'list' => $employees->paginate($perPage),
        ], 200);
    }





    public function engagementCard(Request $request){
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('generate-engagement-card'),403,'Access Denied');

        $office = Office::all();

        return Inertia::render('Backend/MIS/EngagementCard', [
            'office' => $office,
            'canGenerateEngagementCard'=>$user->can('generate-engagement-card'),
        ]);
    }
}
