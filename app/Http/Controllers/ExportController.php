<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ProvisionalSummaryExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MusterRollSummaryExport;

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



}
