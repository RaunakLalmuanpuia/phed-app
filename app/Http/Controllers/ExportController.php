<?php

namespace App\Http\Controllers;

use App\Exports\AllEmployeesExport;
use App\Exports\DeletedEmployeesExport;
use App\Exports\MasterEmployeesExport;
use App\Exports\MusterRollEmployeesExport;
use App\Exports\OfficeRemunerationSheet;
use App\Exports\RemunerationExport;
use App\Exports\SchemeEmployeesExport;
use App\Exports\WorkChargeEmployeesExport;
use App\Exports\WorkChargeSummaryExport;
use App\Models\Office;
use App\Models\Scheme;
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
    public function exportWorkChargeSummary(Request $request)
    {
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('export-wc-summary'),403,'Access Denied');

        $export = new WorkChargeSummaryExport();
        return Excel::download($export, 'work_charge_summary.xlsx');

    }
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

    public function exportWorkCharge(Request $request, Office $model){
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('export-wc'),403,'Access Denied');


        if ($user->hasRole('Manager')) {
            $model = $user->offices()->firstOrFail();
        }

        return Excel::download(
            new WorkChargeEmployeesExport($model),
            Str::slug($model->name, '_') . '_work_charge_employees.xlsx'
        );
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

    public function exportDeleted(Request $request){

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('export-deleted'),403,'Access Denied');

        $export = new DeletedEmployeesExport();
        return Excel::download($export, 'Deleted_Employees.xlsx');
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

    public function exportOfficeRemuneration(Request $request)
    {

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('export-remuneration-summary'),403,'Access Denied');

        $user = $request->user();

        // Pick the user’s first office (adjust if you allow multiple)
        $office = $user->offices()->first();

        if (!$office) {
            return back()->with('error', 'No office assigned to this user.');
        }

        // Generate a safe filename
        $currentMonthYear = now()->format('F_Y');
        $filename = 'remuneration-summary-' . $office->name . '-' . $currentMonthYear . '.xlsx';

        // Return Excel download
        return Excel::download(new OfficeRemunerationSheet($office), $filename);
    }


//    public function exportScheme(Request $request, Scheme $model)
//    {
//        $user = $request->user();
////        abort_if(!$user->hasPermissionTo('export-scheme'), 403, 'Access Denied');
//
//        return Excel::download(
//            new SchemeEmployeesExport($model),
//             'scheme_employees.xlsx'
//        );
//    }

    public function exportScheme(Request $request, Scheme $model)
    {
        $user = $request->user();
         abort_if(!$user->hasPermissionTo('export-scheme'), 403, 'Access Denied');

        return Excel::download(
            new SchemeEmployeesExport($model, $user),
            \Str::slug($model->name, '_') . '_scheme_employees.xlsx'
        );
    }

    public function exportMaster(Request $request){

        $user = $request->user();
        abort_if(!$user->hasPermissionTo('export-master'),403,'Access Denied');

        $export = new MasterEmployeesExport();
        return Excel::download($export, 'Master_Employees.xlsx');
    }



}
