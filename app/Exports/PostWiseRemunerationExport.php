<?php

namespace App\Exports;

use App\Models\Employee;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PostWiseRemunerationExport implements FromView
{
    public function view(): View
    {
        // Fetch all PE employees with remuneration
        $employees = Employee::where('employment_type', 'PE')
            ->with('remunerationDetail')
            ->get();

        // Group by designation
        $grouped = $employees->groupBy('designation');

        // Build rows with SL NO
        $sl = 1;


        $rows = $grouped->map(function ($group, $designation) use (&$sl) {

            $employeeCount = $group->count();

            $oneMonthWages = $group->sum(function ($emp) {
                return optional($emp->remunerationDetail)->round_total ?? 0;
            });

            return [
                'sl_no'        => $sl++,
                'designation'    => $designation,
                'employee_count' => $employeeCount,
                'one_month'      => $oneMonthWages,
                'three_months'   => $oneMonthWages * 3,
                'six_months'     => $oneMonthWages * 6,
                'one_year'       => $oneMonthWages * 12,
            ];
        });

        $totals = [
            'designation'    => 'TOTAL',
            'employee_count' => $rows->sum('employee_count'),
            'one_month'      => $rows->sum('one_month'),
            'three_months'   => $rows->sum('three_months'),
            'six_months'     => $rows->sum('six_months'),
            'one_year'       => $rows->sum('one_year'),
        ];

        return view('exports.post-wise-remuneration', [
            'rows'   => $rows,
            'totals' => $totals
        ]);
    }

    public function title(): string
    {
        return 'ABSTRACT';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->getFont()->setBold(true)->setUnderline(\PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE);
    }
}
