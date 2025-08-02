<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class OfficeController extends Controller
{
    //
    public function index(Request $request)
    {
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('view-office'),403,'Access Denied');

        return inertia('Backend/Office/Index',[
            'canDelete'=>$user->can('delete-office'),
            'canEdit'=>$user->can('edit-office'),
            'canCreate'=>$user->can('create-office'),
        ]);
    }

    public function jsonAll(Request $request)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-anyoffice'),403,'Access Denied');

        $perPage = $request->get('rowsPerPage') ?? 15;
        $filter = $request->get('filter');
        return response()->json([
            'list' => Office::query()
                ->when($filter,fn(Builder $builder)=>$builder->where('name','LIKE',"%$filter%"))
                ->paginate($perPage),
        ],200);
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('create-office'),403,'Access Denied');

        return inertia('Backend/Office/Create',[
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('create-office'),403,'Access Denied');


        $data=$this->validate($request, [
            'name'=>'required|unique:offices',
            'type'=>'required',
            'location'=>'required',

        ]);
        DB::transaction(function () use ($data) {
            $office=Office::query()->create($data);
        });

        return to_route('office.index');
    }

    public function edit(Request $request,Office $model)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('edit-office'),403,'Access Denied');

        return inertia('Backend/Office/Edit', [
            'data' => $model
        ]);
    }

    public function update(Request $request, Office $model)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('edit-office'),403,'Access Denied');

        $data=$this->validate($request, [
            'name'=>'required|unique:offices',
            'type'=>'required',
            'location'=>'required',
        ]);

        DB::transaction(function () use ($data, $model) {
            $model->update($data);
        });

        return to_route('office.index');
    }

    public function show(Request $request, Office $model)
    {
        return inertia('Backend/Office/Show', [
            'data'=>$model,
        ]);
    }

    public function destroy(Request $request,Office $model)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('delete-office'),403,'Access Denied');

        // Check if deleting is going to cause data loss!!
        $model->delete();
        return to_route('office.index');
    }


}
