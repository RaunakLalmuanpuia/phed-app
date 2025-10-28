<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use App\Models\Employee;
use App\Models\Office;
use App\Models\Scheme;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Document;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{

    public function allEmployees(Request $request)
    {

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-allemployee'), 403, 'Access Denied');

        $search = $request->get('search');
        // Offices with at least one employee, plus MR & PE counts
        $offices = Office::withCount([
            'employees as mr_count' => function ($query) {
                $query->where('employment_type', 'MR')
                    ->whereNull('scheme_id');
            },
            'employees as pe_count' => function ($query) {
                $query->where('employment_type', 'PE');
            },
            'employees as wc_count' => function ($query) {
                $query->where('employment_type', 'WC');
            },
        ])->when($search, function ($query) use ($search) {
            $query->where('name', 'LIKE', "%{$search}%"); // ✅ search by office name
        })->get();

        $totalEmployees = Employee::where('employment_type', '!=', 'Deleted')->whereNull('scheme_id')->count();
        $wcCount = Employee::where('employment_type', 'WC')->count();
        $peCount = Employee::where('employment_type', 'PE')->count();
        $mrCount = Employee::where('employment_type', 'MR')->whereNull('scheme_id')->count();
        $deletedCount = Employee::where('employment_type', 'Deleted')->count();
        return Inertia::render('Backend/Employees/AllEmployees', [
            'offices' => $offices,
            'search' => $search,
            'totalEmployees' => $totalEmployees,
            'wcCount' => $wcCount,
            'peCount' => $peCount,
            'mrCount' => $mrCount,
            'deletedCount' => $deletedCount,
        ]);
    }

    public function indexAllEmployees(Office $model) // shows all employees
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-allemployee'), 403, 'Access Denied');

        $totalEmployees = $model->employees()
            ->where('employment_type', '!=', 'Deleted')
            ->count();

        $wcCount = $model->employees()
            ->where('employment_type', 'WC')
            ->count();

        $peCount = $model->employees()
            ->where('employment_type', 'PE')
            ->count();

        $mrCount = $model->employees()
            ->where('employment_type', 'MR')
            ->whereNull('scheme_id')
            ->count();

        // Get distinct designation & education_qln for PE employees in this office
        $designationsPe = $model->employees()
            ->where('employment_type', 'PE')
            ->select('designation')
            ->distinct()
            ->orderBy('designation')
            ->pluck('designation')
            ->map(fn($d) => ['label' => $d, 'value' => $d])
            ->values();

        // Get distinct designation & education_qln for WC employees in this office
        $designationsWc = $model->employees()
            ->where('employment_type', 'WC')
            ->select('designation')
            ->distinct()
            ->orderBy('designation')
            ->pluck('designation')
            ->map(fn($d) => ['label' => $d, 'value' => $d])
            ->values();


        // Get distinct designation & education_qln for PE employees in this office
        $skills = $model->employees()
            ->where('employment_type', 'MR')
            ->whereNull('scheme_id')
            ->select('skill_at_present')
            ->distinct()
            ->orderBy('skill_at_present')
            ->pluck('skill_at_present')
            ->map(fn($d) => ['label' => $d, 'value' => $d])
            ->values();

        $educationQlnPe = $model->employees()
            ->where('employment_type', 'PE')
            ->select('educational_qln')
            ->distinct()
            ->orderBy('educational_qln')
            ->pluck('educational_qln')
            ->map(fn($e) => ['label' => $e, 'value' => $e])
            ->values();


        $educationQlnMr = $model->employees()
            ->where('employment_type', 'MR')
            ->whereNull('scheme_id')
            ->select('educational_qln')
            ->distinct()
            ->orderBy('educational_qln')
            ->pluck('educational_qln')
            ->map(fn($e) => ['label' => $e, 'value' => $e])
            ->values();


        return Inertia::render('Backend/Employees/Index/AllEmployees', [
            'office' => $model,
            'totalEmployees' => $totalEmployees,
            'peCount' => $peCount,
            'mrCount' => $mrCount,
            'wcCount' => $wcCount,
            'designationsPe' => $designationsPe,
            'designationsWc' => $designationsWc,
            'skills' => $skills,
            'educationQlnPe' => $educationQlnPe,
            'educationQlnMr' => $educationQlnMr,
        ]);
    }

    public function jsonAllEmployees(Request $request, Office $model)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-allemployee'), 403, 'Access Denied');


        $perPage = $request->get('rowsPerPage') ?? 5;
        $filter = $request->get('filter', []);
        $search = $filter['search'] ?? null; // ✅ extract from filter array

        $employees = $model->employees() // ✅ Only employees of this office
        ->with('office')
            ->where('employment_type', '!=', 'Deleted') // ✅ Exclude Deleted
            ->whereNull('scheme_id') // ✅ Exclude Scheme
            ->when($search, function ($builder) use ($search) {
                $builder->where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%$search%")
                        ->orWhere('mobile', 'LIKE', "%$search%")
                        ->orWhere('designation', 'LIKE', "%$search%")
                        ->orWhere('date_of_birth', 'LIKE', "%$search%")
                        ->orWhere('name_of_workplace', 'LIKE', "%$search%");
                });
            })
            ->when($filter['type'] ?? null, function ($query, $type) {
                $query->where('employment_type', $type);
            })
            ->when($filter['skill'] ?? null, function ($query, $skill) {
                $query->where('skill_at_present', $skill);
            })
            ->when($filter['designation'] ?? null, function ($query, $designation) {
                $query->where('designation', $designation);
            })->when($filter['education_qln_pe'] ?? null, function ($query, $educationQln) {
                $query->where('educational_qln', $educationQln);
            })->when($filter['education_qln_mr'] ?? null, function ($query, $educationQln) {
                $query->where('educational_qln', $educationQln);
            })
            ->orderBy('name', 'asc');


        return response()->json([
            'list' => $employees->paginate($perPage),
        ], 200);
    }


    public function wcEmployees(Request $request)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-wc-employee'), 403, 'Access Denied');

        $search = $request->get('search');
        $offices = Office::whereHas('employees', function ($query) {
            $query->where('employment_type', 'WC'); // ✅ Only PE employees
        })
            ->withCount(['employees as wc_count' => function ($query) {
                $query->where('employment_type', 'WC'); // ✅ Count PE employees
            }])->when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%"); // ✅ search by office name
            })->get();

        return Inertia::render('Backend/Employees/WcEmployees', [
            'offices' => $offices,
            'search' => $search,
        ]);
    }

    public function indexWcEmployees(Office $model) // shows PE type
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-wc-employee'), 403, 'Access Denied');

        // Get distinct designation & education_qln for PE employees in this office
        $designations = $model->employees()
            ->where('employment_type', 'WC')
            ->select('designation')
            ->distinct()
            ->orderBy('designation')
            ->pluck('designation')
            ->map(fn($d) => ['label' => $d, 'value' => $d])
            ->values();

        $educationQln = $model->employees()
            ->where('employment_type', 'WC')
            ->select('educational_qln')
            ->distinct()
            ->orderBy('educational_qln')
            ->pluck('educational_qln')
            ->map(fn($e) => ['label' => $e, 'value' => $e])
            ->values();

        return Inertia::render('Backend/Employees/Index/WcEmployees', [
            'office' => $model,
            'designations' => $designations,
            'educationQln' => $educationQln,
        ]);
    }

    public function jsonWcEmployees(Request $request, Office $model)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-wc-employee'), 403, 'Access Denied');


        $perPage = $request->get('rowsPerPage') ?? 5;
        $filter = $request->get('filter', []);
        $search = $filter['search'] ?? null; // ✅ extract from filter array


        $employees = $model->employees() // ✅ Only employees of this office
        ->with(['office', 'remunerationDetail'])
            ->where('employment_type', 'WC')
            ->when($search, function ($builder) use ($search) {
                $builder->where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%$search%")
                        ->orWhere('mobile', 'LIKE', "%$search%")
                        ->orWhere('designation', 'LIKE', "%$search%")
                        ->orWhere('date_of_birth', 'LIKE', "%$search%")
                        ->orWhere('name_of_workplace', 'LIKE', "%$search%");
                });
            })->when($filter['designation'] ?? null, function ($query, $designation) {
                $query->where('designation', $designation);
            })->when($filter['education_qln'] ?? null, function ($query, $educationQln) {
                $query->where('educational_qln', $educationQln);
            })
            ->orderBy('name', 'asc');


        return response()->json([
            'list' => $employees->paginate($perPage),
        ], 200);
    }


    public function peEmployees(Request $request)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-pe-employee'), 403, 'Access Denied');

        $search = $request->get('search');
        $offices = Office::whereHas('employees', function ($query) {
            $query->where('employment_type', 'PE'); // ✅ Only PE employees
        })
            ->withCount(['employees as pe_count' => function ($query) {
                $query->where('employment_type', 'PE'); // ✅ Count PE employees
            }])->when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%"); // ✅ search by office name
            })->get();

        return Inertia::render('Backend/Employees/PeEmployees', [
            'offices' => $offices,
            'search' => $search,
        ]);
    }

    public function indexPeEmployees(Office $model) // shows PE type
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-pe-employee'), 403, 'Access Denied');

        // Get distinct designation & education_qln for PE employees in this office
        $designations = $model->employees()
            ->where('employment_type', 'PE')
            ->select('designation')
            ->distinct()
            ->orderBy('designation')
            ->pluck('designation')
            ->map(fn($d) => ['label' => $d, 'value' => $d])
            ->values();

        $educationQln = $model->employees()
            ->where('employment_type', 'PE')
            ->select('educational_qln')
            ->distinct()
            ->orderBy('educational_qln')
            ->pluck('educational_qln')
            ->map(fn($e) => ['label' => $e, 'value' => $e])
            ->values();

        return Inertia::render('Backend/Employees/Index/PeEmployees', [
            'office' => $model,
            'designations' => $designations,
            'educationQln' => $educationQln,
        ]);
    }

    public function jsonPeEmployees(Request $request, Office $model)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-pe-employee'), 403, 'Access Denied');


        $perPage = $request->get('rowsPerPage') ?? 5;
        $filter = $request->get('filter', []);
        $search = $filter['search'] ?? null; // ✅ extract from filter array


        $employees = $model->employees() // ✅ Only employees of this office
        ->with(['office', 'remunerationDetail'])
            ->where('employment_type', 'PE')
            ->when($search, function ($builder) use ($search) {
                $builder->where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%$search%")
                        ->orWhere('mobile', 'LIKE', "%$search%")
                        ->orWhere('designation', 'LIKE', "%$search%")
                        ->orWhere('date_of_birth', 'LIKE', "%$search%")
                        ->orWhere('name_of_workplace', 'LIKE', "%$search%");
                });
            })->when($filter['designation'] ?? null, function ($query, $designation) {
                $query->where('designation', $designation);
            })->when($filter['education_qln'] ?? null, function ($query, $educationQln) {
                $query->where('educational_qln', $educationQln);
            })
            ->orderBy('name', 'asc');


        return response()->json([
            'list' => $employees->paginate($perPage),
        ], 200);
    }

    public function mrEmployees(Request $request)
    {

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-mr-employee'), 403, 'Access Denied');

        $search = $request->get('search');
        $offices = Office::whereHas('employees', function ($query) {
            $query->where('employment_type', 'MR')
                ->whereNull('scheme_id');
        })
            ->withCount(['employees as mr_count' => function ($query) {
                $query->where('employment_type', 'MR')->whereNull('scheme_id'); // ✅ Count PE employees
            }])->when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%"); // ✅ search by office name
            })->get();

        return Inertia::render('Backend/Employees/MrEmployees', [
            'offices' => $offices,
            'search' => $search,
        ]);
    }

    public function indexMrEmployees(Office $model) // shows MR type
    {

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-mr-employee'), 403, 'Access Denied');

        // Get distinct designation & education_qln for PE employees in this office
        $skills = $model->employees()
            ->where('employment_type', 'MR')
            ->whereNull('scheme_id')
            ->select('skill_at_present')
            ->distinct()
            ->orderBy('skill_at_present')
            ->pluck('skill_at_present')
            ->map(fn($d) => ['label' => $d, 'value' => $d])
            ->values();

        $educationQln = $model->employees()
            ->where('employment_type', 'MR')
            ->whereNull('scheme_id')
            ->select('educational_qln')
            ->distinct()
            ->orderBy('educational_qln')
            ->pluck('educational_qln')
            ->map(fn($e) => ['label' => $e, 'value' => $e])
            ->values();

        return Inertia::render('Backend/Employees/Index/MrEmployees', [
            'office' => $model,
            'skills' => $skills,
            'educationQln' => $educationQln,
        ]);
    }


    public function jsonMrEmployees(Request $request, Office $model)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-mr-employee'), 403, 'Access Denied');

        $perPage = $request->get('rowsPerPage') ?? 5;
        $filter = $request->get('filter', []);
        $search = $filter['search'] ?? null; // ✅ extract from filter array
        $employees = $model->employees() // ✅ Only employees of this office
        ->with('office')
            ->where('employment_type', 'MR')
            ->whereNull('scheme_id')
            ->when($search, function ($builder) use ($search) {
                $builder->where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%$search%")
                        ->orWhere('mobile', 'LIKE', "%$search%")
                        ->orWhere('designation', 'LIKE', "%$search%")
                        ->orWhere('date_of_birth', 'LIKE', "%$search%")
                        ->orWhere('name_of_workplace', 'LIKE', "%$search%");
                });
            })
            ->when($filter['skill'] ?? null, function ($query, $skill) {
                $query->where('skill_at_present', $skill);
            })->when($filter['education_qln'] ?? null, function ($query, $educationQln) {
                $query->where('educational_qln', $educationQln);
            })
            ->orderBy('name', 'asc');

        return response()->json([
            'list' => $employees->paginate($perPage),
        ], 200);
    }


    public function schemeEmployees(Request $request)
    {

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-scheme-employee'), 403, 'Access Denied');

        $search = $request->get('search');

        $schemes = Scheme::withCount(['employees as mr_count' => function ($query) {
            $query->where('employment_type', 'MR');
        }])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            })
            ->get();

        return Inertia::render('Backend/Employees/SchemeEmployees', [
            'schemes' => $schemes,
            'search' => $search,
        ]);
    }

    public function indexSchemeEmployees(Scheme $model)
    {

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-scheme-employee'), 403, 'Access Denied');

        // Get distinct skills for MR employees in this scheme
        $skills = $model->employees()
            ->where('employment_type', 'MR')
            ->select('skill_at_present')
            ->distinct()
            ->orderBy('skill_at_present')
            ->pluck('skill_at_present')
            ->map(fn($d) => ['label' => $d, 'value' => $d])
            ->values();

        // Get distinct educational qualifications for MR employees in this scheme
        $educationQln = $model->employees()
            ->where('employment_type', 'MR')
            ->select('educational_qln')
            ->distinct()
            ->orderBy('educational_qln')
            ->pluck('educational_qln')
            ->map(fn($e) => ['label' => $e, 'value' => $e])
            ->values();

        $offices = $model->employees()
            ->where('employment_type', 'MR')
            ->with('office')
            ->select('office_id')
            ->distinct()
            ->get()
            ->map(fn($emp) => [
                'label' => $emp->office?->name,
                'value' => $emp->office_id
            ])
            ->filter(fn($o) => !empty($o['label'])) // remove nulls
            ->values();

        return Inertia::render('Backend/Employees/Index/SchemeEmployees', [
            'scheme' => $model,
            'skills' => $skills,
            'educationQln' => $educationQln,
            'offices' => $offices,
        ]);
    }

    public function jsonSchemeEmployees(Request $request, Scheme $model)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-scheme-employee'), 403, 'Access Denied');

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-allemployee'), 403, 'Access Denied');

        $perPage = $request->get('rowsPerPage') ?? 5;
        $filter = $request->get('filter', []);
        $search = $filter['search'] ?? null;

        $employees = $model->employees() // ✅ Only employees of this scheme
        ->with(['scheme','office'])
            ->where('employment_type', 'MR')
            ->when($search, function ($builder) use ($search) {
                $builder->where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%$search%")
                        ->orWhere('mobile', 'LIKE', "%$search%")
                        ->orWhere('designation', 'LIKE', "%$search%")
                        ->orWhere('date_of_birth', 'LIKE', "%$search%")
                        ->orWhere('name_of_workplace', 'LIKE', "%$search%");
                });
            })
            ->when($filter['skill'] ?? null, function ($query, $skill) {
                $query->where('skill_at_present', $skill);
            })
            ->when($filter['education_qln'] ?? null, function ($query, $educationQln) {
                $query->where('educational_qln', $educationQln);
            })
            ->when($filter['office'] ?? null, function ($query, $officeId) {
                $query->where('office_id', $officeId); // ✅ Office filter
            })
            ->orderBy('name', 'asc');

        return response()->json([
            'list' => $employees->paginate($perPage),
        ], 200);

    }



    public function deletedEmployees() // shows PE type
    {

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-deleted-employee'), 403, 'Access Denied');

        $office = Office::whereHas('employees')->get(); // ⬅️ Only offices with employees

        return Inertia::render('Backend/Employees/Index/DeletedEmployees', [
            'office' => $office,
        ]);
    }

    public function jsonDeletedEmployees(Request $request)
    {

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-deleted-employee'), 403, 'Access Denied');


        $perPage = $request->get('rowsPerPage') ?? 5;
        $filter = $request->get('filter', []);
        $search = $filter['search'] ?? null; // ✅ extract from filter array
        $employees = Employee::query()
            ->where('employment_type', 'Deleted')
            ->with(['office', 'deletionDetail'])
            ->when($search, function (Builder $builder) use ($search) {
                $builder->where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%$search%")
                        ->orWhere('mobile', 'LIKE', "%$search%")
                        ->orWhere('designation', 'LIKE', "%$search%")
                        ->orWhere('date_of_birth', 'LIKE', "%$search%")
                        ->orWhere('name_of_workplace', 'LIKE', "%$search%");
                });
            })
            ->when($filter['office'] ?? null, function (Builder $query, $officeId) {
                $query->whereHas('office', function (Builder $q) use ($officeId) {
                    $q->where('id', $officeId);
                });
            })
            ->when($filter['reason'] ?? null, function (Builder $query, $reason) {
                $query->whereHas('deletionDetail', function (Builder $q) use ($reason) {
                    $q->where('reason', $reason);
                });
            })
            ->orderBy('name', 'asc');

        return response()->json([
            'list' => $employees->paginate($perPage),
        ], 200);
    }

    public function show(Request $request, Employee $model)
    {

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-employee'), 403, 'Access Denied');
        $office = Office::all();
        $documentTypes = DocumentType::all(); // get all document types
        return inertia('Backend/Employees/Show', [
            'data' => $model->load(['office', 'documents.type', 'transfers.oldOffice', 'transfers.newOffice', 'scheme',
                'deletionDetail', 'deletionRequests', 'remunerationDetail', 'engagementCard', 'editRequests.attachments.type',
                'transferRequests.currentOffice', 'transferRequests.requestedOffice', 'documentRequest.files.documentType','documentDeleteRequest.document']),
            'office' => $office,
            'documentTypes' => $documentTypes,
            'canDelete' => $user->can('delete-employee'),
            'canEdit' => $user->can('edit-employee'),
            'canCreate' => $user->can('create-employee'),

            'canEditDelete' => $user->can('edit-delete'),
            'canDeleteDocument' => $user->can('delete-document'),

            'canRequestEdit' => $user->can('request-edit'),
            'canRequestDelete' => $user->can('request-delete'),
            'canRequestTransfer' => $user->can('request-transfer'),
            'canRequestDocumentEdit' => $user->can('request-document-edit'),

            'canApproveEdit' => $user->can('approve-edit'),
            'canApproveDelete' => $user->can('approve-delete'),
            'canApproveTransfer' => $user->can('approve-transfer'),
            'canApproveDocumentEdit' => $user->can('approve-document-edit'),

            'canCreateRemuneration' => $user->can('create-remuneration'),

            'canCreateEngagementCard' => $user->can('store-engagement-card'),
            'canDownloadEngagementCard' => $user->can('download-engagement-card'),
            'canDeleteEngagementCard' => $user->can('delete-engagement-card'),


            'canCreateTransfer' => $user->can('transfer-employee'),
            'canDeleteTransfer' => $user->can('delete-transfer'),


            'canRequestDocumentDelete' => $user->can('request-document-delete'),
            'canApproveDocumentDelete' => $user->can('approve-document-delete'),
        ]);
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('create-employee'), 403, 'Access Denied');

        $documentTypes = DocumentType::all();
        $offices = Office::all();
        return inertia('Backend/Employees/Create', [
            'documentTypes' => $documentTypes,
            'offices' => $offices,
        ]);
    }

    public function store(Request $request)
    {
//        dd($request->all());

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('create-employee'), 403, 'Access Denied');

        $validated = $request->validate([
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:800', // 800KB limit
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:employees,email',
            'mobile' => 'required|string|max:20|unique:employees,mobile',
            'parent_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'address' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'post_assigned' => 'nullable|string|max:255',
            'employment_type' => ['required', Rule::in(['MR', 'PE','WC'])],
            'office' => 'required|exists:offices,id',
            'educational_qln' => 'required|string|max:255',
            'technical_qln' => 'nullable|string|max:255',
            'name_of_workplace' => 'nullable|string|max:255',
            'post_per_qualification' => 'nullable|string|max:255',
            'engagement_card_no' => 'nullable|string|max:255',
            'date_of_engagement' => 'nullable|date',
            'date_of_retirement' => 'nullable|date',
            'skill_category' => 'nullable|string|max:255',
            'skill_at_present' => 'nullable|string|max:255',
            'scheme' => 'nullable|exists:schemes,id',
            'documents' => 'nullable|array',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $employee = DB::transaction(function () use ($validated, $request) {
            $employee = Employee::create([
                'employee_code' => $this->generateEmployeeCode(),
                'office_id' => $validated['office'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'],
                'parent_name' => $validated['parent_name'],
                'date_of_birth' => $validated['date_of_birth'],
                'address' => $validated['address'],
                'designation' => $validated['designation'] ?? null,
                'post_assigned' => $validated['post_assigned'] ?? null,
                'employment_type' => $validated['employment_type'],
                'educational_qln' => $validated['educational_qln'],
                'technical_qln' => $validated['technical_qln'],
                'name_of_workplace' => $validated['name_of_workplace'],
                'post_per_qualification' => $validated['post_per_qualification'],
                'engagement_card_no' => $validated['engagement_card_no'] ?? null,
                'date_of_engagement' => $validated['date_of_engagement'],
                'date_of_retirement' => $validated['date_of_retirement']??null,
                'skill_category' => $validated['skill_category'],
                'skill_at_present' => $validated['skill_at_present'],
                'scheme_id' => $validated['scheme'] ?? null,
            ]);
            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');

                $randomString = \Str::random(8);
                $extension = $file->getClientOriginalExtension();
                $generatedName = 'avatar_' . $randomString . '.' . $extension;

                // Store the avatar in the 'pictures' directory (public disk)
                $avatarPath = $file->storeAs('pictures', $generatedName, 'public');

                // Update the employee with avatar path
                $employee->update([
                    'avatar' => $avatarPath
                ]);
            }

            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $typeId => $file) {
                    if ($file) {
                        $documentType = DocumentType::find($typeId);
                        if (!$documentType) {
                            throw new Exception("Invalid document type ID: {$typeId}");
                        }

                        $randomString = \Str::random(8); // Laravel helper for random string
                        $extension = $file->getClientOriginalExtension();
                        $generatedName = $documentType->name . '_' . $randomString . '.' . $extension;

                        $path = $file->storeAs('documents', $generatedName, 'public');

                        Document::create([
                            'employee_id' => $employee->id,
                            'document_type_id' => $typeId,
                            'type' => $documentType->name,
                            'name' => $generatedName, // custom name
                            'mime' => $file->getClientMimeType(),
                            'path' => $path,
                            'upload_date' => now(),
                        ]);
                    }
                }
            }
            return $employee;
        });


        return response()->json([
            'employee' => $employee,
            'message' => 'Employee Created Successfully!'
        ]);


    }

    public function edit(Request $request, Employee $model)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('edit-employee'), 403, 'Access Denied');
        $documentTypes = DocumentType::all();
        $offices = Office::all();
        $schemes = Scheme::all();
        return inertia('Backend/Employees/Edit', [

            'data' => $model->load(['office', 'scheme', 'documents.type']),
            'documentTypes' => $documentTypes,
            'offices' => $offices,
            'schemes' => $schemes,
        ]);

    }

    public function update(Request $request, Employee $model)
    {
//        dd($request->all());
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('edit-employee'), 403, 'Access Denied');

        $validated = $request->validate([
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:5120', // 800KB limit
            'delete_avatar' => 'nullable|boolean',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => ['required', 'string', 'max:20', Rule::unique('employees', 'mobile')->ignore($model->id)],
            'parent_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'post_assigned' => 'nullable|string|max:255',
            'employment_type' => ['required', Rule::in(['MR', 'PE','WC', 'Deleted'])],
            'office' => 'required|exists:offices,id',
            'educational_qln' => 'nullable|string|max:255',
            'technical_qln' => 'nullable|string|max:255',
            'name_of_workplace' => 'nullable|string|max:255',
            'post_per_qualification' => 'nullable|string|max:255',
            'engagement_card_no' => 'nullable|string|max:255',
            'date_of_engagement' => 'nullable|date',
            'date_of_retirement' => 'nullable|date',
            'skill_category' => 'nullable|string|max:255',
            'skill_at_present' => 'nullable|string|max:255',
            'scheme' => 'nullable|exists:schemes,id',
            'documents' => 'nullable|array',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $employee = DB::transaction(function () use ($validated, $request, $model) {

            // ✅ Handle avatar deletion
            if ($request->boolean('delete_avatar')) {
                if ($model->avatar && Storage::disk('public')->exists($model->avatar)) {
                    Storage::disk('public')->delete($model->avatar);
                }
                $model->avatar = null; // remove from DB
            }

            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');

                $randomString = \Str::random(8);
                $extension = $file->getClientOriginalExtension();
                $generatedName = 'avatar_' . $randomString . '.' . $extension;

                // Store the avatar in the 'pictures' directory (public disk)
                $avatarPath = $file->storeAs('pictures', $generatedName, 'public');
                // Store path relative to 'storage' (e.g., for public URL access)
                $model->avatar = $avatarPath;
            }

            $model->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'],
                'parent_name' => $validated['parent_name'],
                'date_of_birth' => $validated['date_of_birth'],
                'address' => $validated['address'],
                'designation' => $validated['designation'] ?? null,
                'post_assigned' => $validated['post_assigned'] ?? null,
                'employment_type' => $validated['employment_type'],
                'office_id' => $validated['office'],
                'educational_qln' => $validated['educational_qln'],
                'technical_qln' => $validated['technical_qln'],
                'name_of_workplace' => $validated['name_of_workplace'],
                'post_per_qualification' => $validated['post_per_qualification'],
                'engagement_card_no' => $validated['engagement_card_no'],
                'date_of_engagement' => $validated['date_of_engagement'],
                'date_of_retirement'=> $validated['date_of_retirement'],
                'skill_category' => $validated['skill_category'],
                'skill_at_present' => $validated['skill_at_present'],
                'scheme_id' => $validated['scheme'] ?? null,
                'avatar' => $model->avatar ?? $model->avatar,
            ]);


            // Save documents
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $typeId => $file) {
                    if ($file) {
                        $documentType = DocumentType::find($typeId);

                        if (!$documentType) {
                            throw new Exception("Invalid document type ID: {$typeId}");
                        }

                        $randomString = \Str::random(8);
                        $extension = $file->getClientOriginalExtension();
                        $generatedName = $documentType->name . '_' . $randomString . '.' . $extension;

                        $path = $file->storeAs('documents', $generatedName, 'public');

                        $model->documents()->updateOrCreate(
                            ['document_type_id' => $typeId],
                            [
                                'name' => $generatedName,
                                'type' => $documentType->name,
                                'mime' => $file->getClientMimeType(),
                                'path' => str_replace('public/', '', $path),
                                'upload_date' => now(),
                            ]
                        );
                    }
                }
            }

            return $model;
        });


        return response()->json([
            'employee' => $employee,
            'message' => 'Employee Edited Successfully!'
        ]);

    }

    public function destroy(Employee $model)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('delete-employee'), 403, 'Access Denied');

        $model->delete();
        return response()->json(['message' => 'Employee deleted successfully.']);
    }

    public function destroyPermanently($id)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('delete-employee'), 403, 'Access Denied');

        // Find soft-deleted record only
        $employee = Employee::onlyTrashed()->findOrFail($id);

        // Permanently delete
        $employee->forceDelete();

        return response()->json(['message' => 'Employee permanently deleted.']);
    }



    public function restore($model)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('delete-employee'), 403, 'Access Denied');

        $employee = Employee::onlyTrashed()->findOrFail($model); // ✅ include soft-deleted
        $employee->restore();
        return response()->json(['message' => 'Employee restored successfully.']);
    }
    public function managerAll()
    {

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-allemployee'), 403, 'Access Denied');

        $user = auth()->user();

        // Offices directly formatted for q-select
        $offices = $user->offices()
            ->get(['offices.name as label', 'offices.id as value'])
            ->map(function ($office) {
                return [
                    'label' => $office->label,
                    'value' => $office->value,
                ];
            })
            ->toArray();

        // Get all office IDs the user belongs to
        $officeIds = collect($offices)->pluck('value');

        // Total employees across all offices
        $totalEmployees = Employee::whereIn('office_id', $officeIds)
            ->where('employment_type', '!=', 'Deleted')
            ->whereNull('scheme_id')
            ->count();

        $wcCount = Employee::whereIn('office_id', $officeIds)
            ->where('employment_type', 'WC')
            ->count();

        $peCount = Employee::whereIn('office_id', $officeIds)
            ->where('employment_type', 'PE')
            ->count();

        $mrCount = Employee::whereIn('office_id', $officeIds)
            ->where('employment_type', 'MR')
            ->whereNull('scheme_id')
            ->count();

        // Distinct Designations
        $designationsPe = Employee::whereIn('office_id', $officeIds)
            ->where('employment_type', 'PE')
            ->select('designation')
            ->distinct()
            ->orderBy('designation')
            ->pluck('designation')
            ->map(fn($d) => ['label' => $d, 'value' => $d])
            ->values();

        // Distinct Designations
        $designationsWc = Employee::whereIn('office_id', $officeIds)
            ->where('employment_type', 'WC')
            ->select('designation')
            ->distinct()
            ->orderBy('designation')
            ->pluck('designation')
            ->map(fn($d) => ['label' => $d, 'value' => $d])
            ->values();

        // Distinct Educational Qualifications
        $educationQlnWc = Employee::whereIn('office_id', $officeIds)
            ->where('employment_type', 'WC')
            ->select('educational_qln')
            ->distinct()
            ->orderBy('educational_qln')
            ->pluck('educational_qln')
            ->map(fn($d) => ['label' => $d, 'value' => $d])
            ->values();

        // Distinct Educational Qualifications
        $educationQlnPe = Employee::whereIn('office_id', $officeIds)
            ->where('employment_type', 'PE')
            ->select('educational_qln')
            ->distinct()
            ->orderBy('educational_qln')
            ->pluck('educational_qln')
            ->map(fn($d) => ['label' => $d, 'value' => $d])
            ->values();

        // Distinct Educational Qualifications
        $educationQlnMr = Employee::whereIn('office_id', $officeIds)
            ->where('employment_type', 'MR')
            ->whereNull('scheme_id')
            ->select('educational_qln')
            ->distinct()
            ->orderBy('educational_qln')
            ->pluck('educational_qln')
            ->map(fn($d) => ['label' => $d, 'value' => $d])
            ->values();


        // Get distinct designation & education_qln for MR employees in this office
        $skills = Employee::whereIn('office_id', $officeIds)
            ->where('employment_type', 'MR')
            ->whereNull('scheme_id')
            ->select('skill_at_present')
            ->distinct()
            ->orderBy('skill_at_present')
            ->pluck('skill_at_present')
            ->map(fn($d) => ['label' => $d, 'value' => $d])
            ->values();


        return inertia('Backend/Employees/IndexManager/AllEmployees', [
            'offices' => $offices,          // now plain array, safe for Vue
            'totalEmployees' => $totalEmployees,
            'wcCount' => $wcCount,
            'peCount' => $peCount,
            'mrCount' => $mrCount,
            'designationsWc' => $designationsWc,
            'designationsPe' => $designationsPe,
            'educationQlnWc' => $educationQlnWc,
            'educationQlnPe' => $educationQlnPe,
            'educationQlnMr' => $educationQlnMr,
            'skills' => $skills,
        ]);
    }

    public function jsonMangerAll(Request $request)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-allemployee'), 403, 'Access Denied');

        $perPage = $request->integer('rowsPerPage', 5);
        $filter = $request->get('filter', []);
        $search = $filter['search'] ?? null;
        $officeIds = $filter['office'] ?? $user->offices()->pluck('offices.id')->all();

        $employees = Employee::with('office')
            ->whereIn('office_id', (array)$officeIds)
            ->where('employment_type', '!=', 'Deleted')
            ->whereNull('scheme_id')
            ->when($search, fn($q) => $q->where(function ($sub) use ($search) {
                $sub->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('mobile', 'LIKE', "%{$search}%")
                    ->orWhere('designation', 'LIKE', "%{$search}%")
                    ->orWhere('date_of_birth', 'LIKE', "%{$search}%")
                    ->orWhere('name_of_workplace', 'LIKE', "%{$search}%");
            }))
            ->when(($filter['type'] ?? null), fn($q, $type) => $q->where('employment_type', $type))
            ->when(($filter['skill'] ?? null), fn($q, $skill) => $q->where('skill_at_present', $skill))
            ->when($filter['designation'] ?? null, function ($query, $designation) {
                $query->where('designation', $designation);
            })->when($filter['educational_qln'] ?? null, function ($query, $educationQln) {
                $query->where('educational_qln', $educationQln);
            })
            ->orderBy('name', 'asc');

        return response()->json([
            'list' => $employees->paginate($perPage),
        ], 200);
    }

    public function managerWc()
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-wc-employee'), 403, 'Access Denied');

        $user = auth()->user();

        // Offices directly formatted for q-select
        $offices = $user->offices()
            ->get(['offices.name as label', 'offices.id as value'])
            ->map(function ($office) {
                return [
                    'label' => $office->label,
                    'value' => $office->value,
                ];
            });

        // Get designations of PE employees from all offices user belongs to
        $designations = Employee::whereIn('office_id', $offices->pluck('value'))
            ->where('employment_type', 'WC')
            ->select('designation')
            ->distinct()
            ->orderBy('designation')
            ->pluck('designation')
            ->map(fn($d) => ['label' => $d, 'value' => $d])
            ->values();

        $educationQln = Employee::whereIn('office_id', $offices->pluck('value'))
            ->where('employment_type', 'WC')
            ->select('educational_qln')
            ->distinct()
            ->orderBy('educational_qln')
            ->pluck('educational_qln')
            ->map(fn($d) => ['label' => $d, 'value' => $d])
            ->values();

        return inertia('Backend/Employees/IndexManager/WcEmployees', [
            'offices' => $offices,
            'designations' => $designations,
            'educationQln' => $educationQln,
        ]);
    }

    public function jsonMangerWc(Request $request)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-wc-employee'), 403, 'Access Denied');

        $perPage = $request->integer('rowsPerPage', 5);
        $filter = $request->get('filter', []);
        $search = $filter['search'] ?? null;
        $officeIds = $filter['office'] ?? $user->offices()->pluck('offices.id')->all();

        $employees = Employee::with(['office', 'remunerationDetail'])
            ->whereIn('office_id', (array)$officeIds)
            ->where('employment_type', 'WC')
            ->when($search, fn($q) => $q->where(function ($sub) use ($search) {
                $sub->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('mobile', 'LIKE', "%{$search}%")
                    ->orWhere('designation', 'LIKE', "%{$search}%")
                    ->orWhere('date_of_birth', 'LIKE', "%{$search}%")
                    ->orWhere('name_of_workplace', 'LIKE', "%{$search}%");
            }))
            ->when($filter['designation'] ?? null, function ($query, $designation) {
                $query->where('designation', $designation);
            })->when($filter['education_qln'] ?? null, function ($query, $educationQln) {
                $query->where('educational_qln', $educationQln);
            })
            ->orderBy('name', 'asc');

        return response()->json([
            'list' => $employees->paginate($perPage),
        ], 200);
    }


    public function managerPe()
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-pe-employee'), 403, 'Access Denied');

        $user = auth()->user();

        // Offices directly formatted for q-select
        $offices = $user->offices()
            ->get(['offices.name as label', 'offices.id as value'])
            ->map(function ($office) {
                return [
                    'label' => $office->label,
                    'value' => $office->value,
                ];
            });

        // Get designations of PE employees from all offices user belongs to
        $designations = Employee::whereIn('office_id', $offices->pluck('value'))
            ->where('employment_type', 'PE')
            ->select('designation')
            ->distinct()
            ->orderBy('designation')
            ->pluck('designation')
            ->map(fn($d) => ['label' => $d, 'value' => $d])
            ->values();

        $educationQln = Employee::whereIn('office_id', $offices->pluck('value'))
            ->where('employment_type', 'PE')
            ->select('educational_qln')
            ->distinct()
            ->orderBy('educational_qln')
            ->pluck('educational_qln')
            ->map(fn($d) => ['label' => $d, 'value' => $d])
            ->values();

        return inertia('Backend/Employees/IndexManager/PeEmployees', [
            'offices' => $offices,
            'designations' => $designations,
            'educationQln' => $educationQln,
        ]);
    }

    public function jsonMangerPe(Request $request)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-pe-employee'), 403, 'Access Denied');

        $perPage = $request->integer('rowsPerPage', 5);
        $filter = $request->get('filter', []);
        $search = $filter['search'] ?? null;
        $officeIds = $filter['office'] ?? $user->offices()->pluck('offices.id')->all();

        $employees = Employee::with(['office', 'remunerationDetail'])
            ->whereIn('office_id', (array)$officeIds)
            ->where('employment_type', 'PE')
            ->when($search, fn($q) => $q->where(function ($sub) use ($search) {
                $sub->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('mobile', 'LIKE', "%{$search}%")
                    ->orWhere('designation', 'LIKE', "%{$search}%")
                    ->orWhere('date_of_birth', 'LIKE', "%{$search}%")
                    ->orWhere('name_of_workplace', 'LIKE', "%{$search}%");
            }))
            ->when($filter['designation'] ?? null, function ($query, $designation) {
                $query->where('designation', $designation);
            })->when($filter['education_qln'] ?? null, function ($query, $educationQln) {
                $query->where('educational_qln', $educationQln);
            })
            ->orderBy('name', 'asc');

        return response()->json([
            'list' => $employees->paginate($perPage),
        ], 200);
    }

    public function managerMr()
    {

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-mr-employee'), 403, 'Access Denied');

        $user = auth()->user();

        // Offices directly formatted for q-select
        $offices = $user->offices()
            ->get(['offices.name as label', 'offices.id as value'])
            ->map(function ($office) {
                return [
                    'label' => $office->label,
                    'value' => $office->value,
                ];
            });
        // Get distinct designation & education_qln for PE employees in this office
        $skills = Employee::whereIn('office_id', $offices->pluck('value'))
            ->where('employment_type', 'MR')
            ->whereNull('scheme_id')
            ->select('skill_at_present')
            ->distinct()
            ->orderBy('skill_at_present')
            ->pluck('skill_at_present')
            ->map(fn($d) => ['label' => $d, 'value' => $d])
            ->values();

        $educationQln = Employee::whereIn('office_id', $offices->pluck('value'))
            ->where('employment_type', 'MR')
            ->whereNull('scheme_id')
            ->select('educational_qln')
            ->distinct()
            ->orderBy('educational_qln')
            ->pluck('educational_qln')
            ->map(fn($e) => ['label' => $e, 'value' => $e])
            ->values();

        return inertia('Backend/Employees/IndexManager/MrEmployees', [
            'offices' => $offices,
            'skills' => $skills,
            'educationQln' => $educationQln,
        ]);
    }

    public function jsonMangerMr(Request $request)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-mr-employee'), 403, 'Access Denied');

        $perPage = $request->integer('rowsPerPage', 5);
        $filter = $request->get('filter', []);
        $search = $filter['search'] ?? null;
        $officeIds = $filter['office'] ?? $user->offices()->pluck('offices.id')->all();

        $employees = Employee::with(['office'])
            ->whereIn('office_id', (array)$officeIds)
            ->where('employment_type', 'MR')
            ->whereNull('scheme_id')
            ->when($search, fn($q) => $q->where(function ($sub) use ($search) {
                $sub->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('mobile', 'LIKE', "%{$search}%")
                    ->orWhere('designation', 'LIKE', "%{$search}%")
                    ->orWhere('date_of_birth', 'LIKE', "%{$search}%")
                    ->orWhere('name_of_workplace', 'LIKE', "%{$search}%");
            }))
            ->when($filter['skill'] ?? null, function ($query, $skill) {
                $query->where('skill_at_present', $skill);
            })->when($filter['education_qln'] ?? null, function ($query, $educationQln) {
                $query->where('educational_qln', $educationQln);
            })
            ->orderBy('name', 'asc');

        return response()->json([
            'list' => $employees->paginate($perPage),
        ], 200);
    }


    public function managerScheme(Request $request){

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-scheme-employee'), 403, 'Access Denied');

        $user = auth()->user();

        // Offices assigned to this user with MR employees having a scheme
        $offices = $user->offices()
            ->with(['employees' => fn($q) => $q->where('employment_type', 'MR')->whereNotNull('scheme_id')->with('scheme')])
            ->get(['offices.id', 'offices.name']);

        // Get the first scheme from the employees
        $scheme = $offices
            ->flatMap(fn($office) => $office->employees)
            ->pluck('scheme')
            ->first();

        // Distinct skills for MR employees in user offices (any scheme)
        $skills = Employee::whereIn('office_id', $offices->pluck('id'))
            ->where('employment_type', 'MR')
            ->whereNotNull('scheme_id')
            ->select('skill_at_present')
            ->distinct()
            ->orderBy('skill_at_present')
            ->pluck('skill_at_present')
            ->map(fn($d) => ['label' => $d, 'value' => $d])
            ->values();

        // Distinct education qualifications
        $educationQln = Employee::whereIn('office_id', $offices->pluck('id'))
            ->where('employment_type', 'MR')
            ->whereNotNull('scheme_id')
            ->select('educational_qln')
            ->distinct()
            ->orderBy('educational_qln')
            ->pluck('educational_qln')
            ->map(fn($e) => ['label' => $e, 'value' => $e])
            ->values();

        return Inertia::render('Backend/Employees/IndexManager/SchemeEmployees', [
            'skills'       => $skills,
            'educationQln' => $educationQln,
            'offices'      => $offices,
            'scheme'       => $scheme,
        ]);
    }

    public function jsonMangerScheme(Request $request){

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-scheme-employee'), 403, 'Access Denied');


        $perPage   = $request->integer('rowsPerPage', 5);
        $filter    = $request->get('filter', []);
        $search    = $filter['search'] ?? null;

        // Only user’s offices
        $officeIds = $filter['office'] ?? $user->offices()->pluck('offices.id')->all();

        $employees = Employee::with(['scheme', 'office'])
            ->whereIn('office_id', (array) $officeIds)
            ->where('employment_type', 'MR')
            ->whereNotNull('scheme_id') // ✅ any scheme
            ->when($search, function ($builder) use ($search) {
                $builder->where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%$search%")
                        ->orWhere('mobile', 'LIKE', "%$search%")
                        ->orWhere('designation', 'LIKE', "%$search%")
                        ->orWhere('date_of_birth', 'LIKE', "%$search%")
                        ->orWhere('name_of_workplace', 'LIKE', "%$search%");
                });
            })
            ->when($filter['skill'] ?? null, function ($query, $skill) {
                $query->where('skill_at_present', $skill);
            })
            ->when($filter['education_qln'] ?? null, function ($query, $educationQln) {
                $query->where('educational_qln', $educationQln);
            })
            ->orderBy('name', 'asc');

        return response()->json([
            'list' => $employees->paginate($perPage),
        ], 200);
    }


    public function managerDeleted()
    {

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-deleted-employee'), 403, 'Access Denied');

        $user = auth()->user();

        // Offices directly formatted for q-select
        $offices = $user->offices()
            ->get(['offices.name as label', 'offices.id as value'])
            ->map(function ($office) {
                return [
                    'label' => $office->label,
                    'value' => $office->value,
                ];
            });

        return inertia('Backend/Employees/IndexManager/DeletedEmployees', [
            'offices' => $offices,
        ]);
    }

    public function jsonMangerDeleted(Request $request)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-deleted-employee'), 403, 'Access Denied');

        $perPage = $request->integer('rowsPerPage', 5);
        $filter = $request->get('filter', []);
        $search = $filter['search'] ?? null;
        $officeIds = $filter['office'] ?? $user->offices()->pluck('offices.id')->all();

        $employees = Employee::with(['office','deletionDetail'])
            ->whereIn('office_id', (array)$officeIds)
            ->where('employment_type', 'Deleted')
            ->when($search, fn($q) => $q->where(function ($sub) use ($search) {
                $sub->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('mobile', 'LIKE', "%{$search}%")
                    ->orWhere('designation', 'LIKE', "%{$search}%")
                    ->orWhere('date_of_birth', 'LIKE', "%{$search}%")
                    ->orWhere('name_of_workplace', 'LIKE', "%{$search}%");
            }))
            ->when($filter['reason'] ?? null, function (Builder $query, $reason) {
                $query->whereHas('deletionDetail', function (Builder $q) use ($reason) {
                    $q->where('reason', $reason);
                });
            })
            ->when($filter['year'] ?? null, function (Builder $query, $year) {
                $query->whereHas('deletionDetail', function (Builder $q) use ($year) {
                    $q->where('year', $year);
                });
            })
            ->orderBy('name', 'asc');

        return response()->json([
            'list' => $employees->paginate($perPage),
        ], 200);
    }

    public function trashedEmployees() // shows PE type
    {

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-trashed-employee'), 403, 'Access Denied');

        $office = Office::whereHas('employees', function ($query) {
            $query->onlyTrashed(); // 🔥 only offices with trashed employees
        })->get();

        return Inertia::render('Backend/Employees/Index/TrashedEmployees', [
            'office' => $office,
        ]);
    }

    public function jsonTrashedEmployees(Request $request)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-trashed-employee'), 403, 'Access Denied');

        $perPage = $request->get('rowsPerPage', 5);
        $filter  = $request->get('filter', []);
        $search  = $filter['search'] ?? null;

        $employees = Employee::onlyTrashed()
            ->with(['office', 'deletionDetail'])
            ->when($search, function (Builder $builder, $search) {
                $builder->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%")
                        ->orWhere('designation', 'like', "%{$search}%")
                        ->orWhere('date_of_birth', 'like', "%{$search}%")
                        ->orWhere('name_of_workplace', 'like', "%{$search}%");
                });
            })
            ->when($filter['office'] ?? null, function (Builder $query, $officeId) {
                $query->where('office_id', $officeId);
            })
            ->when($filter['reason'] ?? null, function (Builder $query, $reason) {
                $query->whereHas('deletionDetail', function (Builder $q) use ($reason) {
                    $q->where('reason', $reason);
                });
            })
            ->orderBy('name', 'asc')
            ->paginate($perPage);

        return response()->json([
            'list' => $employees,
        ], 200);
    }


    public function masterEmployees() // shows PE type
    {

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-master-employee'), 403, 'Access Denied');

        $office = Office::whereHas('employees')->get();

        return Inertia::render('Backend/Employees/Index/MasterEmployees', [
            'office' => $office,
        ]);
    }

    public function jsonMasterEmployees(Request $request)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-master-employee'), 403, 'Access Denied');

        $perPage = $request->get('rowsPerPage', 5);
        $filter  = $request->get('filter', []);
        $search  = $filter['search'] ?? null;

        $employees = Employee::with(['office'])
            ->when($search, function (Builder $builder, $search) {
                $builder->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%")
                        ->orWhere('date_of_birth', 'like', "%{$search}%")
                        ->orWhere('parent_name', 'like', "%{$search}%");
                });
            })
            ->when($filter['office'] ?? null, function (Builder $query, $officeId) {
                $query->where('office_id', $officeId);
            })
            ->orderBy('name', 'asc')
            ->paginate($perPage);

        return response()->json([
            'list' => $employees,
        ], 200);
    }

}
