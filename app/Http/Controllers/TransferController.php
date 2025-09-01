<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Transfer;
use App\Models\TransferRequest;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    //

    public function store(Request $request,Employee $model){

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('transfer-employee'), 403, 'Access Denied');

        $validated = $request->validate([
            'is_present_transfer' => 'required|boolean',
            'old_office_id.id' => 'required|exists:offices,id',
            'new_office_id.id' => 'required|exists:offices,id',
            'transfer_date' => 'required|date',
            'supporting_document' => 'required|file|max:2048',
        ]);

        if ($validated['is_present_transfer']) {
            $model->update([
                'office_id' => $validated['new_office_id']['id']
            ]);
        }

        $documentPath = null;
        if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');
            $filename = 'transfer_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $documentPath = $file->storeAs('transfers', $filename, 'public');
        }

        // Create the transfer
        Transfer::create([
            'employee_id' => $model->id,
            'old_office_id' => $validated['old_office_id']['id'],
            'new_office_id' => $validated['new_office_id']['id'],
            'transfer_date' => $validated['transfer_date'],
            'supporting_document' => $documentPath,
        ]);

        return redirect()->back()->with('success', 'Transfer successfully recorded.');
    }

    public function destroy(Transfer $model)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('transfer-employee'), 403, 'Access Denied');

//        dd($model);
        $model->delete();

        return redirect()->back()->with('success', 'Transfer successfully Deleted.');
    }

    public function request(Request $request, Employee $model){
//        dd($request->all());

        $request->validate([
            'requested_office_id.id' => 'required|exists:offices,id',
            'supporting_document' => 'required|file|max:2048',
        ]);

        // Handle file upload if exists
        $documentPath = null;
        if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');
            $filename = 'transfer_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $documentPath = $file->storeAs('transfers', $filename, 'public');
        }

        // Create the transfer request
        $transferRequest = TransferRequest::create([
            'employee_id' => $model->id,
            'current_office_id' => $model->office_id, // current office
            'requested_office_id' => $request->requested_office_id['id'],
            'request_date' => now(),
            'approval_status' => 'pending',
            'supporting_document' => $documentPath,
        ]);
        return redirect()->back()->with('success', 'Transfer request successfully recorded.');
    }

    public function approve(TransferRequest $model)
    {
        $model->update([
            'approval_status' => 'approved',
            'approval_date' => now(),
        ]);

//        dd($model);
        // Create actual transfer record
        Transfer::create([
            'employee_id' => $model->employee_id,
            'old_office_id' => $model->current_office_id,
            'new_office_id' => $model->requested_office_id,
            'supporting_document' => $model->supporting_document,
            'transfer_date' => now(),
        ]);


        // Update employee's office
        $model->employee->update([
            'office_id' => $model->requested_office_id,
        ]);

        return redirect()->back()->with('success', 'Transfer request approved and employee transferred.');

    }

    public function reject(TransferRequest $model)
    {
        $model->update([
            'approval_status' => 'rejected',
            'approval_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Transfer request rejected');

    }
}
