<?php

namespace App\Http\Controllers;


use App\Models\Scheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SchemeController extends Controller
{
    //
    public function index(Request $request)
    {

        $user = $request->user();

        abort_if(!$user->hasPermissionTo('view-anyscheme'),403,'Access Denied');

        return inertia('Backend/Schemes/Index',[
            'list' => Scheme::query()->get(),
            'canDelete'=>$user->can('delete-scheme'),
            'canEdit'=>$user->can('edit-scheme'),
            'canCreate'=>$user->can('create-scheme'),
        ]);
    }
    public function create(Request $request)
    {

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('create-scheme'),403,'Access Denied');


        return inertia('Backend/Schemes/Create',[
        ]);
    }
    public function store(Request $request)
    {

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('create-scheme'),403,'Access Denied');


        $data=$this->validate($request, [
            'name'=>'required|unique:schemes,name',
            'description'=>'required',
        ]);

        DB::transaction(function () use ($data) {
            $scheme=Scheme::query()->create($data);
        });

        return to_route('scheme.index');
    }
    public function edit(Request $request,Scheme $model)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('edit-scheme'),403,'Access Denied');


        return inertia('Backend/Schemes/Edit', [
            'data' => $model
        ]);
    }
    public function update(Request $request, Scheme $model)
    {

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('edit-scheme'),403,'Access Denied');


        $data = $this->validate($request, [
            'name' => [
                'required',
                Rule::unique('schemes')->ignore($model->id),
            ],
            'description' => 'required',
        ]);

        DB::transaction(function () use ($data, $model) {
            $model->update($data);
        });

        return to_route('scheme.index');
    }

    public function destroy(Request $request, Scheme $model)
    {

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('delete-scheme'),403,'Access Denied');

        // Check if the scheme has employees
        if ($model->employees()->exists()) {
            return back()->withErrors([
                'error' => 'Cannot delete scheme because employees are assigned to it.'
            ]);
        }

        // Safe to delete
        $model->delete();

        return to_route('scheme.index')->with('success', 'Scheme deleted successfully.');
    }

}
