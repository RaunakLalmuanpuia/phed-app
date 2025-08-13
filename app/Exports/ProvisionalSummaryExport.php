<?php

namespace App\Exports;

use App\Models\Employee;
use App\Models\Office;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProvisionalSummaryExport implements FromView
{
    protected $designations;
    protected $offices;

    public function __construct()
    {
        // Step 1: Get all unique designations for PE employees
        $this->designations = Employee::where('employment_type', 'PE')
            ->select('designation')
            ->distinct()
            ->orderBy('designation')
            ->pluck('designation')
            ->toArray();

        // Step 2: Build summary data
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
            });
    }

    public function view(): View
    {
        return view('exports.provisional_summary', [
            'designations' => $this->designations,
            'offices' => $this->offices
        ]);
    }
}
