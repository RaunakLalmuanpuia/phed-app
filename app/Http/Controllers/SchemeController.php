<?php

namespace App\Http\Controllers;


use App\Models\Scheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchemeController extends Controller
{
    //
    public function index(Request $request)
    {

        return inertia('Backend/Schemes/Index',[
            'list' => Scheme::query()->get(),
        ]);
    }
    public function create(Request $request)
    {

        return inertia('Backend/Schemes/Create',[
        ]);
    }
    public function store(Request $request)
    {

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

        return inertia('Backend/Schemes/Edit', [
            'data' => $model
        ]);
    }
    public function update(Request $request, Scheme $model)
    {

        $data=$this->validate($request, [
            'name'=>'required|unique:schemes',
            'description'=>'required',
        ]);

        DB::transaction(function () use ($data, $model) {
            $model->update($data);
        });

        return to_route('scheme.index');
    }

    public function destroy(Request $request,Scheme $model)
    {

        $model->delete();
        return to_route('scheme.index');
    }
}
