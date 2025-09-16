<?php

namespace App\Exports;

use App\Models\Scheme;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SchemeEmployeesExport implements FromView
{
    protected $scheme;

    public function __construct(Scheme $scheme)
    {
        $this->scheme = $scheme;
    }

    public function view(): View
    {
        $employees = $this->scheme->employees()
            ->where('employment_type', 'MR')
            ->with('office')
            ->orderBy('name')
            ->get();

        return view('exports.scheme_employees', [
            'scheme'    => $this->scheme,
            'employees' => $employees,
        ]);
    }
}
