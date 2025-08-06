<?php

namespace App\Http\Controllers;

use App\Models\DeletionDetail;
use App\Models\Employee;
use Illuminate\Http\Request;

class DeletionController extends Controller
{
    //
    public function store(Request $request, Employee $model)
    {
//        dd($model);

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('delete-employee'), 403, 'Access Denied');

        $validated = $request->validate([
            'seniority_list' => 'required|string|max:1000',
            'reason' => 'required|string|max:255',
            'year' => 'nullable|integer',
            'remark' => 'nullable|string',
            'supporting_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
        ]);

        if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');
            $filename = 'deletion_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $validated['supporting_document'] = $file->storeAs('deletions', $filename, 'public');
        }

        $validated['employee_id'] = $model->id;

        DeletionDetail::create($validated);

        // Optionally update employment_type
        $model->update(['employment_type' => 'Deleted']);

        return redirect()->back()->with('success', 'Employee successfully Deleted.');
    }

    public function update(Request $request, DeletionDetail $model)
    {

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('edit-delete'), 403, 'Access Denied');

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'seniority_list' => 'required|string|max:255',
            'year' => 'required|integer',
            'remark' => 'nullable|string',
            'supporting_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
        ]);

        if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');
            $filename = 'deletion_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $validated['supporting_document'] = $file->storeAs('deletions', $filename, 'public');
        }

        $model->update($validated);

        return  redirect()->back()->with('success', 'Deletion detail updated.');
    }

}
