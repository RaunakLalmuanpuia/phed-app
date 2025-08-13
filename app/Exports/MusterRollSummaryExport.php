<?php

namespace App\Exports;

use App\Models\Employee;
use App\Models\Office;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MusterRollSummaryExport implements FromView
{
    protected $skills;
    protected $offices;

    public function __construct()
    {
        $skillOrder = ['Unskilled', 'Semi-Skilled', 'Skilled-I', 'Skilled-II'];

        // Step 1: Get all unique skills for MR employees
        $this->skills = Employee::where('employment_type', 'MR')
            ->select('skill_at_present')
            ->distinct()
            ->pluck('skill_at_present')
            ->toArray();

        // Step 2: Order according to desired sequence
        usort($this->skills, function ($a, $b) use ($skillOrder) {
            return array_search($a, $skillOrder) <=> array_search($b, $skillOrder);
        });

        // Step 3: Prepare office data
        $this->offices = Office::select('id', 'name')
            ->whereHas('employees', function ($q) {
                $q->where('employment_type', 'MR');
            })
            ->with(['employees' => function ($q) {
                $q->where('employment_type', 'MR')
                    ->select('id', 'office_id', 'skill_at_present');
            }])
            ->get()
            ->map(function ($office) {
                $row = ['name' => $office->name];
                $total = 0;

                foreach ($this->skills as $skill) {
                    $count = $office->employees
                        ->where('skill_at_present', $skill)
                        ->count();

                    $row[$skill] = $count;
                    $total += $count;
                }

                $row['total'] = $total;
                return $row;
            });
    }

    public function view(): View
    {
        return view('exports.muster_roll_summary', [
            'skills' => $this->skills,
            'offices' => $this->offices
        ]);
    }
}
