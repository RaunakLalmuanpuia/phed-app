<?php

namespace App\Exports;

use App\Models\Employee;
use App\Models\Office;
use App\Models\Scheme;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MusterRollSummaryExport implements FromView, WithStyles
{
    protected $skills;
    protected $offices;
    protected $schemes;
    protected $totals;

    public function __construct()
    {
        $skillOrder = ['Skilled-I', 'Skilled-II', 'Semi-Skilled', 'Unskilled'];

        // Step 1: Get all unique skills
        $this->skills = Employee::where('employment_type', 'MR')
            ->select('skill_at_present')
            ->distinct()
            ->pluck('skill_at_present')
            ->toArray();

        usort($this->skills, function ($a, $b) use ($skillOrder) {
            return array_search($a, $skillOrder) <=> array_search($b, $skillOrder);
        });

        // Step 2: Office Data (only MR employees without scheme)
        $this->offices = Office::select('id', 'name')
            ->whereHas('employees', function ($q) {
                $q->where('employment_type', 'MR')->whereNull('scheme_id');
            })
            ->with(['employees' => function ($q) {
                $q->where('employment_type', 'MR')->whereNull('scheme_id')
                    ->select('id', 'office_id', 'skill_at_present');
            }])
            ->get()
            ->map(function ($office) {
                $row = ['name' => $office->name];
                $total = 0;
                foreach ($this->skills as $skill) {
                    $count = $office->employees->where('skill_at_present', $skill)->count();
                    $row[$skill] = $count;
                    $total += $count;
                }
                $row['total'] = $total;
                return $row;
            })
            ->toArray();

        // Step 3: Add Office Total (A)
        $officeTotalRow = ['name' => 'Total (A)'];
        $officeGrand = 0;
        foreach ($this->skills as $skill) {
            $sum = array_sum(array_column($this->offices, $skill));
            $officeTotalRow[$skill] = $sum;
            $officeGrand += $sum;
        }
        $officeTotalRow['total'] = $officeGrand;
        $this->offices[] = $officeTotalRow;

        // Step 4: Scheme Data (all MR employees with scheme)
        $this->schemes = Scheme::select('id', 'name')
            ->whereHas('employees', function ($q) {
                $q->where('employment_type', 'MR');
            })
            ->with(['employees' => function ($q) {
                $q->where('employment_type', 'MR')
                    ->select('id', 'scheme_id', 'skill_at_present');
            }])
            ->get()
            ->map(function ($scheme) {
                $row = ['name' => $scheme->name];
                $total = 0;
                foreach ($this->skills as $skill) {
                    $count = $scheme->employees->where('skill_at_present', $skill)->count();
                    $row[$skill] = $count;
                    $total += $count;
                }
                $row['total'] = $total;
                return $row;
            })
            ->toArray();

        // Step 5: Add Scheme Total (B)
        $schemeTotalRow = ['name' => 'Total (B)'];
        $schemeGrand = 0;
        foreach ($this->skills as $skill) {
            $sum = array_sum(array_column($this->schemes, $skill));
            $schemeTotalRow[$skill] = $sum;
            $schemeGrand += $sum;
        }
        $schemeTotalRow['total'] = $schemeGrand;
        $this->schemes[] = $schemeTotalRow;

        // Step 6: Grand Total (A+B)
        $grandTotalRow = ['name' => 'Grand Total (A+B)'];
        foreach ($this->skills as $skill) {
            $grandSum = $officeTotalRow[$skill] + $schemeTotalRow[$skill];
            $grandTotalRow[$skill] = $grandSum;
        }
        $grandTotalRow['total'] = $officeTotalRow['total'] + $schemeTotalRow['total'];

        $this->totals = $grandTotalRow;
    }

    public function view(): View
    {
        return view('exports.muster_roll_summary', [
            'skills' => $this->skills,
            'offices' => $this->offices,
            'schemes' => $this->schemes,
            'grandTotal' => $this->totals,
        ]);
    }
    public function styles(Worksheet $sheet)
    {
        // Last column index:
        // 1 => Sl. No, 2 => Office/Scheme, next N => skills, then Total => N + 3
        $lastColIndex = count($this->skills) + 3;
        $lastColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColIndex);

        $highestRow = $sheet->getHighestRow();

        // Apply thin border to the whole table INCLUDING the last "Total" column
        $sheet->getStyle("A1:{$lastColumnLetter}{$highestRow}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);

        // Make header row bold
        $sheet->getStyle("A1:{$lastColumnLetter}1")->getFont()->setBold(true);

        // Bold any row where column B contains the word "Total" (handles RichText too)
        for ($row = 2; $row <= $highestRow; $row++) {
            $cell = $sheet->getCell("B{$row}");
            $value = $cell->getValue();

            if ($value instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
                $value = $value->getPlainText();
            }

            if ($value !== null && stripos((string) $value, 'Total') !== false) {
                $sheet->getStyle("A{$row}:{$lastColumnLetter}{$row}")->getFont()->setBold(true);
            }
        }
    }

}
