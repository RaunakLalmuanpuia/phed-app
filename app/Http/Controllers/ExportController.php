<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ProvisionalSummaryExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MusterRollSummaryExport;

class ExportController extends Controller
{
    //
    public function exportProvisionalSummary()
    {
        $export = new ProvisionalSummaryExport();
        return Excel::download($export, 'provisional_summary.xlsx');
    }

    public function exportMusterRollSummary(){

        $export = new MusterRollSummaryExport();
        return Excel::download($export, 'muster_roll_summary.xlsx');

    }



}
