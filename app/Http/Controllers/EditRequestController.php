<?php

namespace App\Http\Controllers;

use App\Models\EditRequest;
use App\Models\Employee;
use Illuminate\Http\Request;

class EditRequestController extends Controller
{
    //
    // Submit a new edit request
    public function request(Request $request, Employee $model)
    {
        $validated = $request->validate([
            'requested_changes' => 'required|array',
        ]);

        $changes = collect($validated['requested_changes'])
            ->filter(fn($value) => !is_null($value) && $value !== '') // remove empty
            ->toArray();

        $editRequest = EditRequest::create([
            'employee_id' => $model->id,
            'requested_changes' => json_encode($changes),
            'request_date' => now(),
            'approval_status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Edit request submitted.');

    }

    // Approve request
    public function approve(EditRequest $model)
    {
        // Apply changes to employee
        $model->employee->update(json_decode($model->requested_changes, true));

        // Update request status
        $model->update([
            'approval_status' => 'approved',
            'approval_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Edit request approved.');

    }

    // Reject request
    public function reject(EditRequest $model)
    {
        $model->update([
            'approval_status' => 'rejected',
            'approval_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Edit request rejected.');
    }


}
