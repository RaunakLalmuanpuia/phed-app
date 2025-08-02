<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    //
    public function index(Request $request)
    {
        return inertia('Backend/Roles/Index', [
            'list' => Role::query()->get(),
        ]);
    }

    public function show(Request $request, Role $model)
    {
        return inertia('Backend/Roles/Show', [
            'data'=>$model->load(['permissions']),
            'permissions' => Permission::query()->get(),
        ]);
    }

    public function update(Request $request, Role $model)
    {
        $data=$this->validate($request, [
            'permissions' => 'required'
        ]);
        $model->syncPermissions($data['permissions']);

        return to_route('role.index');
    }
}
