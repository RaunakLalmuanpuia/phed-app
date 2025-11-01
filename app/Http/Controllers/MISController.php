<?php

namespace App\Http\Controllers;

use App\Imports\EmployeeEngagementImport;
use App\Imports\WorkchargeEmployeesImport;
use App\Models\DocumentType;
use App\Models\Employee;
use App\Models\Office;
use App\Models\Scheme;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EmployeesImport;
use Carbon\Carbon;
class MISController extends Controller
{


    public function createWC(Request $request){

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('create-employee'),403,'Access Denied');

        $documentTypes = DocumentType::all();
        $offices = Office::all();

        return Inertia::render('Backend/Employees/Create/WC', [
            'documentTypes' => $documentTypes,
            'offices' => $offices,
            'canCreate'=>$user->can('create-employee'),
        ]);
    }

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
        $schemes= Scheme::all();

        return Inertia::render('Backend/Employees/Create/MR', [
            'documentTypes' => $documentTypes,
            'offices' => $offices,
            'schemes' => $schemes,
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

    public function importWorkCharge(Request $request){

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('import-employee'),403,'Access Denied');

        $office = Office::all();

        return Inertia::render('Backend/MIS/ImportWorkCharge', [
            'office' => $office,
            'canImport'=>$user->can('import-employee'),
        ]);
    }

    public function importWorkChargeEmployee(Request $request)
    {
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('import-employee'), 403, 'Access Denied');

        $request->validate([
            'document' => 'required|file|mimes:xlsx,csv,xls',
            'office' => 'required|integer|exists:offices,id',
        ]);

        Excel::import(new WorkchargeEmployeesImport($request->office), $request->file('document'));

        return redirect()->back()->with('message', 'Work-Charge employee data imported successfully');
    }

    public function importDateOfEngagement(Request $request){

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('import-employee'),403,'Access Denied');



        return Inertia::render('Backend/MIS/ImportEngagement', [
            'canImport'=>$user->can('import-employee'),
        ]);
    }

    public function importDateOfEngagementEmployee(Request $request)
    {
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('import-employee'), 403, 'Access Denied');

        $request->validate([
            'document' => 'required|mimes:xlsx,csv',
        ]);

        Excel::import(new EmployeeEngagementImport(), $request->file('document'));

        return back()->with('success', 'Employee engagement dates updated successfully!');
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

    public function engagementCard(Request $request){
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('generate-engagement-card'),403,'Access Denied');

        $office = Office::whereHas('employees', function($query) {
            $query->where('employment_type', 'PE');
        })->get();


        return Inertia::render('Backend/MIS/EngagementCard', [
            'office' => $office,
            'canGenerateEngagementCard'=>$user->can('generate-engagement-card'),
            'canCreateEngagementCard' => $user->can('store-engagement-card'),
            'canDownloadEngagementCard' => $user->can('download-engagement-card'),
            'canDeleteEngagementCard' => $user->can('delete-engagement-card'),

        ]);
    }

    public function jsonEngagementCard(Request $request)
    {
        $perPage = $request->integer('rowsPerPage', 5);
        $filter  = $request->get('filter', []);
        $search  = $request->get('search');
        $officeIds = $filter['offices'] ?? [];
        $startYear = $filter['startYear']; // always provided
        $endYear   = $filter['endYear'];   // always provided

        // Compute fiscal year
        $fiscalYear = $this->getFiscalYear($startYear . '-03-01', $endYear . '-02-28');

        $employees = Employee::with([
            'office',
            'engagementCard' => function($q) use ($fiscalYear) {
                // Only load the card for the selected fiscal year
                $q->where('fiscal_year', $fiscalYear)->latest();
            }
        ])
            ->whereIn('office_id', (array) $officeIds)
            ->where('employment_type', 'PE')
            ->when($search, function ($q) use ($search, $fiscalYear) {
                $q->where(function ($sub) use ($search, $fiscalYear) {
                    $sub->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('mobile', 'LIKE', "%{$search}%")
                        ->orWhere('designation', 'LIKE', "%{$search}%")
                        ->orWhere('date_of_birth', 'LIKE', "%{$search}%")
                        ->orWhere('name_of_workplace', 'LIKE', "%{$search}%")
                        ->orWhereHas('engagementCard', function ($card) use ($search, $fiscalYear) {
                            $card->where('fiscal_year', $fiscalYear)
                                ->where('card_no', 'LIKE', "%{$search}%");
                        });
                });
            })
            ->orderBy('name', 'asc')
            ->paginate($perPage);

        return response()->json([
            'list' => $employees,
        ], 200);
    }


    public function employeeDocument(Request $request){
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('update-document'),403,'Access Denied');

        $office = Office::all();
        $documentTypes = DocumentType::all();

        return Inertia::render('Backend/MIS/Document', [
            'office' => $office,
            'documentTypes' => $documentTypes,
            'canUpdateDocument'=>$user->can('update-document'),
        ]);
    }


    public function jsonEmployeeDocument(Request $request){
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('update-document'),403,'Access Denied');

        $perPage = $request->integer('rowsPerPage', 5);
        $filter  = $request->get('filter', []);
        $search  = $request->get('search');
        $officeIds = $filter['offices'] ?? [];


        $employees = Employee::with(['office','documents.type','scheme'])
            ->whereIn('office_id', (array) $officeIds)
            ->where('employment_type', '!=', 'Deleted')
            ->when($filter['type'] ?? null, function ($query, $type) {
                $query->where('employment_type', $type);
            })
            ->when($filter['scheme'] ?? null, function ($query, $scheme) {
                if ($scheme === 'yes') {
                    // Scheme is true - employees with scheme_id NOT NULL
                    $query->whereNotNull('scheme_id');
                } else {
                    // Scheme is false - employees with scheme_id NULL
                    $query->whereNull('scheme_id');
                }
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('mobile', 'LIKE', "%{$search}%")
                        ->orWhere('designation', 'LIKE', "%{$search}%")
                        ->orWhere('date_of_birth', 'LIKE', "%{$search}%")
                        ->orWhere('name_of_workplace', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('name', 'asc')
            ->paginate($perPage);

        return response()->json([
            'list' => $employees,
        ], 200);

    }

    public function managerRemuneration(Request $request){

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('view-remuneration'),403,'Access Denied');

        $offices = $user->offices()
            ->get(['offices.name as label', 'offices.id as value'])
            ->map(function ($office) {
                return [
                    'label' => $office->label,
                    'value' => $office->value,
                ];
            });

        return Inertia::render('Backend/MIS/Manager/Remuneration', [
            'offices' => $offices,
            'canViewAllEmployee'=>$user->can('view-allemployee'),
            'canExportRemunerationSummary'=>$user->can('export-remuneration-summary'),
        ]);

    }

    public function jsonManagerRemuneration(Request $request)
    {
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('view-remuneration'),403,'Access Denied');

        $user = $request->user();

        // If you allow multiple offices per user, decide which one
        // Here: pick the first office
        $office = $user->offices()->first();

        if (!$office) {
            return response()->json([
                'message' => 'No office assigned to this user.',
                'data' => [],
                'totals' => [],
            ], 404);
        }

        // Fetch employees (only PE)
        $employees = $office->employees()
            ->where('employment_type', 'PE')
            ->with('remunerationDetail')
            ->get();

        $rows = $employees->map(function ($emp, $index) {
            $rem = optional($emp->remunerationDetail);

            return [
                'sl_no'            => $index + 1,
                'name'             => $emp->name,
                'designation'      => $emp->designation,
                'remuneration'     => $rem->remuneration ?? 0,
                'medical_allowance'=> $rem->medical_amount ?? 0,
                'total'            => $rem->total ?? 0,
                'monthly_rem'      => $rem->round_total ?? 0,
                'next_increment'   => $rem->next_increment_date
                    ? \Carbon\Carbon::parse($rem->next_increment_date)->format('d.m.Y')
                    : '',
                'pay_matrix' => $rem->pay_matrix ?? null,
            ];
        });

        // Compute totals
        $totals = [
            'remuneration'      => $rows->sum('remuneration'),
            'medical_allowance' => $rows->sum('medical_allowance'),
            'total'             => $rows->sum('total'),
            'monthly_rem'       => $rows->sum('monthly_rem'),
        ];

        $offices = $user->offices()
            ->get(['offices.name as label', 'offices.id as value'])
            ->map(function ($office) {
                return [
                    'label' => $office->label,
                    'value' => $office->value,
                ];
            });

        return response()->json([
           'offices' => $offices,
            'data' => $rows,
            'totals' => $totals,
        ], 200);
    }





    public function managerEngagementCard(Request $request){

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('download-engagement-card'),403,'Access Denied');

        $offices = $user->offices()
            ->get(['offices.name as label', 'offices.id as value'])
            ->map(function ($office) {
                return [
                    'label' => $office->label,
                    'value' => $office->value,
                ];
            });

        return Inertia::render('Backend/MIS/Manager/EngagementCard', [
            'offices' => $offices,
            'canDownloadEngagementCard'=>$user->can('download-engagement-card'),
        ]);

    }

    public function jsonManagerEngagementCard(Request $request)
    {
        $user = $request->user();

        $perPage = $request->integer('rowsPerPage', 5);
        $filter  = $request->get('filter', []);
        $search  = $request->get('search');
        $startYear = $filter['startYear']; // always provided
        $endYear   = $filter['endYear'];   // always provided

        // Compute fiscal year
        $fiscalYear = $this->getFiscalYear($startYear . '-03-01', $endYear . '-02-28');

        // 🔹 Instead of filter from request, use current user's offices
        $officeIds = $user->offices()->pluck('offices.id')->toArray();

        $employees = Employee::with([
            'office',
            'engagementCard' => function($q) use ($fiscalYear) {
                // Only load the card for the selected fiscal year
                $q->where('fiscal_year', $fiscalYear)->latest();
            }
        ])
            ->whereIn('office_id', $officeIds)  // now using user's offices
            ->where('employment_type', 'PE')
            ->when($search, function ($q) use ($search, $fiscalYear) {
                $q->where(function ($sub) use ($search, $fiscalYear) {
                    $sub->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('mobile', 'LIKE', "%{$search}%")
                        ->orWhere('designation', 'LIKE', "%{$search}%")
                        ->orWhere('date_of_birth', 'LIKE', "%{$search}%")
                        ->orWhere('name_of_workplace', 'LIKE', "%{$search}%")
                        ->orWhereHas('engagementCard', function ($card) use ($search, $fiscalYear) {
                            $card->where('fiscal_year', $fiscalYear)
                                ->where('card_no', 'LIKE', "%{$search}%");
                        });
                });
            })
            ->orderBy('name', 'asc')
            ->paginate($perPage);

        return response()->json([
            'list' => $employees,
        ], 200);
    }




}
