<?php

namespace App\Exports;

use App\Models\Employee;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProvisionalEmployeesExport implements FromView
{
    protected $office;

    public function __construct($office)
    {
        $this->office = $office;
    }

    public function view(): View
    {
        $employees = Employee::with('remunerationDetail')
            ->where('office_id', $this->office->id)
            ->where('employment_type', 'PE')
            ->get();

        return view('exports.provisional_employees', [
            'office'     => $this->office,
            'employees'  => $employees,
        ]);
    }
}
