<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\RemunerationDetail;
use Illuminate\Http\Request;

class RemunerationController extends Controller
{
    //
    public function store(Request $request, Employee $model){

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('create-remuneration'), 403, 'Access Denied');

        $validated = $request->validate([
            'remuneration' => 'required|numeric|min:0',
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
