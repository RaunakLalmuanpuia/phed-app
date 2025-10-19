<?php

namespace App\Exports;

use App\Models\Employee;
use App\Models\Office;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class WorkChargeSummaryExport implements FromView, WithStyles
{
    protected $designationGroups;
    protected $designations;
    protected $offices;
    protected $rows;

    public function __construct()
    {
        // ✅ Fixed Group Mapping (same as Vue)
        $this->designationGroups = [
            'Junior Engineer'          => 'B',
            'Overseer-II'              => 'B',
            'Telephone Operator G-II'  => 'B',
            'Trained/SA'               => 'C',
            'Plumber'                  => 'C',
            'Asst.Plumber'             => 'C',
            'Pump Operator'            => 'C',
            'Asst. Plant Operator'     => 'C',
            'Asst. Operator'           => 'C',
            'Asst. Mechanic'           => 'C',
            'Mechanic G-III'           => 'C',
            'Asst. Driller'            => 'C',
            'Hand Pump Mechanic'       => 'C',
            'Asst.Hand Pump Mechanic'  => 'C',
            'Driver'                   => 'C',
            'Telephone Operator G-III' => 'C',
            'Asst. Telephone Operator' => 'C',
            'Store Keeper'             => 'C',
            'Electrician G-III'        => 'C',
            'Carpenter'                => 'C',
            'Khalasi'                  => 'D',
            'Unskilled Work Assistant' => 'D',
            'Water Chowkidar'          => 'D',
            'Chowkidar'                => 'D',
            'Conductor'                => 'D',
            'Handyman'                 => 'D',
        ];

        // ✅ Offices with WC employees
        $this->offices = Office::whereHas('employees', fn($q) =>
        $q->where('employment_type', 'WC')
        )
            ->with(['employees' => fn($q) =>
            $q->where('employment_type', 'WC')->select('id','office_id','designation')
            ])
            ->select('id','name')
            ->get();

        // ✅ Get *other* WC designations
        $existingDesignations = Employee::where('employment_type','WC')
            ->select('designation')->distinct()->pluck('designation')->toArray();

        $unknownDesignations = array_diff($existingDesignations, array_keys($this->designationGroups));

        // ✅ Final designation order: fixed groups + unknown
        $this->designations = array_merge(array_keys($this->designationGroups), $unknownDesignations);

        // ✅ Build rows (same as Vue)
        $this->rows = collect($this->designations)->map(function ($designation, $i) {
            $group = $this->designationGroups[$designation] ?? 'N/A';
            $row = [
                'sl' => $i + 1,
                'name_of_post' => $designation,
                'group' => $group
            ];

            $total = 0;
            foreach ($this->offices as $office) {
                $count = $office->employees->where('designation', $designation)->count();
                $row[$office->name] = $count;
                $total += $count;
            }

            $row['total'] = $total;
            return $row;
        });

        // ✅ Grand Total row
        $grand = ['sl' => '', 'name_of_post' => 'Grand Total', 'group' => ''];
        $grandTotal = 0;

        foreach ($this->offices as $office) {
            $sum = $this->rows->sum($office->name);
            $grand[$office->name] = $sum;
            $grandTotal += $sum;
        }
        $grand['total'] = $grandTotal;

        $this->rows->push($grand);
    }

    public function view(): View
    {
        return view('exports.workcharge_summary', [
            'offices' => $this->offices->pluck('name')->toArray(),
            'rows' => $this->rows
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $rowCount = count($this->rows) + 1; // +1 for header
        $colCount = count($this->offices) + 4; // SL, Post, Group, Offices..., Total
        $lastColumn = Coordinate::stringFromColumnIndex($colCount);

        // ✅ Border for all
        $sheet->getStyle("A1:{$lastColumn}{$rowCount}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // ✅ Bold totals
        $sheet->getStyle("A{$rowCount}:{$lastColumn}{$rowCount}")
            ->getFont()->setBold(true);
    }
}
