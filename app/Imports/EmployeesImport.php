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
            ['mobile' => $row['mobile']], // match by mobile
            [
                'office_id'            => $this->office_id,
                'employee_code'        =>  $this->generateEmployeeCode(), // generate unique code
                'name'                 => $row['name'],
                'date_of_birth'        => $this->transformDate($row['date_of_birth'] ?? null),
                'parent_name'          => $row['parent_name'],
                'employment_type'      => $this->employment_type,
                'educational_qln'      => $row['educational_qln'],
                'technical_qln'        => $row['technical_qln'],
                'designation'          => $row['designation'],
                'name_of_workplace'    => $row['name_of_workplace'],
                'post_per_qualification' => $row['post_per_qualification'],
                'date_of_engagement'   => $this->transformDate($row['date_of_engagement'] ?? null),
                'skill_category'       => $row['skill_category'],
                'skill_at_present'     => $row['skill_at_present'],
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
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            } else {
                return \Carbon\Carbon::parse($value)->format('Y-m-d');
            }
        } catch (\Exception $e) {
            return null; // or handle/log the error
        }
    }
}
