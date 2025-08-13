<?php

namespace App\Exports;

use App\Models\Employee;
use App\Models\Office;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProvisionalSummaryExport implements FromView, WithStyles
{
    protected $designations;
    protected $offices;

    public function __construct()
    {
        $this->designations = Employee::where('employment_type', 'PE')
            ->select('designation')
            ->distinct()
            ->orderBy('designation')
            ->pluck('designation')
            ->toArray();

        $this->offices = Office::select('id', 'name')
            ->whereHas('employees', function ($q) {
                $q->where('employment_type', 'PE');
            })
            ->with(['employees' => function ($q) {
                $q->where('employment_type', 'PE')
                    ->select('id', 'office_id', 'designation');
            }])
            ->get()
            ->map(function ($office) {
                $row = [
                    'name' => $office->name,
                ];

                $total = 0;

                foreach ($this->designations as $designation) {
                    $count = $office->employees
                        ->where('designation', $designation)
                        ->count();

                    $row[$designation] = $count;
                    $total += $count;
                }

                $row['total'] = $total;
                return $row;
            })
            ->toArray();

        // Grand Total Row
        $grandTotalRow = ['name' => 'Grand Total'];
        $grandTotal = 0;

        foreach ($this->designations as $designation) {
            $sum = array_sum(array_column($this->offices, $designation));
            $grandTotalRow[$designation] = $sum;
            $grandTotal += $sum;
        }

        $grandTotalRow['total'] = $grandTotal;
        $this->offices[] = $grandTotalRow;
    }

    public function view(): View
    {
        return view('exports.provisional_summary', [
            'designations' => $this->designations,
            'offices' => $this->offices
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $rowCount = count($this->offices) + 1; // +1 for header
        $grandTotalRowIndex = $rowCount; // Last row is grand total

        // Bold Total column (last column)
        $lastColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($this->designations) + 2);
        $sheet->getStyle($lastColumnLetter . '2:' . $lastColumnLetter . $rowCount)->getFont()->setBold(true);

        // Black border for all cells
        $sheet->getStyle('A1:' . $lastColumnLetter . $rowCount)
            ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
            ->getColor()->setARGB('000000');

        // Bold the grand total row
        $sheet->getStyle('A' . $grandTotalRowIndex . ':' . $lastColumnLetter . $grandTotalRowIndex)
            ->getFont()->setBold(true);
    }

}
