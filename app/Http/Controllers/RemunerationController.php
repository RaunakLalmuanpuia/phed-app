<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Office;
use App\Models\RemunerationDetail;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RemunerationController extends Controller
{
    //

    public function remunerationDetail(Request $request){
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('generate-remuneration'),403,'Access Denied');

        $office = Office::whereHas('employees', function($query) {
            $query->where('employment_type', 'PE');
        })->get();



        return Inertia::render('Backend/Remunerations/Detail', [
            'office' => $office,
            'canGenerateRemuneration'=>$user->can('generate-remuneration'),
            'canCreateRemuneration'=>$user->can('create-remuneration'),
            'canEditRemuneration'=>$user->can('edit-remuneration'),
            'canBulkUpdateRemuneration'=>$user->can('bulk-update-remuneration'),

        ]);
    }

    public function jsonRemunerationDetail(Request $request)
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

    public function remunerationSummary(Request $request){
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('view-remuneration'),403,'Access Denied');

        $office = Office::whereHas('employees', function($query) {
            $query->where('employment_type', 'PE');
        })->get();



        return Inertia::render('Backend/Remunerations/Summary', [
            'office' => $office,
            'canGenerateRemuneration'=>$user->can('generate-remuneration'),
        ]);
    }


    public function jsonRemunerationSummary(Request $request)
    {
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('view-remuneration'),403,'Access Denied');

        // Fetch only offices that have employees with employment_type = 'PE'
        $offices = Office::whereHas('employees', function ($query) {
            $query->where('employment_type', 'PE');
        })
            ->with(['employees' => function ($query) {
                $query->where('employment_type', 'PE')
                    ->with('remunerationDetail');
            }])
            ->get();

        // Prepare the rows
        $rows = $offices->map(function ($office) {
            $employeeCount = $office->employees->count();

            $oneMonthWages = $office->employees->sum(function ($employee) {
                return optional($employee->remunerationDetail)->round_total ?? 0;
            });

            return [
                'office_name'    => $office->name,
                'employee_count' => $employeeCount,
                'one_month'      => $oneMonthWages,
                'three_months'   => $oneMonthWages * 3,
                'six_months'     => $oneMonthWages * 6,
                'one_year'       => $oneMonthWages * 12,
            ];
        });

        // Calculate totals
        $totals = [
            'office_name'    => 'TOTAL',
            'employee_count' => $rows->sum('employee_count'),
            'one_month'      => $rows->sum('one_month'),
            'three_months'   => $rows->sum('three_months'),
            'six_months'     => $rows->sum('six_months'),
            'one_year'       => $rows->sum('one_year'),
        ];

        return response()->json([
            'data'   => $rows,
            'totals' => $totals
        ]);
    }


    public function store(Request $request, Employee $model){

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('create-remuneration'), 403, 'Access Denied');

        $validated = $request->validate([
            'remuneration' => 'required|numeric|min:0',
            'pay_matrix'=> 'required|string',
            'next_increment_date' => 'required|date'
        ]);

        $model->remunerationDetail()->create($validated);

        return redirect()->back()->with('success', 'Remuneration successfully recorded.');
    }
    public function update(Request $request, Employee $model)
    {

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('edit-remuneration'), 403, 'Access Denied');

        $validated = $request->validate([
            'remuneration' => 'required|numeric|min:0',
            'pay_matrix'=> 'required|string',
            'next_increment_date' => 'required|date'
        ]);

        $remunerationDetail = $model->remunerationDetail; // Get model instance

        if ($remunerationDetail) {
            $remunerationDetail->fill($validated);
            $remunerationDetail->save(); // This triggers the saving event in boot()
        }

        return back()->with('success', 'Remuneration successfully updated.');
    }


    public function bulkUpdate(Request $request)
    {


        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('bulk-update-remuneration'), 403, 'Access Denied');

        $validated = $request->validate([
            'employee_ids' => ['required', 'array', 'min:1'],
            'employee_ids.*' => ['integer', 'exists:employees,id'],
            'next_increment_date' => ['required', 'date'],
        ]);

        $ids = $validated['employee_ids'];
        $date = $validated['next_increment_date'];

        RemunerationDetail::whereIn('employee_id', $ids)->get()->each(function ($rem) use ($date) {

            $rem->remuneration = $rem->total;
            $rem->next_increment_date = $date;
            $rem->save(); // triggers boot() recalculation
        });

        return back()->with('success', 'Employees updated successfully!');
    }
}
