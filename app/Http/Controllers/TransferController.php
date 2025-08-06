<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Transfer;
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
            'transfer_date' => 'required|date'
        ]);

        if ($validated['is_present_transfer']) {
            $model->update([
                'office_id' => $validated['new_office_id']['id']
            ]);
        }
        // Create the transfer
        Transfer::create([
            'employee_id' => $model->id,
            'old_office_id' => $validated['old_office_id']['id'],
            'new_office_id' => $validated['new_office_id']['id'],
            'transfer_date' => $validated['transfer_date'],
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
}
