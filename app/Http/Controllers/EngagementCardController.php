<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EngagementCard;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use ZipArchive;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
class EngagementCardController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'employee_id'   => 'required|exists:employees,id',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'phed_file_no'  => 'required|string',
            'letter_date'   => 'required|date',
            'approval_dpar' => 'required|string',
            'approval_fin'  => 'required|string',
        ]);

        $employee = Employee::with(['office','remunerationDetail'])
            ->findOrFail($request->employee_id);

        $fiscalYear = $this->getFiscalYear($request->start_date, $request->end_date);

        // 🆕 Engagement card number logic
        if ($employee->engagement_card_no) {
            $card_no = $employee->engagement_card_no;

        } else {
            // Find prefix from other employees in same office
            $existingPrefix = Employee::where('office_id', $employee->office_id)
                ->whereNotNull('engagement_card_no')
                ->pluck('engagement_card_no')
                ->map(function ($card) {
                    return preg_match('/^([A-Z]+)-\d+$/', $card, $m) ? $m[1] : null;
                })
                ->filter()
                ->first();

            $officePrefix = $existingPrefix ?? strtoupper(substr(optional($employee->office)->name ?? 'OFF', 0, 3));

            // Find highest suffix for this office prefix
            $lastSuffix = Employee::where('office_id', $employee->office_id)
                ->whereNotNull('engagement_card_no')
                ->pluck('engagement_card_no')
                ->map(function ($card) use ($officePrefix) {
                    return preg_match('/^' . $officePrefix . '-(\d+)$/', $card, $m) ? (int)$m[1] : 0;
                })
                ->max();

            $nextSuffix = $lastSuffix ? $lastSuffix + 1 : 1;
            $card_no = $officePrefix . '-' . $nextSuffix;

            // Save to employee
            $employee->update(['engagement_card_no' => $card_no]);
        }

        // Prepare HTML
        $htmlContent = view('templates.engagement-card', [
            'name'           => $employee->name,
            'dob'            => Carbon::parse( $employee->date_of_birth)->format('d-m-Y'),
            'parent_name'    => $employee->parent_name,
            'address'        => $employee->address,
            'post'           => $employee->designation,
            'pay_matrix'     => $employee->remunerationDetail->pay_matrix ?? 'N/A',
            'remuneration'   => number_format($employee->remunerationDetail->round_total ?? 0, 0, '.', ','),
            'start_date'     => Carbon::parse($request->start_date)->format('d-m-Y'),
            'end_date'       => Carbon::parse($request->end_date)->format('d-m-Y'),
            'card_no'        => $card_no,
            'date'           => Carbon::parse($request->letter_date)->format('d.m.Y'),
            'phed_file_no'   => $request->phed_file_no,
            'approval_dpar'  => $request->approval_dpar,
            'approval_fin'   => $request->approval_fin,
            'account_head_1' => '2215 - Water Supply & Sanitation',
            'account_head_2' => '01 - Water Supply',
            'account_head_3' => '001 - Direction & Administration',
            'account_head_4' => '02 - Administration',
            'account_head_5' => '02 - Estt. of Provisional Employees',
            'account_head_6' => '02 - Wages',
        ])->render();

        // ✅ Update or Create EngagementCard
        EngagementCard::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'fiscal_year' => $fiscalYear,
            ],
            [
                'content'     => $htmlContent,
                'start_date'  => $request->start_date,
                'end_date'    => $request->end_date,
                'card_no'     => $card_no,
            ]
        );

        return redirect()->back()->with('success', 'Engagement card saved successfully.');
    }


//    public function generate(Request $request)
//    {
//        $request->validate([
//            'employee_id' => 'required|exists:employees,id',
//            'start_date'  => 'required|date',
//            'end_date'    => 'required|date|after_or_equal:start_date',
//            'phed_file_no'=> 'required|string',
//            'letter_date'=> 'required|date',
//            'approval_dpar'=> 'required|string',
//            'approval_fin' => 'required|string',
//        ]);
//
//        $employee = Employee::with(['office','remunerationDetail'])->findOrFail($request->employee_id);
//
//        // 🟢 Compute fiscal year
//        $fiscalYear = $this->getFiscalYear($request->start_date, $request->end_date);
//
//        // 🛡 Check uniqueness (optional safety, since DB also enforces it)
//        $alreadyExists = EngagementCard::where('employee_id', $employee->id)
//            ->where('fiscal_year', $fiscalYear)
//            ->exists();
//
//        if ($alreadyExists) {
//            return redirect()->back()->withErrors([
//                'employee_id' => 'This employee already has an engagement card for fiscal year ' . $fiscalYear,
//            ]);
//        }
//
//        // Generate office prefix (first 3 letters of office name, uppercase)
//        $officePrefix = strtoupper(substr(optional($employee->office)->name ?? 'OFF', 0, 3));
//
//        // Count existing engagement cards for this office and period
//        $existingCount = EngagementCard::whereHas('employee', function($q) use ($employee) {
//            $q->where('office_id', $employee->office_id);
//        })
//            ->where('start_date', $request->start_date)
//            ->where('end_date', $request->end_date)
//            ->count();
//
//        $cardNumberSuffix = $existingCount + 1;
//        $card_no = $officePrefix . '-' . $cardNumberSuffix;
//
//        $htmlContent = view('templates.engagement-card', [
//            'name' => $employee->name,
//            'dob' => $employee->date_of_birth,
//            'parent_name' => $employee->parent_name,
//            'address' => $employee->address,
//            'post' => $employee->designation,
//            'pay_matrix' => $employee->remunerationDetail->pay_matrix ?? 'N/A',
//            'remuneration'   =>  number_format($employee->remunerationDetail->remuneration ?? 0, 0, '.', ','),
//            'start_date' => Carbon::parse($request->start_date)->format('d-m-Y'),
//            'end_date' => Carbon::parse($request->end_date)->format('d-m-Y'),
//            'card_no' => $card_no,
//            'date'=> Carbon::parse($request->letter_date)->format('d.m.Y'),
//            'phed_file_no' => $request->phed_file_no,       // <- added
//            'approval_dpar' => $request->approval_dpar,
//            'approval_fin' => $request->approval_fin,
//            'account_head_1' => '2215 - Water Supply & Sanitation',
//            'account_head_2' => '01 - Water Supply',
//            'account_head_3' => '001 - Direction & Administration',
//            'account_head_4' => '02 - Administration',
//            'account_head_5' => '02 - Estt. of Provisional Employees',
//            'account_head_6' => '02 - Wages',
//        ])->render();
//
//        // Create EngagementCard record (now with fiscal_year)
//        $engagementCard = $employee->engagementCard()->create([
//            'content'     => $htmlContent,
//            'start_date'  => $request->start_date,
//            'end_date'    => $request->end_date,
//            'card_no'     => $card_no,
//            'fiscal_year' => $fiscalYear, // 🟢 new field
//        ]);
//
//        return redirect()->back()->with('success', 'Engagement card saved successfully.');
//    }

    public function bulkGenerate(Request $request)
    {
        $request->validate([
            'employee_ids'   => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after_or_equal:start_date',
            'phed_file_no'   => 'required|string',
            'letter_date'    => 'required|date',
            'approval_dpar'  => 'required|string',
            'approval_fin'   => 'required|string',
        ]);

        $employees = Employee::with(['office','remunerationDetail'])
            ->whereIn('id', $request->employee_ids)
            ->get();

        $fiscalYear = $this->getFiscalYear($request->start_date, $request->end_date);

        foreach ($employees as $employee) {

            // 🆕 Engagement card number logic
            if ($employee->engagement_card_no) {
                $card_no = $employee->engagement_card_no;
            } else {
                $existingPrefix = Employee::where('office_id', $employee->office_id)
                    ->whereNotNull('engagement_card_no')
                    ->pluck('engagement_card_no')
                    ->map(function ($card) {
                        return preg_match('/^([A-Z]+)-\d+$/', $card, $m) ? $m[1] : null;
                    })
                    ->filter()
                    ->first();

                $officePrefix = $existingPrefix ?? strtoupper(substr(optional($employee->office)->name ?? 'OFF', 0, 3));

                $lastSuffix = Employee::where('office_id', $employee->office_id)
                    ->whereNotNull('engagement_card_no')
                    ->pluck('engagement_card_no')
                    ->map(function ($card) use ($officePrefix) {
                        return preg_match('/^' . $officePrefix . '-(\d+)$/', $card, $m) ? (int)$m[1] : 0;
                    })
                    ->max();

                $nextSuffix = $lastSuffix ? $lastSuffix + 1 : 1;
                $card_no = $officePrefix . '-' . $nextSuffix;

                $employee->update(['engagement_card_no' => $card_no]);
            }

            // Prepare HTML content
            $htmlContent = view('templates.engagement-card', [
                'name'           => $employee->name,
                'dob'            => Carbon::parse( $employee->date_of_birth)->format('d-m-Y'),
                'parent_name'    => $employee->parent_name,
                'address'        => $employee->address,
                'post'           => $employee->designation,
                'pay_matrix'     => $employee->remunerationDetail->pay_matrix ?? 'N/A',
                'remuneration'   => number_format($employee->remunerationDetail->round_total ?? 0, 0, '.', ','),
                'start_date'     => Carbon::parse($request->start_date)->format('d-m-Y'),
                'end_date'       => Carbon::parse($request->end_date)->format('d-m-Y'),
                'card_no'        => $card_no,
                'date'           => Carbon::parse($request->letter_date)->format('d.m.Y'),
                'phed_file_no'   => $request->phed_file_no,
                'approval_dpar'  => $request->approval_dpar,
                'approval_fin'   => $request->approval_fin,
                'account_head_1' => '2215 - Water Supply & Sanitation',
                'account_head_2' => '01 - Water Supply',
                'account_head_3' => '001 - Direction & Administration',
                'account_head_4' => '02 - Administration',
                'account_head_5' => '02 - Estt. of Provisional Employees',
                'account_head_6' => '02 - Wages',
            ])->render();

            // 🔄 Replace if exists, else create
            $employee->engagementCard()
                ->updateOrCreate(
                    ['fiscal_year' => $fiscalYear], // match by fiscal year
                    [
                        'content'     => $htmlContent,
                        'start_date'  => $request->start_date,
                        'end_date'    => $request->end_date,
                        'card_no'     => $card_no,
                    ]
                );
        }

        return redirect()->back()->with('success', 'Bulk engagement cards saved/updated successfully.');
    }


//    public function bulkGenerate(Request $request)
//    {
//        $request->validate([
//            'employee_ids'   => 'required|array',
//            'employee_ids.*' => 'exists:employees,id',
//            'start_date'     => 'required|date',
//            'end_date'       => 'required|date|after_or_equal:start_date',
//            'phed_file_no'   => 'required|string',
//            'letter_date'=> 'required|date',
//            'approval_dpar'  => 'required|string',
//            'approval_fin'   => 'required|string',
//        ]);
//
//        $employees = Employee::with(['office','remunerationDetail'])
//            ->whereIn('id', $request->employee_ids)
//            ->get();
//
//        // Compute fiscal year once
//        $fiscalYear = $this->getFiscalYear($request->start_date, $request->end_date);
//
//        foreach ($employees as $employee) {
//
//            // 🛡 Skip if card for this fiscal year already exists
//            $alreadyExists = EngagementCard::where('employee_id', $employee->id)
//                ->where('fiscal_year', $fiscalYear)
//                ->exists();
//
//            if ($alreadyExists) {
//                // Optionally skip silently or log it
//                // continue;
//                // or maybe collect IDs to notify user later
//                continue;
//            }
//
//            // Generate office prefix (first 3 letters of office name, uppercase)
//            $officePrefix = strtoupper(substr(optional($employee->office)->name ?? 'OFF', 0, 3));
//
//            // Count existing engagement cards for this office and period
//            $existingCount = EngagementCard::whereHas('employee', function($q) use ($employee) {
//                $q->where('office_id', $employee->office_id);
//            })
//                ->where('start_date', $request->start_date)
//                ->where('end_date', $request->end_date)
//                ->count();
//
//            $cardNumberSuffix = $existingCount + 1;
//            $card_no = $officePrefix . '-' . $cardNumberSuffix;
//
//            // Prepare HTML content using the template
//            $htmlContent = view('templates.engagement-card', [
//                'name'           => $employee->name,
//                'dob'            => $employee->date_of_birth,
//                'parent_name'    => $employee->parent_name,
//                'address'        => $employee->address,
//                'post'           => $employee->designation,
//                'pay_matrix'     => $employee->remunerationDetail->pay_matrix ?? 'N/A',
//                'remuneration'   =>  number_format($employee->remunerationDetail->remuneration ?? 0, 0, '.', ','),
//                'start_date'     =>  Carbon::parse($request->start_date)->format('d-m-Y'),
//                'end_date'       => Carbon::parse($request->end_date)->format('d-m-Y'),
//                'card_no'        => $card_no,
//                'date'           =>  Carbon::parse($request->letter_date)->format('d.m.Y'),
//                'phed_file_no'   => $request->phed_file_no,
//                'approval_dpar'  => $request->approval_dpar,
//                'approval_fin'   => $request->approval_fin,
//                'account_head_1' => '2215 - Water Supply & Sanitation',
//                'account_head_2' => '01 - Water Supply',
//                'account_head_3' => '001 - Direction & Administration',
//                'account_head_4' => '02 - Administration',
//                'account_head_5' => '02 - Estt. of Provisional Employees',
//                'account_head_6' => '02 - Wages',
//            ])->render();
//
//            // Save EngagementCard (with fiscal_year)
//            $employee->engagementCard()->create([
//                'content'     => $htmlContent,
//                'start_date'  => $request->start_date,
//                'end_date'    => $request->end_date,
//                'card_no'     => $card_no,
//                'fiscal_year' => $fiscalYear, // 🟢 new field
//            ]);
//        }
//
//        return redirect()->back()->with('success', 'Bulk engagement cards saved successfully.');
//    }

    public function update(Request $request, EngagementCard $model)
    {

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('store-engagement-card'), 403, 'Access Denied');

        $request->validate([
            'html_content' => 'required|string',
        ]);

        $data=$this->validate($request, [
            'html_content' => 'required|string',
        ]);

        DB::transaction(function () use ($data, $model) {
            $model->content = $data['html_content'];
            $model->update();
        });

        return redirect()->back()->with('success', 'Engagement card content updated successfully.');


    }

    public function bulkDownload(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        // Compute fiscal year from input dates
        $fiscalYear = $this->getFiscalYear($request->start_date, $request->end_date);

        $zip = new \ZipArchive();
        $zipName = 'EngagementCards_' . now()->format('Ymd_His') . '.zip';
        $zipPath = storage_path('app/public/' . $zipName);

        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {

            $employees = Employee::with(['engagementCard' => function($q) use ($fiscalYear) {
                // Only get cards for the requested fiscal year
                $q->where('fiscal_year', $fiscalYear);
            }])
                ->whereIn('id', $request->employee_ids)
                ->get();

            foreach ($employees as $employee) {
                if ($employee->engagementCard && $employee->engagementCard->count()) {
                    foreach ($employee->engagementCard as $card) {
                        $pdf = \PDF::loadHTML($card->content);
                        $pdfName = "EngagementCard_{$employee->name}_{$card->card_no}.pdf";
                        $zip->addFromString($pdfName, $pdf->output());
                    }
                }
            }

            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function download(EngagementCard $model)
    {

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('download-engagement-card'), 403, 'Access Denied');



        if (!$model) {
            abort(404, 'Engagement card not found.');
        }

        $pdf = Pdf::loadHTML($model->content)
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
            ]);

        return $pdf->download("EngagementCard_{$model->card_no}.pdf");

    }

    public function destroy(EngagementCard $model){
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('delete-engagement-card'),403,'Access Denied');
        $model->delete();

        return redirect()->back()->with('success', 'Engagement card deleted successfully.');
    }

}
