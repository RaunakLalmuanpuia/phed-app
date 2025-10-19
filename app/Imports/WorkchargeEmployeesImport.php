<?php

namespace App\Imports;

use App\Models\Employee;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class WorkchargeEmployeesImport implements ToModel, WithStartRow
{
    protected $office_id;
    protected $employment_type = 'WC';

    protected $columns = [
        'sl'                 => 0,
        'name'               => 1,
        'designation'        => 2,
        'date_of_birth'      => 3,
        'date_of_retirement' => 4,
        'educational_qln'    => 5,
        'technical_qln'      => 6,
        'date_of_engagement' => 7,
        'name_of_workplace'  => 8,
    ];

    public function __construct($office_id)
    {
        $this->office_id = $office_id;
    }

    public function startRow(): int
    {
        return 2; // Skip header row
    }

    public function model(array $row)
    {
        // Skip invalid rows (no name)
        if (empty($row[$this->columns['name']])) {
            return null;
        }

        // Generate a unique mobile number for each imported employee
        $randomMobile = $this->generateUniqueMobile();

        $employee = Employee::updateOrCreate(
            [
                'mobile' => $randomMobile, // used as unique identifier
                'office_id' => $this->office_id,
            ],
            [
                'office_id'          => $this->office_id,
                'employee_code'      => $this->generateEmployeeCode(),
                'name'               => $row[$this->columns['name']],
                'designation'        => $row[$this->columns['designation']] ?? null,
                'date_of_birth'      => $this->transformDate($row[$this->columns['date_of_birth']] ?? null),
                'date_of_retirement' => $this->transformDate($row[$this->columns['date_of_retirement']] ?? null),
                'educational_qln'    => $row[$this->columns['educational_qln']] ?? null,
                'technical_qln'      => $row[$this->columns['technical_qln']] ?? null,
                'date_of_engagement' => $this->transformDate($row[$this->columns['date_of_engagement']] ?? null),
                'name_of_workplace'  => $row[$this->columns['name_of_workplace']] ?? null,
                'employment_type'    => $this->employment_type,
                'mobile'             => $randomMobile,
            ]
        );

        return $employee;
    }

    protected function generateEmployeeCode()
    {
        do {
            $code = 'PHED-WC-' . rand(100000, 999999);
        } while (Employee::where('employee_code', $code)->exists());

        return $code;
    }

    protected function generateUniqueMobile()
    {
        do {
            // Generate RN-prefixed 10-digit random number (e.g. RN1234567890)
            $mobile = 'RN' . rand(1000000000, 9999999999);
        } while (Employee::where('mobile', $mobile)->exists());

        return $mobile;
    }

    protected function transformDate($value)
    {
        if (empty($value)) return null;

        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            return Carbon::createFromFormat('j/n/Y', trim($value))->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
    }
}
