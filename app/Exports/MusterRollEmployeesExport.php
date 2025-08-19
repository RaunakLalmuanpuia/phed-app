<?php

namespace App\Exports;

use App\Models\Employee;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MusterRollEmployeesExport implements FromView
{
    protected $office;

    public function __construct($office)
    {
        $this->office = $office;
    }

    public function view(): View
    {
        $employees = Employee::where('office_id', $this->office->id)
            ->where('employment_type', 'MR')
            ->get();

        return view('exports.muster_roll_employees', [
            'office'     => $this->office,
            'employees'  => $employees,
        ]);
    }
}
