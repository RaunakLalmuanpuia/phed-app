<?php

namespace App\Exports;

use App\Models\DocumentType;
use App\Models\Employee;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MasterEmployeesExport implements FromView
{

    public function view(): View
    {
        $employees = Employee::with(['office','deletionDetail','documents.type'])->orderBy('name', 'asc')->get();
        $documentTypes = DocumentType::orderBy('name')->get();

        return view('exports.master_employees', [
            'employees'  => $employees,
            'documentTypes' => $documentTypes,
        ]);
    }
}
