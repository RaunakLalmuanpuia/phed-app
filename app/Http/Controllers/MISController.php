<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MISController extends Controller
{

    public function import(Request $request){

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('import-employee'),403,'Access Denied');

        $office = Office::all();

        return Inertia::render('Backend/MIS/Import', [
            'office' => $office,
            'canImport'=>$user->can('import-employee'),
        ]);
    }
}
