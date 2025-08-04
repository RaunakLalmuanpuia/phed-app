<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class EmployeesImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    protected $office_id;
    protected $employment_type;

    public function __construct($office_id, $employment_type)
    {
        $this->office_id = $office_id;
        $this->employment_type = $employment_type;
    }

    public function model(array $row)
    {
        return Employee::updateOrCreate(
            ['mobile' => $row['Contact No'] ?? null],
            [
                'office_id'              => $this->office_id,
                'employee_code'          => $this->generateEmployeeCode(),
                'name'                   => $row['Name'] ?? null,
                'date_of_birth'          => $this->transformDate($row['Date of Birth'] ?? null),
                'parent_name'            => $row['Fathers/Mothers Name'] ?? null,
                'employment_type'        => $this->employment_type,
                'educational_qln'        => $row['Educational Qln'] ?? null,
                'technical_qln'          => $row['Technical Qln'] ?? null,
                'designation'            => $row['Designation'] ?? null,
                'name_of_workplace'      => $row['Name of Office Workplace'] ?? null,
                'post_per_qualification' => $row['Qualification mil a post thlan'] ?? null,
                'date_of_engagement'     => $this->transformDate($row['Date of Initial Engagement'] ?? null),
                'skill_category'         => $row['Skilled Category'] ?? null,
                'skill_at_present'       => $row['Skilled at present'] ?? null,
            ]
        );
    }

    protected function generateEmployeeCode()
    {
        do {
            $code = 'PHED-' . rand(100000, 999999); // You can modify the format if needed
        } while (Employee::where('employee_code', $code)->exists());

        return $code;
    }
    protected function transformDate($value)
    {
        try {
            if (empty($value)) {
                return null;
            }

            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null; // optionally log the error
        }
    }
}
