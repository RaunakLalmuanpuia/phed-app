<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class EmployeesImport implements ToModel, WithStartRow
{
    protected $office_id;
    protected $employment_type;

    // Define column index mapping
    protected $columns = [
        'sl'                   => 0,
        'name'                 => 1,
        'parent_name'          => 2,
        'date_of_birth'        => 3,
        'mobile'               => 4,
        'workplace'            => 5,
        'designation'          => 6,
        'date_of_engagement'   => 7,
        'skill_category'       => 8,
        'present_skill'        => 9,
        'educational_qln'      => 10,
        'technical_qln'        => 11,
        'post_per_qln'         => 12,
        'remuneration'         => 13,
        'next_increment_date'  => 14,
        'address'  => 15,
        'pay_matrix'=>16,
        'engagement_card_no'=>17,

    ];

    public function __construct($office_id, $employment_type)
    {
        $this->office_id = $office_id;
        $this->employment_type = $employment_type;
    }

    public function startRow(): int
    {
        return 2; // skip header row
    }

    public function model(array $row)
    {
        $designationValue = $row[$this->columns['designation']] ?? null;
        $employee = Employee::updateOrCreate(
            ['mobile' => $row[$this->columns['mobile']] ?? null],
            [
                'office_id'              => $this->office_id,
                'employee_code'          => $this->generateEmployeeCode(),
                'name'                   => $row[$this->columns['name']] ?? null,
                'parent_name'            => $row[$this->columns['parent_name']] ?? null,
                'date_of_birth'          => $this->transformDate($row[$this->columns['date_of_birth']] ?? null),
                'address'                 => $row[$this->columns['address']] ?? null,
                'employment_type'        => $this->employment_type,
                'educational_qln'        => $row[$this->columns['educational_qln']] ?? null,
                'technical_qln'          => $row[$this->columns['technical_qln']] ?? null,

                'name_of_workplace'      => $row[$this->columns['workplace']] ?? null,
                'post_per_qualification' => $row[$this->columns['post_per_qln']] ?? null,
                'date_of_engagement'     => $this->transformDate($row[$this->columns['date_of_engagement']] ?? null),
                'skill_category'         => $row[$this->columns['skill_category']] ?? null,
                'skill_at_present'       => $row[$this->columns['present_skill']] ?? null,
                'engagement_card_no'       => $row[$this->columns['engagement_card_no']] ?? null,

                // conditional assignment
                'designation'   => $this->employment_type === 'PE' ? $designationValue : null,
                'post_assigned' => $this->employment_type === 'MR' ? $designationValue : null,

            ]
        );

        // ✅ If employment type is PE, create remuneration detail
        if ($this->employment_type === 'PE') {
            $remuneration = $row[$this->columns['remuneration']] ?? null;
            $nextIncrementDate = $this->transformDate($row[$this->columns['next_increment_date']] ?? null);
            $pay_matrix = $this->formatPayMatrix($row[$this->columns['pay_matrix']] ?? null);


            if (!empty($remuneration) && !empty($nextIncrementDate)) {
                $employee->remunerationDetail()->updateOrCreate(
                    [],
                    [
                        'remuneration'        => $remuneration,
                        'next_increment_date' => $nextIncrementDate,
                        'pay_matrix'         => $pay_matrix,
                    ]
                );
            }
        }

        return $employee;
    }


    protected function generateEmployeeCode()
    {
        do {
            $code = 'PHED-' . rand(100000, 999999);
        } while (Employee::where('employee_code', $code)->exists());

        return $code;
    }

    protected function transformDate($value)
    {
        try {
            if (empty($value)) return null;

            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function formatPayMatrix(?string $level): ?string
    {
        if (empty($level)) {
            return null;
        }

        // Normalize input (e.g. "Level-1" -> "1")
        $number = preg_replace('/[^0-9]/', '', $level);

        // Mapping table
        $mapping = [
            '1' => 'Level 1 (17,400 - 38,600)',
            '2' => 'Level 2 (19,900 - 44,400)',
            '3' => 'Level 3 (21,700 - 48,500)',
            '4' => 'Level 4 (25,500 - 56,800)',
            '5' => 'Level 5 (29,200 - 64,700)',
        ];

        return $mapping[$number] ?? $level; // fallback to original if not found
    }

}
