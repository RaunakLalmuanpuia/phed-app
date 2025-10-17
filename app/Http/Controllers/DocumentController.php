<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentEditRequest;
use App\Models\DocumentType;
use App\Models\Employee;
use App\Models\DocumentDeleteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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

            $path = $file->storeAs('document_edit_request', $generatedName, 'public');

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

            $fileName = $file->name;

            // New storage path under employee_documents/
            $newPath = 'documents/' . $fileName;

            // Copy file from edit_request_documents → employee_documents
            if (Storage::disk('public')->exists($file->path)) {
                Storage::disk('public')->copy($file->path, $newPath);
            }

            // update or create the document for the employee and document type
            Document::updateOrCreate(
                [
                    'employee_id' => $model->employee_id,
                    'document_type_id' => $file->document_type_id,
                ],
                [
                    'name' => $file->name,
                    'mime' => $file->mime,
                    'path' => $newPath,
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

    public function updateEmployeeDocument(Request $request, Employee $model)
    {
//        dd($request);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => ['required', 'string', 'max:20', Rule::unique('employees', 'mobile')->ignore($model->id)],
            'parent_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'address' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'post_assigned' => 'nullable|string|max:255',
            'educational_qln' => 'required|string|max:255',
            'technical_qln' => 'nullable|string|max:255',
            'name_of_workplace' => 'nullable|string|max:255',
            'post_per_qualification' => 'nullable|string|max:255',
            'engagement_card_no' => 'nullable|string|max:255',
            'date_of_engagement' => 'nullable|date',
            'skill_category' => 'nullable|string|max:255',
            'skill_at_present' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:10240', // 800KB limit
            'documents' => 'nullable|array',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);


        // ✅ Update employee basic details
        $model->update($validated);


        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $typeId => $file) {
                if ($file) {
                    $documentType = DocumentType::find($typeId);
                    if (!$documentType) {
                        return response()->json([
                            'message' => "Invalid document type ID: {$typeId}"
                        ], 422);
                    }

                    $randomString = \Str::random(8);
                    $extension = $file->getClientOriginalExtension();
                    $generatedName = $documentType->name . '_' . $randomString . '.' . $extension;

                    $path = $file->storeAs('documents', $generatedName, 'public');

                    $model->documents()->updateOrCreate(
                        ['document_type_id' => $typeId],
                        [
                            'type' => $documentType->name,
                            'name' => $generatedName,
                            'mime' => $file->getClientMimeType(),
                            'path' => $path,
                            'upload_date' => now(),
                        ]
                    );
                }
            }
        }

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');

            $randomString = \Str::random(8);
            $extension = $file->getClientOriginalExtension();
            $generatedName = 'avatar_' . $randomString . '.' . $extension;

            // Store the avatar in the 'pictures' directory (public disk)
            $avatarPath = $file->storeAs('pictures', $generatedName, 'public');
            // Store path relative to 'storage' (e.g., for public URL access)
            $model->avatar = $avatarPath;
            $model->update();
        }



        return response()->json([
            'employee' => $model->load('documents.type'),
            'message' => 'Employee Document Updated Successfully!'
        ]);
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

    public function requestDelete(Request $request, Employee $model)
    {

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('request-document-delete'), 403, 'Access Denied');

        $validated = $request->validate([
            'document_id' => ['required', 'exists:documents,id'],
        ]);

        // Load the document with its type
        $document = Document::with('type')->findOrFail($validated['document_id']);

        // Prevent multiple pending delete requests for same document
        $alreadyPending = DocumentDeleteRequest::where('document_id', $validated['document_id'])
            ->where('approval_status', 'pending')
            ->exists();

        if ($alreadyPending) {
            return redirect()->back()->with('warning', 'Delete request already pending for this document.');
        }

        DocumentDeleteRequest::create([
            'employee_id' => $model->id,
            'document_type_name' => $document->type,
            'document_id' => $validated['document_id'],
            'request_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Document delete request submitted!');
    }

    public function approveDelete(Request $request, DocumentDeleteRequest $model)
    {
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('approve-document-delete'), 403, 'Access Denied');

        $document = $model->document;

        if ($document && Storage::disk('public')->exists($document->path)) {
            Storage::disk('public')->delete($document->path);
        }

        $document->delete();

        $model->update([
            'approval_status' => 'approved',
            'approval_date' => now(),
        ]);


        return redirect()->back()->with('success', 'Document deleted successfully.');
    }


    public function rejectDelete(Request $request, DocumentDeleteRequest $model)
    {
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('approve-document-delete'), 403, 'Access Denied');

        $model->update([
            'approval_status' => 'rejected',
            'approval_date' => now(),
        ]);

        return redirect()->back()->with('info', 'Document delete request rejected.');
    }


}
