<?php

namespace App\Exports;

use App\Models\Office;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class OfficeRemunerationSheet implements FromView, WithTitle
{
    protected $office;

    public function __construct(Office $office)
    {
        $this->office = $office;
    }

    public function view(): View
    {
        // filter only PE employees
        $employeesQuery = $this->office->employees()->where('employment_type', 'PE')->with('remunerationDetail')->get();

        $employees = $employeesQuery->map(function ($emp, $index) {
            $rem = optional($emp->remunerationDetail);

            return [
                'sl_no'            => $index + 1,
                'name'             => $emp->name,
                'designation'      => $emp->designation,
                'remuneration'     => $rem->remuneration ?? 0,
                'medical_allowance'=> $rem->medical_amount ?? 0,
                'total'            => $rem->total ?? 0,
                'monthly_rem'      => $rem->round_total ?? 0,
                'next_increment'   => $rem->next_increment_date ? \Carbon\Carbon::parse($rem->next_increment_date)->format('d.m.Y') : '',
            ];
        });



        return view('exports.office_remuneration', [
            'office'       => $this->office,
            'employees'    => $employees,

        ]);
    }


    public function title(): string
    {
        return substr($this->office->name, 0, 31);
    }
}
