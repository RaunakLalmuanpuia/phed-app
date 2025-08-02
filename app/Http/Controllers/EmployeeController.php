<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Office;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Builder;
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

        $perPage = $request->get('rowsPerPage') ?? 15;
        $filter = $request->get('filter', []);
        $search = $request->get('search');
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
        $employees = Employee::with('office')
            ->where('employment_type', 'MR')
            ->get();

        return Inertia::render('Backend/Employees/MrEmployees', [
            'employees' => $employees,
        ]);
    }

    public function indexPeEmployees() // shows PE type
    {
        $employees = Employee::with('office')
            ->where('employment_type', 'PE')
            ->get();

        return Inertia::render('Employees/Backend/PeEmployees', [
            'employees' => $employees,
        ]);
    }
    public function show(Request $request, Employee $model)
    {
        return inertia('Backend/Employees/Show', [
            'data'=>$model->load('office'),
        ]);
    }
}
