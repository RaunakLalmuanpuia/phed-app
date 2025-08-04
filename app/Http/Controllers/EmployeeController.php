<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use App\Models\Employee;
use App\Models\Office;
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
    //
    public function indexAllEmployees() // shows all employees
    {
        $office = Office::all();
        return Inertia::render('Backend/Employees/AllEmployees', [
            'office' => $office,
        ]);
    }

    public function jsonAllEmployees(Request $request)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-allemployee'), 403, 'Access Denied');

        $perPage = $request->get('rowsPerPage') ?? 5;
        $filter = $request->get('filter', []);
        $search = $filter['search'] ?? null; // ✅ extract from filter array

        $employees = Employee::query()
            ->with('office')
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
            ->when($filter['type'] ?? null, function (Builder $query, $type) {
                $query->where('employment_type', $type);
            })
            ->when($filter['skill'] ?? null, function (Builder $query, $skill) {
                $query->where('skill_at_present', $skill);
            });

        return response()->json([
            'list' => $employees->paginate($perPage),
        ], 200);
    }

    public function indexMrEmployees() // shows MR type
    {
        $office = Office::all();

        return Inertia::render('Backend/Employees/MrEmployees', [

        ]);
    }

    public function jsonMrEmployees(Request $request)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-allemployee'), 403, 'Access Denied');

        $perPage = $request->get('rowsPerPage') ?? 5;
        $filter = $request->get('filter', []);
        $search = $filter['search'] ?? null; // ✅ extract from filter array
        $employees = Employee::query()
            ->where('employment_type', 'MR')
            ->with('office')
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
            ->when($filter['skill'] ?? null, function (Builder $query, $skill) {
                $query->where('skill_at_present', $skill);
            });

        return response()->json([
            'list' => $employees->paginate($perPage),
        ], 200);
    }

    public function indexPeEmployees() // shows PE type
    {
        $office = Office::all();

        return Inertia::render('Backend/Employees/PeEmployees', [
            'office' => $office,
        ]);
    }
    public function jsonPeEmployees(Request $request)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-allemployee'), 403, 'Access Denied');

        $perPage = $request->get('rowsPerPage') ?? 5;
        $filter = $request->get('filter', []);
        $search = $filter['search'] ?? null; // ✅ extract from filter array
        $employees = Employee::query()
            ->where('employment_type', 'PE')
            ->with('office')
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
            ->when($filter['skill'] ?? null, function (Builder $query, $skill) {
                $query->where('skill_at_present', $skill);
            });

        return response()->json([
            'list' => $employees->paginate($perPage),
        ], 200);
    }
    public function show(Request $request, Employee $model)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-employee'), 403, 'Access Denied');
        return inertia('Backend/Employees/Show', [
            'data' => $model->load(['office', 'documents.type']),
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

    public function store(Request $request){
//        dd($request->all());

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('create-employee'), 403, 'Access Denied');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:employees,email',
            'mobile' => 'required|string|max:20|unique:employees,mobile',
            'parent_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'designation' => 'required|string|max:255',
            'employment_type' => ['required', Rule::in(['MR', 'PE'])],
            'office' => 'required|exists:offices,id',
            'educational_qln' => 'required|string|max:255',
            'technical_qln' => 'required|string|max:255',
            'name_of_workplace' => 'required|string|max:255',
            'post_per_qualification' => 'required|string|max:255',
            'date_of_engagement' => 'required|date',
            'skill_category' => 'required|string|max:255',
            'skill_at_present' => 'required|string|max:255',
            'documents' => 'nullable|array',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $employee = DB::transaction(function () use ($validated, $request) {
            $employee = Employee::create([
                'employee_code'=> $this->generateEmployeeCode(),
                'office_id' => $validated['office'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'],
                'parent_name' => $validated['parent_name'],
                'date_of_birth' => $validated['date_of_birth'],
                'designation' => $validated['designation'],
                'employment_type' => $validated['employment_type'],
                'educational_qln' => $validated['educational_qln'],
                'technical_qln' => $validated['technical_qln'],
                'name_of_workplace' => $validated['name_of_workplace'],
                'post_per_qualification' => $validated['post_per_qualification'],
                'date_of_engagement' => $validated['date_of_engagement'],
                'skill_category' => $validated['skill_category'],
                'skill_at_present' => $validated['skill_at_present'],
            ]);
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $typeId => $file) {
                    if ($file) {
                        $documentType = DocumentType::find($typeId);
                        if (!$documentType) {
                            throw new \Exception("Invalid document type ID: {$typeId}");
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
            'employee'=>$employee,
            'message' =>  'Employee Created Successfully!'
        ]);


    }

    public function edit(Request $request,Employee $model)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('edit-employee'),403,'Access Denied');
        $documentTypes = DocumentType::all();
        $offices = Office::all();
        return inertia('Backend/Employees/Edit', [

            'data' => $model->load(['office', 'documents.type']),
            'documentTypes' => $documentTypes,
            'offices' => $offices,
        ]);

    }

    public function update(Request $request, Employee $model)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('edit-employee'),403,'Access Denied');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => ['required', 'string', 'max:20', Rule::unique('employees', 'mobile')->ignore($model->id)],
            'parent_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'designation' => 'required|string|max:255',
            'employment_type' => ['required', Rule::in(['MR', 'PE'])],
            'office' => 'required|exists:offices,id',
            'educational_qln' => 'required|string|max:255',
            'technical_qln' => 'required|string|max:255',
            'name_of_workplace' => 'required|string|max:255',
            'post_per_qualification' => 'required|string|max:255',
            'date_of_engagement' => 'required|date',
            'skill_category' => 'required|string|max:255',
            'skill_at_present' => 'required|string|max:255',
            'documents' => 'nullable|array',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $employee = DB::transaction(function () use ($validated, $request,$model) {
            $model->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'],
                'parent_name' => $validated['parent_name'],
                'date_of_birth' => $validated['date_of_birth'],
                'designation' => $validated['designation'],
                'employment_type' => $validated['employment_type'],
                'office_id' => $validated['office'],
                'educational_qln' => $validated['educational_qln'],
                'technical_qln' => $validated['technical_qln'],
                'name_of_workplace' => $validated['name_of_workplace'],
                'post_per_qualification' => $validated['post_per_qualification'],
                'date_of_engagement' => $validated['date_of_engagement'],
                'skill_category' => $validated['skill_category'],
                'skill_at_present' => $validated['skill_at_present'],
            ]);

            // Save documents
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $typeId => $file) {
                    if ($file) {
                        $documentType = DocumentType::find($typeId);

                        if (!$documentType) {
                            throw new \Exception("Invalid document type ID: {$typeId}");
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
            'employee'=>$employee,
            'message' =>  'Employee Edited Successfully!'
        ]);

    }

}
