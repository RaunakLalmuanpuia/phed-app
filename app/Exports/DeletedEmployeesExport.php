<?php

namespace App\Exports;

use App\Models\Employee;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DeletedEmployeesExport implements FromView
{

    public function view(): View
    {
        $employees = Employee::where('employment_type', 'Deleted')->with(['office','deletionDetail'])->get();


        return view('exports.deleted_employees', [
            'employees'  => $employees,
        ]);
    }
}
