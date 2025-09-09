<?php

namespace App\Exports;

use App\Models\Office;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RemunerationExport implements WithMultipleSheets
{
    protected $year;

    public function __construct($year)
    {
        $this->year = $year;
    }

    public function sheets(): array
    {
        $sheets = [];

        // 1. Add the summary sheet
        $sheets[] = new RemunerationSummarySheet($this->year);

        // 2. Add a sheet per office
        $offices = Office::with(['employees.remunerationDetail'])->get();

        foreach ($offices as $office) {
            $sheets[] = new OfficeRemunerationSheet($office);
        }

        return $sheets;
    }
}
