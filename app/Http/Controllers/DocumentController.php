<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentEditRequest;
use App\Models\DocumentType;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    //

    public function request(Request $request, Employee $model)
    {
        $user = $request->user();

        abort_if(!$user->hasPermissionTo('request-document-edit'),403,'Access Denied');

        $validated = $request->validate([
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        $requestRecord = DocumentEditRequest::create([
            'employee_id' => $model->id,
            'request_date' => now(),
            'approval_status' => 'pending',
        ]);

        foreach ($validated['files'] as $documentTypeId => $file) {

            $docType = DocumentType::where('id', $documentTypeId)->first();

            $randomString = \Str::random(8); // Laravel helper for random string
            $extension = $file->getClientOriginalExtension();
            $generatedName = $docType->name . '_' . $randomString . '.' . $extension;

            $path = $file->storeAs('edit_request_documents', $generatedName, 'public');

            $requestRecord->files()->create([
                'document_type_id' => $documentTypeId,
                'path' => $path,
                'name' => $generatedName,
                'mime' =>$file->getClientMimeType(),
                'type' =>$docType->name,
            ]);
        }

        return redirect()->back()->with('success', 'Document edit request submitted!');
    }
    // Approve request
    public function approve(Request $request, DocumentEditRequest $model)
    {
        $user = $request->user();

        abort_if(!$user->hasPermissionTo('approve-document-edit'),403,'Access Denied');
        // Loop through all files attached to this request
        foreach ($model->files as $file) {
            // update or create the document for the employee and document type
            Document::updateOrCreate(
                [
                    'employee_id' => $model->employee_id,
                    'document_type_id' => $file->document_type_id,
                ],
                [
                    'name' => $file->name,
                    'mime' => $file->mime,
                    'path' => $file->path,
                    'type' => $file->type,
                    'upload_date' => now(),
                ]
            );
        }

        // mark the request as approved
        $model->update([
            'approval_status' => 'approved',
            'approval_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Document edit request approved and documents updated.');
    }





    // Reject request
    public function reject(Request $request,DocumentEditRequest $model)
    {
        $user = $request->user();

        abort_if(!$user->hasPermissionTo('approve-document-edit'),403,'Access Denied');

        $model->update([
            'approval_status' => 'rejected',
            'approval_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Document update request rejected.');

    }

    public function deleteEmployeeDocument(Request $request, Document $model)
    {

        $user = $request->user();

        abort_if(!$user->hasPermissionTo('delete-document'),403,'Access Denied');

        if ($model->path && Storage::disk('public')->exists($model->path)) {
            Storage::disk('public')->delete($model->path);
        }

        // Delete DB record
        $model->delete();

        return redirect()->back()->with('success', 'Document deleted successfully.');


    }
}
