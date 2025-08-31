<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentEditRequest;
use App\Models\DocumentType;
use App\Models\Employee;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    //
    public function request(Request $request, Employee $model)
    {
        // Validate that at least one file is uploaded
        $validated = $request->validate([
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'], // 2MB max per file
        ]);

        foreach ($validated['files'] as $documentTypeId => $file) {
            if (!$file) {
                continue;
            }

            $docType = DocumentType::where('id', $documentTypeId)->first();

            $randomString = \Str::random(8); // Laravel helper for random string
            $extension = $file->getClientOriginalExtension();
            $generatedName = $docType->name . '_' . $randomString . '.' . $extension;

            $path = $file->storeAs('edit_request_documents', $generatedName, 'public');





            DocumentEditRequest::create([
                'employee_id' => $model->id,
                'document_type_id' => $documentTypeId,
                'mime' =>$file->getClientMimeType(),
                'path' => $path,
                'name' => $generatedName,
                'type' =>$docType->name,
                'request_date' => now(),
                'approval_status' => 'pending', // initial status
            ]);
        }

        return redirect()->back()->with('success', 'Document edit request submitted successfully!');
    }

    // Approve request
    public function approve(DocumentEditRequest $model)
    {
        // update or create document
        Document::updateOrCreate(
            [
                'employee_id' => $model->employee_id,
                'document_type_id' => $model->document_type_id,
            ],
            [
                'name' => $model->name,
                'mime' => $model->mime,
                'path' => $model->path,
                'type' => $model->type,
                'upload_date' => now(),
            ]
        );

        $model->update([
            'approval_status' => 'approved',
            'approval_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Document approved and updated.');

    }

    // Reject request
    public function reject(DocumentEditRequest $model)
    {
        $model->update([
            'approval_status' => 'rejected',
            'approval_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Document update request rejected.');

    }
}
