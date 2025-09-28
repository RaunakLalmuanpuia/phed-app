<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use App\Models\EditRequest;
use App\Models\EditRequestDocument;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EditRequestController extends Controller
{
    //

    public function request(Request $request, Employee $model)
    {
        $validated = $request->validate([
            'requested_changes' => 'required|array',
            'documents' => 'nullable|array',
        ]);

        $changes = collect($validated['requested_changes'])
            ->filter(fn($v) => !is_null($v) && $v !== '')
            ->toArray();

        // Field → Required documents
        $fieldToDocuments = [
            'date_of_birth'   => ['Birth Certificate', 'Aadhar', 'EPIC', 'Educational Certificate'],
            'name'            => ['Birth Certificate', 'Aadhar', 'EPIC', 'Educational Certificate'],
            'parent_name'     => ['Birth Certificate'],
            'educational_qln' => ['Educational Certificate'],
            'technical_qln'   => ['Technical Certificate'],
        ];

        // Collect required docs
        $requiredDocTypes = collect($changes)
            ->keys()
            ->flatMap(fn($field) => $fieldToDocuments[$field] ?? [])
            ->unique()
            ->values();

        // 🔹 Validate that all required docs exist
        foreach ($requiredDocTypes as $docTypeName) {
            $docType = DocumentType::where('name', $docTypeName)->first();
            if (!$docType) {
                return back()->withErrors([
                    'documents' => "Document type '{$docTypeName}' not found in system."
                ]);
            }

            $fileInput = $request->file("documents.$docTypeName");

            if (!$fileInput) {
                return back()->withErrors([
                    'documents' => "Please upload required document: {$docTypeName}."
                ]);
            }

            // Normalize to array
            $files = is_array($fileInput) ? $fileInput : [$fileInput];

            if (count($files) === 0) {
                return back()->withErrors([
                    'documents' => "Please upload required document: {$docTypeName}."
                ]);
            }
        }
        $previousData = [];
        foreach (array_keys($changes) as $field) {
            $previousData[$field] = $model->{$field};
        }
        // 🔹 Save edit request
        $editRequest = EditRequest::create([
            'employee_id'       => $model->id,
            'previous_data'     => json_encode($previousData),
            'requested_changes' => json_encode($changes),
            'request_date'      => now(),
            'approval_status'   => 'pending',
        ]);

        // 🔹 Save uploaded documents
        if ($request->has('documents')) {
            foreach ($request->documents as $field => $files) {
                // normalize to array
                $files = is_array($files) ? $files : [$files];

                foreach ($files as $file) {


                    $docType = DocumentType::where('name', $field)->first();

                    $randomString = \Str::random(8); // Laravel helper for random string
                    $extension = $file->getClientOriginalExtension();
                    $generatedName = $docType->name . '_' . $randomString . '.' . $extension;

                    $path = $file->storeAs('edit_request_documents', $generatedName, 'public');

                    EditRequestDocument::create([
                        'edit_request_id'  => $editRequest->id,
                        'employee_id'      => $model->id,
                        'document_type_id' => $docType->id ?? null,
                        'name'             => $generatedName,
                        'path'             => $path,
                        'mime'             => $file->getClientMimeType(),
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Edit request submitted.');
    }





    public function approve(EditRequest $model)
    {
        $changes = json_decode($model->requested_changes, true);
        $employee = $model->employee;

        // Update employee fields
        $employee->update($changes);

        // Save documents from request attachments into employee documents
        foreach ($model->attachments as $attachment) {

            // Keep the same filename
            $fileName = $attachment->name;

            // New storage path under employee_documents/
            $newPath = 'documents/' . $fileName;

            // Copy file from edit_request_documents → employee_documents
            if (Storage::disk('public')->exists($attachment->path)) {
                Storage::disk('public')->copy($attachment->path, $newPath);
            }


            $employee->documents()->updateOrCreate(
                [
                    'document_type_id' => $attachment->document_type_id
                ],
                [
                    'name'        => $attachment->name,
                    'path'        => $newPath,
                    'mime'        => $attachment->mime,
                    'upload_date' => now(),
                    'type' => $attachment->document_type_id,
                ]
            );
        }

        // Mark request as approved
        $model->update([
            'approval_status' => 'approved',
            'approval_date'   => now(),
        ]);

        return redirect()->back()->with('success', 'Edit request approved and documents saved.');
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
