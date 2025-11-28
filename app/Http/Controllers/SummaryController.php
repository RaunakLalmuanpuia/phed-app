<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Office;
use App\Models\Scheme;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    //
    public function provisionalSummary(Request $request)
    {
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('view-pe-summary'),403,'Access Denied');

        $designations = Employee::where('employment_type', 'PE')
            ->select('designation')
            ->distinct()
            ->orderBy('designation')
            ->pluck('designation')
            ->toArray();

        $offices = Office::select('id', 'name')
            ->whereHas('employees', function ($q) {
                $q->where('employment_type', 'PE');
            })
            ->with(['employees' => function ($q) {
                $q->where('employment_type', 'PE')
                    ->select('id', 'office_id', 'designation');
            }])
            ->get()
            ->map(function ($office) use ($designations) {
                $row = [
                    'id' => $office->id,
                    'name' => $office->name,
                ];

                $total = 0; // Track total per office

                foreach ($designations as $designation) {
                    $count = $office->employees
                        ->where('designation', $designation)
                        ->count();

                    $row[$designation] = $count;
                    $total += $count;
                }

                $row['total'] = $total; // Add total column

                return $row;
            });

        return inertia('Backend/Summary/Provisional', [
            'designations' => $designations,
            'offices' => $offices
        ]);
    }

    public function workChargeSummary(Request $request)
    {
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('view-wc-summary'), 403, 'Access Denied');

        // Step 1: Designation → Group mapping (predefined)
        $designationGroups = [
            'Junior Engineer'          => 'B',
            'Overseer-II'              => 'B',
            'Telephone Operator G-II'  => 'B',
            'Trained/SA'               => 'C',
            'Plumber'                  => 'C',
            'Asst.Plumber'             => 'C',
            'Pump Operator'            => 'C',
            'Asst. Plant Operator'     => 'C',
            'Asst. Operator'           => 'C',
            'Asst. Mechanic'           => 'C',
            'Mechanic G-III'           => 'C',
            'Asst. Driller'            => 'C',
            'Hand Pump Mechanic'       => 'C',
            'Asst.Hand Pump Mechanic'  => 'C',
            'Driver'                   => 'C',
            'Telephone Operator G-III' => 'C',
            'Asst. Telephone Operator' => 'C',
            'Store Keeper'             => 'C',
            'Electrician G-III'        => 'C',
            'Carpenter'                => 'C',
            'Khalasi'                  => 'D',
            'Unskilled Work Assistant' => 'D',
            'Water Chowkidar'          => 'D',
            'Chowkidar'                => 'D',
            'Conductor'                => 'D',
            'Handyman'                 => 'D',
        ];

        // Step 2: Fetch all WC employees and offices
        $offices = Office::select('id', 'name')
            ->whereHas('employees', fn($q) => $q->where('employment_type', 'WC'))
            ->with(['employees' => fn($q) => $q->where('employment_type', 'WC')
                ->select('id', 'office_id', 'designation')])
            ->get();

        // Step 3: Find all unique WC designations from DB
        $dbDesignations = Employee::where('employment_type', 'WC')
            ->pluck('designation')
            ->unique()
            ->toArray();

        // Step 4: Merge (so that missing designations also show up)
        $allDesignations = array_unique(array_merge(array_keys($designationGroups), $dbDesignations));

        // Step 5: Build table rows
        $rows = collect($allDesignations)->map(function ($designation, $index) use ($offices, $designationGroups) {
            $row = [
                'sl' => $index + 1,
                'name_of_post' => $designation,
                'group' => $designationGroups[$designation] ?? '—', // default if not found
            ];

            $total = 0;
            foreach ($offices as $office) {
                $count = $office->employees
                    ->where('designation', $designation)
                    ->count();

                $row[$office->name] = $count;
                $total += $count;
            }

            $row['total'] = $total;
            return $row;
        })->values(); // reset index

        return inertia('Backend/Summary/WorkCharge', [
            'offices' => $offices->pluck('name')->toArray(),
            'rows' => $rows->toArray(),
        ]);
    }

    public function musterRollSummary(Request $request)
    {
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('view-mr-summary'), 403, 'Access Denied');

        $skillOrder = ['Skilled-I','Skilled-II','Semi-Skilled','Unskilled',];

        // Step 1: get all unique skills for MR employees in desired order
        $skills = Employee::where('employment_type', 'MR')
            ->select('skill_at_present')
            ->distinct()
            ->pluck('skill_at_present')
            ->toArray();

        usort($skills, function ($a, $b) use ($skillOrder) {
            return array_search($a, $skillOrder) <=> array_search($b, $skillOrder);
        });

        // Step 2: Offices
        $offices = Office::select('id', 'name')
            ->whereHas('employees', function ($q) {
                $q->where('employment_type', 'MR')->whereNull('scheme_id');
            })
            ->with(['employees' => function ($q) {
                $q->where('employment_type', 'MR')->whereNull('scheme_id')
                    ->select('id', 'office_id', 'skill_at_present');
            }])
            ->get()
            ->map(function ($office) use ($skills) {
                $row = [
                    'id' => $office->id,
                    'name' => $office->name,
                ];

                $total = 0;
                foreach ($skills as $skill) {
                    $count = $office->employees
                        ->where('skill_at_present', $skill)
                        ->count();

                    $row[$skill] = $count;
                    $total += $count;
                }

                $row['total'] = $total;
                return $row;
            });

        // Step 3: Schemes
        $schemes = Scheme::select('id', 'name')
            ->whereHas('employees', function ($q) {
                $q->where('employment_type', 'MR');
            })
            ->with(['employees' => function ($q) {
                $q->where('employment_type', 'MR')
                    ->select('id', 'scheme_id', 'skill_at_present');
            }])
            ->get()
            ->map(function ($scheme) use ($skills) {
                $row = [
                    'id' => $scheme->id,
                    'name' => $scheme->name,
                ];

                $total = 0;
                foreach ($skills as $skill) {
                    $count = $scheme->employees
                        ->where('skill_at_present', $skill)
                        ->count();

                    $row[$skill] = $count;
                    $total += $count;
                }

                $row['total'] = $total;
                return $row;
            });

        return inertia('Backend/Summary/MusterRoll', [
            'skills' => $skills,
            'offices' => $offices,
            'schemes' => $schemes
        ]);
    }


    public function officeMusterRollSummary(Request $request)
    {
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('view-mr-summary'), 403, 'Access Denied');

        // Get offices assigned to authenticated user
        $userOfficeIds = $user->offices()->pluck('office_id')->toArray();

        $skillOrder = ['Skilled-I','Skilled-II','Semi-Skilled','Unskilled'];

        // Step 1: get unique skills
        $skills = Employee::where('employment_type', 'MR')
            ->select('skill_at_present')
            ->distinct()
            ->pluck('skill_at_present')
            ->toArray();

        usort($skills, function ($a, $b) use ($skillOrder) {
            return array_search($a, $skillOrder) <=> array_search($b, $skillOrder);
        });

        // Step 2: Offices (filtered by user office)
        $offices = Office::select('id', 'name')
            ->whereIn('id', $userOfficeIds) // <<--- IMPORTANT FILTER
            ->whereHas('employees', function ($q) {
                $q->where('employment_type', 'MR')->whereNull('scheme_id');
            })
            ->with(['employees' => function ($q) {
                $q->where('employment_type', 'MR')->whereNull('scheme_id')
                    ->select('id', 'office_id', 'skill_at_present');
            }])
            ->get()
            ->map(function ($office) use ($skills) {
                $row = [
                    'id' => $office->id,
                    'name' => $office->name,
                ];

                $total = 0;
                foreach ($skills as $skill) {
                    $count = $office->employees
                        ->where('skill_at_present', $skill)
                        ->count();

                    $row[$skill] = $count;
                    $total += $count;
                }

                $row['total'] = $total;
                return $row;
            });

        // Step 3: Schemes (employees only from user's offices)
        $schemes = Scheme::select('id', 'name')
            ->whereHas('employees', function ($q) use ($userOfficeIds) {
                $q->where('employment_type', 'MR')
                    ->whereIn('office_id', $userOfficeIds); // <<--- FILTER APPLIED
            })
            ->with(['employees' => function ($q) use ($userOfficeIds) {
                $q->where('employment_type', 'MR')
                    ->whereIn('office_id', $userOfficeIds)
                    ->select('id', 'scheme_id', 'office_id', 'skill_at_present');
            }])
            ->get()
            ->map(function ($scheme) use ($skills) {
                $row = [
                    'id' => $scheme->id,
                    'name' => $scheme->name,
                ];

                $total = 0;
                foreach ($skills as $skill) {
                    $count = $scheme->employees
                        ->where('skill_at_present', $skill)
                        ->count();

                    $row[$skill] = $count;
                    $total += $count;
                }

                $row['total'] = $total;
                return $row;
            });
        $officeName = Office::whereIn('id', $userOfficeIds)->pluck('name')->first();
        return inertia('Backend/Summary/Office/MusterRoll', [
            'skills' => $skills,
            'offices' => $offices,
            'schemes' => $schemes,
            'officeName' => $officeName
        ]);
    }
    public function officeProvisionalSummary(Request $request)
    {
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('view-pe-summary'), 403, 'Access Denied');

        $officeIds =$user->offices()->pluck('office_id')->toArray();


        // Get all designations for this office
        $designations = Employee::where('employment_type', 'PE')
            ->whereIn('office_id', $officeIds)
            ->select('designation')
            ->distinct()
            ->orderBy('designation')
            ->pluck('designation')
            ->toArray();

        // Convert designation → count row
        $rows = collect($designations)->map(function ($designation) use ($officeIds) {
            $count = Employee::where('employment_type', 'PE')
                ->whereIn('office_id', $officeIds)
                ->where('designation', $designation)
                ->count();

            return [
                'designation' => $designation,
                'count'       => $count,
                'total'       => $count,
            ];
        });

//        dd($rows);

        $officeName = Office::whereIn('id', $officeIds)->pluck('name')->first();

        return inertia('Backend/Summary/Office/Provisional', [
            'rows'       => $rows->values()->toArray(),
            'officeName' => $officeName,
        ]);
    }


    public function officeWorkChargeSummary(Request $request)
    {
        $user = $request->user();
        abort_if(!$user->hasPermissionTo('view-wc-summary'), 403, 'Access Denied');

        // Get auth user office ID
        $officeId =  $user->offices()->pluck('office_id')->toArray();

        // Step 1: Group mapping
        $designationGroups = [
            'Junior Engineer' => 'B',
            'Overseer-II' => 'B',
            'Telephone Operator G-II' => 'B',
            'Trained/SA' => 'C',
            'Plumber' => 'C',
            'Asst.Plumber' => 'C',
            'Pump Operator' => 'C',
            'Asst. Plant Operator' => 'C',
            'Asst. Operator' => 'C',
            'Asst. Mechanic' => 'C',
            'Mechanic G-III' => 'C',
            'Asst. Driller' => 'C',
            'Hand Pump Mechanic' => 'C',
            'Asst.Hand Pump Mechanic' => 'C',
            'Driver' => 'C',
            'Telephone Operator G-III' => 'C',
            'Asst. Telephone Operator' => 'C',
            'Store Keeper' => 'C',
            'Electrician G-III' => 'C',
            'Carpenter' => 'C',
            'Khalasi' => 'D',
            'Unskilled Work Assistant' => 'D',
            'Water Chowkidar' => 'D',
            'Chowkidar' => 'D',
            'Conductor' => 'D',
            'Handyman' => 'D',
        ];

        // Step 2: Only employees of this office
        $employees = Employee::where('employment_type', 'WC')
            ->whereIn('office_id', $officeId)
            ->select('id', 'office_id', 'designation')
            ->get();

        // Step 3: get all unique designations of this office
        $designations = $employees->pluck('designation')->unique()->values();

        // Step 4: Build rows
        $rows = $designations->map(function ($designation, $index) use ($employees, $designationGroups) {

            $count = $employees->where('designation', $designation)->count();

            return [
                'sl' => $index + 1,
                'name_of_post' => $designation,
                'group' => $designationGroups[$designation] ?? '—',
                'count' => $count,
                'total' => $count, // same as count since only one office
            ];
        });

        // Office name for table heading
        $officeName = Office::whereIn('id', $officeId)->value('name');

        return inertia('Backend/Summary/Office/WorkCharge', [
            'officeName' => $officeName,
            'rows' => $rows->toArray(),
        ]);
    }




}
