<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use App\Models\EditRequest;
use App\Models\EditRequestDocument;
use App\Models\Employee;
use Illuminate\Http\Request;

class EditRequestController extends Controller
{
    //
    // Submit a new edit request
//    public function request(Request $request, Employee $model)
//    {
//        $validated = $request->validate([
//            'requested_changes' => 'required|array',
//        ]);
//
//        $changes = collect($validated['requested_changes'])
//            ->filter(fn($value) => !is_null($value) && $value !== '') // remove empty
//            ->toArray();
//
//        $editRequest = EditRequest::create([
//            'employee_id' => $model->id,
//            'requested_changes' => json_encode($changes),
//            'request_date' => now(),
//            'approval_status' => 'pending',
//        ]);
//
//        return redirect()->back()->with('success', 'Edit request submitted.');
//
//    }
    public function request(Request $request, Employee $model)
    {
        $validated = $request->validate([
            'requested_changes' => 'required|array',
            'documents' => 'nullable|array',
        ]);

        $changes = collect($validated['requested_changes'])
            ->filter(fn($v) => !is_null($v) && $v !== '')
            ->toArray();

        $editRequest = EditRequest::create([
            'employee_id' => $model->id,
            'requested_changes' => json_encode($changes),
            'request_date' => now(),
            'approval_status' => 'pending',
        ]);

        if ($request->has('documents')) {
            foreach ($request->documents as $field => $files) {
                foreach ($files as $file) {
                    $path = $file->store('edit_request_documents');
                    $docType = DocumentType::where('name', $field)->first(); // map to DocumentType
                    EditRequestDocument::create([
                        'edit_request_id' => $editRequest->id,
                        'employee_id' => $model->id,
                        'document_type_id' => $docType->id ?? null,
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'mime' => $file->getClientMimeType(),
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Edit request submitted.');
    }

    // Approve request
//    public function approve(EditRequest $model)
//    {
//        // Apply changes to employee
//        $model->employee->update(json_decode($model->requested_changes, true));
//
//        // Update request status
//        $model->update([
//            'approval_status' => 'approved',
//            'approval_date' => now(),
//        ]);
//
//        return redirect()->back()->with('success', 'Edit request approved.');
//
//    }

    public function approve(EditRequest $model)
    {
        $changes = json_decode($model->requested_changes, true);
        $employee = $model->employee;

        // Update employee
        $employee->update($changes);

        // Copy or update documents
        foreach ($model->attachments as $attachment) {
            $employee->documents()->updateOrCreate(
                ['document_type_id' => $attachment->document_type_id],
                [
                    'name' => $attachment->name,
                    'path' => $attachment->path,
                    'mime' => $attachment->mime,
                    'upload_date' => now(),
                ]
            );
        }

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
