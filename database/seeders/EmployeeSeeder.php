<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Employee;
use App\Models\Office;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Offices
        $offices = collect([
            'Head Office',
            'Regional Office North',
            'Regional Office South',
            'Field Office East',
            'Field Office West'
        ])->map(fn($name) => Office::create([
            'name' => $name,
            'type' => 'Regional',
            'location' => fake()->city(),
        ]));

        // Seed Document Types
        $docTypes = collect([
            'Aadhar Card',
            'PAN Card',
            'Passport',
            'Driving License',
            'Voter ID'
        ])->map(fn($type) => DocumentType::create([
            'name' => $type,
            'description' => "$type document",
        ]));

        // Seed Employees (10 MR, 10 PE)
        $employmentTypes = array_merge(array_fill(0, 10, 'MR'), array_fill(0, 10, 'PE'));
        shuffle($employmentTypes);

        $employees = collect($employmentTypes)->map(function ($type, $i) use ($offices) {
            return Employee::create([
                'office_id' => $offices->random()->id,
                'employee_code' => 'EMP' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'name' => fake()->name(),
                'mobile' => fake()->unique()->numerify('9#########'),
                'email' => fake()->unique()->safeEmail(),
                'date_of_birth' => fake()->date('Y-m-d', '2000-01-01'),
                'parent_name' => fake()->name(),
                'employment_type' => $type,
                'educational_qln' => 'Graduate',
                'technical_qln' => 'Diploma in IT',
                'designation' => 'Staff',
                'name_of_workplace' => fake()->company(),
                'post_per_qualification' => 'Junior Assistant',
                'date_of_engagement' => fake()->date(),
                'skill_category' => 'Category A',
                'skill_at_present' => 'Skill',
            ]);
        });

        // Seed Documents for each employee
        $employees->each(function ($employee) use ($docTypes) {
            foreach ($docTypes->random(2) as $docType) {
                Document::create([
                    'document_type_id' => $docType->id,
                    'employee_id' => $employee->id,
                    'mime' => 'application/pdf',
                    'path' => 'uploads/documents/sample.pdf',
                    'name' => $docType->name . ' - ' . $employee->name,
                    'type' => 'identity',
                    'upload_date' => Carbon::now()->toDateString(),
                ]);
            }
        });
    }
}
