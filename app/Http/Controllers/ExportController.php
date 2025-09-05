<?php

namespace App\Http\Controllers;

use App\Exports\AllEmployeesExport;
use App\Exports\MusterRollEmployeesExport;
use App\Exports\RemunerationExport;
use App\Models\Office;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\ProvisionalSummaryExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MusterRollSummaryExport;
use Illuminate\Support\Str;
use App\Exports\ProvisionalEmployeesExport;

class ExportController extends Controller
{
    //
    public function exportProvisionalSummary(Request $request)
    {
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('export-pe-summary'),403,'Access Denied');

        $export = new ProvisionalSummaryExport();
        return Excel::download($export, 'provisional_summary.xlsx');
    }

    public function exportMusterRollSummary(Request $request)
    {

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('export-mr-summary'),403,'Access Denied');

        $export = new MusterRollSummaryExport();
        return Excel::download($export, 'muster_roll_summary.xlsx');

    }

    public function exportProvisional(Request $request, Office $model){
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('export-pe'),403,'Access Denied');


        if ($user->hasRole('Manager')) {
            $model = $user->offices()->firstOrFail();
        }

        return Excel::download(
            new ProvisionalEmployeesExport($model),
            Str::slug($model->name, '_') . '_provisional_employees.xlsx'
        );
    }

    public function exportMusterRoll(Request $request, Office $model){
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('export-mr'),403,'Access Denied');


        if ($user->hasRole('Manager')) {
            $model = $user->offices()->firstOrFail();
        }

        return Excel::download(
            new MusterRollEmployeesExport($model),
            Str::slug($model->name, '_') . '_muster_roll_employees.xlsx'
        );
    }

    public function exportAll(Request $request, Office $model){

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('export-all'),403,'Access Denied');

        if ($user->hasRole('Manager')) {
            $model = $user->offices()->firstOrFail();
        }
        return Excel::download(
            new AllEmployeesExport($model),
            Str::slug($model->name, '_') . '_all_employees.xlsx'
        );
    }

    public function exportRemunerationSummary(Request $request){

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('export-remuneration-summary'),403,'Access Denied');

        $year =  Carbon::now()->year;
        return Excel::download(new RemunerationExport($year), "Remuneration-{$year}.xlsx");

    }

}
