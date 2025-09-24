<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentTypeController extends Controller
{
    //
    public function index(Request $request)
    {
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('view-document-type'),403,'Access Denied');

        return inertia('Backend/DocumentType/Index',[
            'list' => DocumentType::query()->get(),
            'canDelete'=>$user->can('delete-document-type'),
            'canEdit'=>$user->can('edit-document-type'),
            'canCreate'=>$user->can('create-document-type'),
        ]);
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('create-document-type'),403,'Access Denied');

        return inertia('Backend/DocumentType/Create',[
        ]);
    }
    public function store(Request $request)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('create-document-type'),403,'Access Denied');


        $data=$this->validate($request, [
            'name'=>'required|unique:document_types,name',
            'description'=>'required',
        ]);
        DB::transaction(function () use ($data) {
            $docType=DocumentType::query()->create($data);
        });

        return to_route('document-type.index');
    }
    public function edit(Request $request,DocumentType $model)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('edit-office'),403,'Access Denied');

        return inertia('Backend/DocumentType/Edit', [
            'data' => $model
        ]);
    }
    public function update(Request $request, DocumentType $model)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('edit-document-type'),403,'Access Denied');

        $data=$this->validate($request, [
            'name'=>'required|unique:document_types',
            'description'=>'required',
        ]);

        DB::transaction(function () use ($data, $model) {
            $model->update($data);
        });

        return to_route('document-type.index');
    }

    public function destroy(Request $request,DocumentType $model)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('delete-document-type'),403,'Access Denied');

        if ($model->documents()->exists()) {
            return back()->withErrors([
                'error' => 'Cannot delete this document type because it is linked to existing employee documents.'
            ]);
        }

        // Check if deleting is going to cause data loss!!
        $model->delete();
        return to_route('document-type.index');
    }
}
