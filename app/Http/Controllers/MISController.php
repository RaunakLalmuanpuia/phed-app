<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use App\Models\Employee;
use App\Models\Office;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EmployeesImport;
use Carbon\Carbon;
class MISController extends Controller
{

    public function createPE(Request $request){

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('create-employee'),403,'Access Denied');

        $documentTypes = DocumentType::all();
        $offices = Office::all();

        return Inertia::render('Backend/Employees/Create/PE', [
            'documentTypes' => $documentTypes,
            'offices' => $offices,
            'canCreate'=>$user->can('create-employee'),
        ]);
    }
    public function createMR(Request $request){

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('create-employee'),403,'Access Denied');

        $documentTypes = DocumentType::all();
        $offices = Office::all();

        return Inertia::render('Backend/Employees/Create/MR', [
            'documentTypes' => $documentTypes,
            'offices' => $offices,
            'canCreate'=>$user->can('create-employee'),
        ]);
    }

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

        $office = Office::whereHas('employees', function($query) {
            $query->where('employment_type', 'PE');
        })->get();



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
        $incrementYear = $filter['incrementYear'] ?? null;

        $employees = Employee::with(['office','remunerationDetail'])
            ->whereIn('office_id', (array) $officeIds)
            ->where('employment_type', 'PE')

            // 🔹 Filter by Increment Year
            ->when($incrementYear, function ($q) use ($incrementYear) {
                $q->whereHas('remunerationDetail', function ($rem) use ($incrementYear) {
                    $rem->whereYear('next_increment_date', $incrementYear);
                });
            })

            // 🔹 Search
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

            // 🔹 Sort by closest increment date
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

        $office = Office::whereHas('employees', function($query) {
            $query->where('employment_type', 'PE');
        })->get();


        return Inertia::render('Backend/MIS/EngagementCard', [
            'office' => $office,
            'canGenerateEngagementCard'=>$user->can('generate-engagement-card'),
        ]);
    }
    public function jsonEngagementCard(Request $request)
    {
        $perPage = $request->integer('rowsPerPage', 5);
        $filter  = $request->get('filter', []);
        $search  = $request->get('search');
        $officeIds = $filter['offices'] ?? [];
        $startYear = $filter['startYear'] ?? null;
        $endYear   = $filter['endYear'] ?? null;

        $employees = Employee::with(['office', 'engagementCard' => function($q) use ($startYear, $endYear) {
            if ($startYear) $q->whereYear('start_date', '>=', $startYear);
            if ($endYear) $q->whereYear('end_date', '<=', $endYear);
        }])
            ->whereIn('office_id', (array) $officeIds)
            ->where('employment_type', 'PE')

// 🔹 No need to filter employees themselves by engagement card year
// Employees are included regardless of having cards in that period
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('mobile', 'LIKE', "%{$search}%")
                        ->orWhere('designation', 'LIKE', "%{$search}%")
                        ->orWhere('date_of_birth', 'LIKE', "%{$search}%")
                        ->orWhere('name_of_workplace', 'LIKE', "%{$search}%")
                        ->orWhereHas('engagementCard', function ($card) use ($search) {
                            $card->where('card_no', 'LIKE', "%{$search}%");
                        });
                });
            });


        return response()->json([
            'list' => $employees->paginate($perPage),
        ], 200);
    }



}
