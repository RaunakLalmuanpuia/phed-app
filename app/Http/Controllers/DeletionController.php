<?php

namespace App\Http\Controllers;

use App\Models\DeletionDetail;
use App\Models\DeletionRequest;
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
            'seniority_list' => 'nullable|string|max:1000',
            'reason' => 'required|string|max:255',
            'year' => 'nullable|integer',
            'remark' => 'nullable|string',
            'supporting_document' => 'required|file|mimes:pdf,jpg,jpeg,png',
        ]);

        if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');
            $filename = 'deletion_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $validated['supporting_document'] = $file->storeAs('deletions', $filename, 'public');
        }

        $validated['employee_id'] = $model->id;

        DeletionDetail::create($validated);

        // If employment_type is NOT "PE", delete the employee and redirect
        if ($model->employment_type !== 'PE') {
            // Delete employee
            $model->delete();

            return redirect()->route('employees.all')
                ->with('success', 'Employee moved to Trash.');
        }

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
            'seniority_list' => 'nullable|string|max:255',
            'year' => 'nullable|required|integer',
            'remark' => 'nullable|string',
            'supporting_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
        ]);

        if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');
            $filename = 'deletion_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $validated['supporting_document'] = $file->storeAs('deletions', $filename, 'public');
        }
        else {
            // Remove supporting_document from $validated so it doesn't overwrite existing
            unset($validated['supporting_document']);
        }

        $model->update($validated);

        return  redirect()->back()->with('success', 'Deletion detail updated.');
    }



    public function request(Request $request, Employee $model){

        $request->validate([
            'reason' => 'required|string|max:255',
            'seniority_list' => 'nullable|string',
            'year' => 'nullable|integer',
            'remark' => 'nullable|string',
            'supporting_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Handle file upload if exists
        $documentPath = null;
        if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');
            $filename = 'deletion_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $documentPath = $file->storeAs('deletions', $filename, 'public');
        }

        // Store everything in reason as JSON
        $deletionRequest = DeletionRequest::create([
            'employee_id' => $model->id,
            'reason' => json_encode([
                'reason' => $request->reason,
                'seniority_list' => $request->seniority_list,
                'year' => $request->year,
                'remark' => $request->remark,
                'supporting_document' => $documentPath,
            ]),
            'request_date' => now(),
            'approval_status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Deletion request submitted successfully.');

    }
    // 2. Approve deletion (auto create DeletionDetail from JSON)
    public function approve(DeletionRequest $model)
    {
//        dd($model);
        $employee = $model->employee;

        // Decode stored details from JSON
        $details = json_decode($model->reason, true);

        // Update request status
        $model->update([
            'approval_status' => 'approved',
            'approval_date' => now(),
        ]);



        // Create DeletionDetail from JSON
        $detail = DeletionDetail::create([
            'employee_id' => $employee->id,
            'seniority_list' => $details['seniority_list'] ?? null,
            'reason' => $details['reason'] ?? null,
            'year' => $details['year'] ?? null,
            'remark' => $details['remark'] ?? null,
            'supporting_document' => $details['supporting_document'] ?? null,
        ]);

        // If employment_type is NOT "PE", delete the employee and redirect
        if ($employee->employment_type !== 'PE') {
            // Delete employee
            $employee->delete();

            return redirect()->route('employees.all')
                ->with('success', 'Employee moved to Trash.');
        }

        // Update employee
        $employee->update([
            'employment_type' => 'Deleted',
        ]);

        return redirect()->back()->with('success', 'Deletion request approved.');

    }

    public function reject(DeletionRequest $model)
    {
        $model->update([
            'approval_status' => 'rejected',
            'approval_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Deletion request rejected.');

    }

}
