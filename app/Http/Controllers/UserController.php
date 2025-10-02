<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    //
    public function index(Request $request)
    {
        $user = $request->user();

        abort_if(!$user->hasPermissionTo('view-anyuser'),403,'Access Denied');

        return inertia('Backend/User/Index',[
            'canDelete'=>$user->can('delete-user'),
            'canEdit'=>$user->can('edit-user'),
            'canCreate'=>$user->can('create-user'),
        ]);
    }

    public function jsonAll(Request $request)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-anyuser'),403,'Access Denied');

        $perPage = $request->get('rowsPerPage') ?? 15;
        $filter = $request->get('filter');
        return response()->json([
            'list' => User::query()
                ->with(['roles','offices'])
                ->when($filter,fn(Builder $builder)=>$builder->where('name','LIKE',"%$filter%"))
                ->paginate($perPage),
        ],200);
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('create-user'),403,'Access Denied');

        return inertia('Backend/User/Create',[
            'userRoles'=>Role::query()->get(['name as value','name as label']),
            'offices'=>Office::query()->get(['name as label','id as value']),
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('create-user'),403,'Access Denied');


        $roles = $request->get('roles', []);

        $data=$this->validate($request, [
            'name'=>'required',
            'designation'=>'required',
            'mobile'=>'required|digits:10|unique:users',
            'email'=>'required|email|unique:users',
            'password'=>'required|confirmed',
            'office_id' => 'nullable',
        ]);

        $roles = $request->get('roles');
        $mergedData = array_merge($data, ['password' => Hash::make($data["password"])]);
        DB::transaction(function () use ($roles, $request, $mergedData) {
            $user=User::query()->create($mergedData);
            if (!empty($mergedData['office_id'])) {
                $user->offices()->sync([$mergedData['office_id']]);
            }
            if ($roles) {
                $user->assignRole($roles);
            }
        });


        return to_route('user.index');
    }

    public function edit(Request $request,User $model)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('edit-user'),403,'Access Denied');

        return inertia('Backend/User/Edit', [
            'userRoles' => Role::query()->get(['name as value', 'name as label']),
            'current_offices' => $model->offices()->get(['name as label','offices.id as value']),
            'data' => $model->load(['roles']),
            'offices'=>Office::query()->get(['name as label','id as value'])
        ]);


    }

    public function update(Request $request, User $model)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('edit-user'),403,'Access Denied');

//        dd($request->all());
        $data=$this->validate($request, [
            'name'=>'required',
            'designation'=>'required',
            'mobile'=>['required','digits:10',Rule::unique('users','mobile')->ignore($model->id)],
            'email'=>['required','email',Rule::unique('users','email')->ignore($model->id)],
            'office_ids' => ['nullable'],
        ]);
        $roles = $request->get('roles');
        $password = $request->get('password');
        DB::transaction(function () use ($model, $password, $roles, $request, $data) {
            $model->update($data);
            $model->offices()->sync($data['office_ids']);
            if ($roles) {
                $model->syncRoles($roles);

            }
            if ($password) {
                $model->password = Hash::make($password);
                $model->save();
            }

        });

        return to_route('user.index');
    }

    public function show(Request $request, User $model)
    {
        return inertia('Backend/User/Show', [
            'data'=>$model->load(['roles', 'offices']),
        ]);
    }

    public function destroy(Request $request,User $model)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('delete-user'),403,'Access Denied');

        $model->delete();
        return to_route('user.index');
    }
}
