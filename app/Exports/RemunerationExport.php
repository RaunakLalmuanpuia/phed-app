<?php

namespace App\Exports;

use App\Models\Office;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RemunerationExport implements FromView, WithStyles
{
    protected $year;

    public function __construct($year)
    {
        $this->year = Carbon::now()->year;
    }

    public function view(): View
    {
        $offices = Office::with(['employees.remunerationDetail'])->get();

        $rows = $offices->map(function ($office, $index) {
            $oneMonth = $office->employees->sum(fn($emp) => optional($emp->remunerationDetail)->round_total ?? 0);

            return [
                'sl_no'        => $index + 1,
                'name'         => $office->name,
                'employee_cnt' => $office->employees->count(),
                'one_month'    => $oneMonth,
                'three_months' => $oneMonth * 3,
                'six_months'   => $oneMonth * 6,
                'one_year'     => $oneMonth * 12,
            ];
        });

        $totals = [
            'employee_cnt' => $rows->sum('employee_cnt'),
            'one_month'    => $rows->sum('one_month'),
            'three_months' => $rows->sum('three_months'),
            'six_months'   => $rows->sum('six_months'),
            'one_year'     => $rows->sum('one_year'),
        ];

        // Build month header: JANUARY - ... DECEMBER, 2025
        $monthsHeader = collect(range(1, 12))
            ->map(fn($m) => strtoupper(now()->month($m)->format('F')))
            ->implode(' - ');

        return view('exports.remuneration', [
            'rows'        => $rows,
            'totals'      => $totals,
            'monthsHeader'=> $monthsHeader,
            'year'        => $this->year
        ]);
    }
    public function styles(Worksheet $sheet)
    {
        // Apply underline and bold to the first row (row 1)
        $sheet->getStyle('A1:G1')->getFont()->setBold(true)->setUnderline(\PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE);
        // Optional: increase row height

    }
}
