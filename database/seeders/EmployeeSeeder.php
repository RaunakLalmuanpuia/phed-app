<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Employee;
use App\Models\Office;
use App\Models\RemunerationDetail;
use App\Models\Transfer;
use Carbon\Carbon;
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
            'Aadhar',
            'EPIC',
            'Birth Certificate',
            'Educational Certificate',
            'Technical Certificate'
        ])->map(fn($type) => DocumentType::create([
            'name' => $type,
            'description' => "$type document",
        ]));

        // Predefined Designations
        $designations = [
            'Junior Assistant',
            'Senior Assistant',
            'Supervisor',
            'Manager',
            'Clerk',
            'Field Officer',
            'Technical Assistant',
            'Engineer',
            'Accountant',
            'Data Entry Operator'
        ];

        // Predefined Skills for MR employees
        $mrSkills = ['Unskilled', 'Semi-Skilled', 'Skilled-I', 'Skilled-II'];

        // Seed Employees (20 MR, 20 PE)
        $employmentTypes = array_merge(array_fill(0, 20, 'MR'), array_fill(0, 20, 'PE'));
        shuffle($employmentTypes);

        // Predefined Educational Qualifications
        $educationalQualifications = [
            'U/M',
            'HSLC',
            'HSSLC',
            'Graduate & Level',
            'Master Degree & Level',
        ];

        // Predefined Technical Qualifications
        $technicalQualifications = [
            'Diploma in IT',
            'Certificate in Electricals',
            'B.Tech in Civil Engineering',
            'B.Sc in Computer Science',
            'ITI Welder',
            'None'
        ];

        $employees = collect($employmentTypes)->map(function ($type, $i) use ($offices, $designations, $mrSkills, $educationalQualifications, $technicalQualifications) {
            $designationValue = collect($designations)->random();

            return Employee::create([
                'office_id'              => $offices->random()->id,
                'employee_code'          => 'EMP' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'name'                   => fake()->name(),
                'mobile'                 => fake()->unique()->numerify('9#########'),
                'email'                  => fake()->unique()->safeEmail(),
                'address'                => fake()->address(),
                'date_of_birth'          => fake()->date('Y-m-d', '2000-01-01'),
                'parent_name'            => fake()->name(),
                'employment_type'        => $type,
                'educational_qln'        => collect($educationalQualifications)->random(),
                'technical_qln'          => collect($technicalQualifications)->random(),
                'designation'            => $type === 'PE' ? $designationValue : null,
                'post_assigned'          => $type === 'MR' ? $designationValue : null,
                'name_of_workplace'      => fake()->company(),
                'post_per_qualification' => collect($designations)->random(),
                'date_of_engagement'     => fake()->date(),
                'skill_category'         => 'Category A',
                'skill_at_present'       => $type === 'MR' ? collect($mrSkills)->random() : null,
            ]);
        });

        // Add remuneration for PE employees
        $employees->each(function ($employee) {
            if ($employee->employment_type === 'PE') {
                RemunerationDetail::create([
                    'employee_id'        => $employee->id,
                    'remuneration'       => fake()->numberBetween(20000, 50000),
                    'next_increment_date'=> now()->addYear()->toDateString(),
                ]);
            }
        });


//        // Seed Documents for each employee
//        $employees->each(function ($employee) use ($docTypes) {
//            foreach ($docTypes->random(2) as $docType) {
//                Document::create([
//                    'document_type_id' => $docType->id,
//                    'employee_id' => $employee->id,
//                    'mime' => 'application/pdf',
//                    'path' => 'uploads/documents/sample.pdf',
//                    'name' => $docType->name . ' - ' . $employee->name,
//                    'type' => 'identity',
//                    'upload_date' => Carbon::now()->toDateString(),
//                ]);
//            }
//        });


        // Seed Transfers for some employees (with history)
        $employees->random(10)->each(function ($employee) use ($offices) {
            $availableOffices = $offices->pluck('id')->toArray();
            $currentOfficeId = $employee->office_id;

            // Create 1 to 3 transfers
            $transferCount = rand(1, 3);
            $transferDate = Carbon::now()->subYears(3); // Start transfers 3 years ago

            for ($i = 0; $i < $transferCount; $i++) {
                $newOfficeId = collect($availableOffices)
                    ->reject(fn($id) => $id === $currentOfficeId)
                    ->random();

                Transfer::create([
                    'employee_id'   => $employee->id,
                    'old_office_id' => $currentOfficeId,
                    'new_office_id' => $newOfficeId,
                    'transfer_date' => $transferDate->copy()->addMonths($i * rand(6, 12))->toDateString(),
                ]);

                $currentOfficeId = $newOfficeId;
            }

            // Update employee's current office to last transferred office
            $employee->update(['office_id' => $currentOfficeId]);
        });
    }
}
