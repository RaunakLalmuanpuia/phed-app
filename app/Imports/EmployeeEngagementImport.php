<?php

namespace App\Imports;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeeEngagementImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Match actual Excel headings (converted automatically)
        $employeeCode = $row['emp_code'] ?? null;
        $engagementDate = $row['date_of_initial_engagement'] ?? null;

        if (empty($employeeCode) || empty($engagementDate)) {
            return null;
        }

        // Transform date
        $date = $this->transformDate($engagementDate);

        if (!$date) {
            Log::warning("Invalid date for employee_code: {$employeeCode}");
            return null;
        }

        // Find employee
        $employee = Employee::where('employee_code', trim($employeeCode))->first();

        if ($employee) {
            $employee->update([
                'date_of_engagement' => $date,
            ]);
        } else {
            Log::warning("Employee not found for code: {$employeeCode}");
        }

        return null;
    }

    protected function transformDate($value)
    {
        try {
            if (empty($value)) return null;

            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            // Normalize separators (convert . and - to /)
            $normalized = str_replace(['.', '-'], '/', trim($value));

            // Handle DD/MM/YYYY or D/M/YYYY
            if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $normalized)) {
                return Carbon::createFromFormat('d/m/Y', $normalized)->format('Y-m-d');
            }

            return Carbon::parse($normalized)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning("Date transform failed for value: {$value}");
            return null;
        }
    }
}
