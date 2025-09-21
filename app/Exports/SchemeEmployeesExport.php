<?php

namespace App\Exports;

use App\Models\Scheme;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SchemeEmployeesExport implements FromView
{
    protected $scheme;
    protected $user;

    public function __construct(Scheme $scheme, $user)
    {
        $this->scheme = $scheme;
        $this->user   = $user;
    }

    public function view(): View
    {
        $employeesQuery = $this->scheme->employees()
            ->where('employment_type', 'MR')
            ->with('office')
            ->orderBy('name');

        // ✅ If Manager, filter employees by Manager’s office
        if ($this->user->hasRole('Manager')) {
            $managerOffice = $this->user->offices()->first(); // assuming relation: user->offices()
            if ($managerOffice) {
                $employeesQuery->where('office_id', $managerOffice->id);
            }
        }

        $employees = $employeesQuery->get();

        return view('exports.scheme_employees', [
            'scheme'    => $this->scheme,
            'employees' => $employees,
        ]);
    }
}
