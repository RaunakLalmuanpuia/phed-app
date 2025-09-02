<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EngagementCard;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
class EngagementCardController extends Controller
{
    // --- API: get template HTML
    public function show(Employee $employee)
    {
        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('view-engagement-card'), 403, 'Access Denied');

        // Check if engagement card exists for employee
        $card = $employee->engagementCard;

        if ($card) {
            return response()->json($card->content);
        }

        // Generate new engagement card HTML
        $htmlContent = view('templates.engagement-card', [
            'card_no' => 'RWA-16',
            'date' => now()->format('d.m.Y'),
            'name' => $employee->name,
            'dob' => $employee->date_of_birth,
            'parent_name' => $employee->parent_name,
            'address' => $employee->address,
            'post' => $employee->designation,
            'pay_matrix' => 'Level 5 (29,200-64,700)',
            'remuneration' => 23334,
            'start_date' => '01-03-2024',
            'end_date' => '28-02-2025',
            'approval_dpar' => 'No. ARW/PHE-A/2023-2024/B-319  Dt.27.02.2024',
            'approval_fin' => 'No. FIN(EC)827/2023 Dt. 08.03.2024',
            'account_head_1' => '2215 - Water Supply & Sanitation',
            'account_head_2' => '  01 - Water Supply',
            'account_head_3' => ' 001 - Direction & Administration',
            'account_head_4' => '  02 - Administration',
            'account_head_5' => '  02 - Establishment of MR Employees',
            'account_head_6' => '  02 - Wages',
            'officer_name' => 'H. DUHKIMA',
            'officer_designation' => 'Engineer-in-Chief: PHED Mizoram: Aizawl',
        ])->render();

        return response()->json($htmlContent);
    }

    public function store(Request $request, Employee $employee){

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('store-engagement-card'), 403, 'Access Denied');

        $request->validate([
            'html_content' => 'required|string',
        ]);

        $htmlContent = $request->input('html_content');

        // Find existing engagement card or create a new instance
        $card = EngagementCard::updateOrCreate(
            ['employee_id' => $employee->id],
            ['content' => $htmlContent]
        );

        return redirect()->back()->with('success', 'Engagement card saved successfully.');

    }
    public function download(Employee $employee)
    {

        $user = auth()->user();
        abort_if(!$user->hasPermissionTo('download-engagement-card'), 403, 'Access Denied');

        $card = $employee->engagementCard;

        if (!$card) {
            abort(404, 'Engagement card not found.');
        }

        $pdf = Pdf::loadHTML($card->content)
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
            ]);

        return $pdf->download("EngagementCard_{$employee->id}.pdf");

    }



    public function generate(Request $request)
    {
//        dd($request->all());
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'phed_file_no' => 'required|string',
            'approval_dpar'=> 'required|string',
            'approval_fin' => 'required|string',
        ]);

        $employee = Employee::with(['office','remunerationDetail'])->findOrFail($request->employee_id);

        // Generate office prefix (first 3 letters of office name, uppercase)
        $officePrefix = strtoupper(substr(optional($employee->office)->name ?? 'OFF', 0, 3));

        // Count existing engagement cards for this office and period
        $existingCount = EngagementCard::whereHas('employee', function($q) use ($employee) {
            $q->where('office_id', $employee->office_id);
        })
            ->where('start_date', $request->start_date)
            ->where('end_date', $request->end_date)
            ->count();

        $cardNumberSuffix = $existingCount + 1;
        $card_no = $officePrefix . '-' . $cardNumberSuffix;

        $htmlContent = view('templates.engagement-card', [
            'name' => $employee->name,
            'dob' => $employee->date_of_birth,
            'parent_name' => $employee->parent_name,
            'address' => $employee->address,
            'post' => $employee->designation,
            'pay_matrix' => "Level 5 (29,200-64,700)",
            'remuneration' => $employee->remunerationDetail->remuneration,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'card_no' => $card_no,
            'date'=> now(),
            'phed_file_no' => $request->phed_file_no,       // <- added
            'approval_dpar' => $request->approval_dpar,
            'approval_fin' => $request->approval_fin,
            'account_head_1' => '2215 - Water Supply & Sanitation',
            'account_head_2' => '01 - Water Supply',
            'account_head_3' => '001 - Direction & Administration',
            'account_head_4' => '02 - Administration',
            'account_head_5' => '02 - Establishment of MR Employees',
            'account_head_6' => '02 - Wages',
        ])->render();

        // Create EngagementCard record
        $engagementCard = $employee->engagementCard()->create([
            'content' => $htmlContent,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'card_no' => $card_no,
        ]);

//        // Generate PDF for download
//        $pdf = \PDF::loadHTML($htmlContent);
//        $filename = 'Engagement_Card_' . $card_no . '.pdf';
//
//        return $pdf->download($filename);

        return redirect()->back()->with('success', 'Engagement card saved successfully.');
    }

    public function bulkGenerate(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'phed_file_no' => 'required|string',
            'approval_dpar'=> 'required|string',
            'approval_fin' => 'required|string',
        ]);

        $employees = Employee::with(['office','remunerationDetail'])
            ->whereIn('id', $request->employee_ids)
            ->get();

        foreach ($employees as $employee) {

            // Generate office prefix (first 3 letters of office name, uppercase)
            $officePrefix = strtoupper(substr(optional($employee->office)->name ?? 'OFF', 0, 3));

            // Count existing engagement cards for this office and period
            $existingCount = EngagementCard::whereHas('employee', function($q) use ($employee) {
                $q->where('office_id', $employee->office_id);
            })
                ->where('start_date', $request->start_date)
                ->where('end_date', $request->end_date)
                ->count();

            $cardNumberSuffix = $existingCount + 1;
            $card_no = $officePrefix . '-' . $cardNumberSuffix;

            // Prepare HTML content using the template
            $htmlContent = view('templates.engagement-card', [
                'name' => $employee->name,
                'dob' => $employee->date_of_birth,
                'parent_name' => $employee->parent_name,
                'address' => $employee->address,
                'post' => $employee->designation,
                'pay_matrix' => $employee->remunerationDetail->pay_matrix ?? 'N/A',
                'remuneration' => $employee->remunerationDetail->remuneration ?? 0,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'card_no' => $card_no,
                'date'=> now(),
                'phed_file_no' => $request->phed_file_no,
                'approval_dpar' => $request->approval_dpar,
                'approval_fin' => $request->approval_fin,
                'account_head_1' => '2215 - Water Supply & Sanitation',
                'account_head_2' => '01 - Water Supply',
                'account_head_3' => '001 - Direction & Administration',
                'account_head_4' => '02 - Administration',
                'account_head_5' => '02 - Establishment of MR Employees',
                'account_head_6' => '02 - Wages',
            ])->render();

            // Save EngagementCard
            $employee->engagementCard()->create([
                'content' => $htmlContent,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'card_no' => $card_no,
            ]);
        }

        return redirect()->back()->with('success', 'Bulk engagement cards saved successfully.');
    }

    public function bulkDownload(Request $request)
    {
//        dd($request);
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'start_date' => 'required|digits:4', // only 4-digit year
            'end_date' => 'required|digits:4',
        ]);

        $zip = new \ZipArchive();
        $zipName = 'EngagementCards_' . now()->format('Ymd_His') . '.zip';
        $zipPath = storage_path('app/public/' . $zipName);

        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {

            $employees = Employee::with(['engagementCard' => function($q) use ($request) {
                if ($request->start_date) {
                    $q->whereYear('start_date', '>=', $request->start_date);
                }
                if ($request->end_date) {
                    $q->whereYear('end_date', '<=', $request->end_date);
                }
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


    public function generateBatchEngagementCardsPdfZip($office_id, Carbon $start_date, Carbon $end_date)
    {
        $employees = Employee::where('office_id', $office_id)->get();
        if ($employees->isEmpty()) {
            abort(404, 'No employees found for this office.');
        }

        $officePrefix = strtoupper(substr(optional($employees->first()->office)->name ?? 'OFF', 0, 3));

        $existingCount = EngagementCard::whereHas('employee', function ($q) use ($office_id) {
            $q->where('office_id', $office_id);
        })->where('start_date', $start_date)
            ->where('end_date', $end_date)
            ->count();

        $cardCounter = $existingCount;

        // Create a temp zip file
        $zipFileName = storage_path("app/engagement_cards_{$office_id}_{$start_date->format('Ym')}.zip");
        if (file_exists($zipFileName)) {
            unlink($zipFileName);
        }

        $zip = new ZipArchive();
        if ($zip->open($zipFileName, ZipArchive::CREATE) !== true) {
            abort(500, 'Cannot create zip file.');
        }

        foreach ($employees as $employee) {
            $cardCounter++;
            $card_no = $officePrefix . '-' . $cardCounter;

            $content = view('engagement_card_template', [
                'card_no'       => $card_no,
                'date'          => now()->format('d/m/Y'),
                'name'          => $employee->name,
                'dob'           => $employee->date_of_birth->format('d/m/Y'),
                'parent_name'   => $employee->parent_name,
                'address'       => $employee->address,
                'post'          => $employee->designation,
                'pay_matrix'    => $employee->post_per_qualification,
                'remuneration'  => $employee->remuneration ?? 0,
                'start_date'    => $start_date->format('d/m/Y'),
                'end_date'      => $end_date->format('d/m/Y'),
                'approval_dpar' => $employee->approval_dpar ?? '',
                'approval_fin'  => $employee->approval_fin ?? '',
                'account_head_1'=> 'Major Head 1',
                'account_head_2'=> 'Sub Head 1',
                'account_head_3'=> 'Sub Head 2',
                'account_head_4'=> 'Sub Head 3',
                'account_head_5'=> 'Sub Head 4',
                'account_head_6'=> 'Sub Head 5',
            ])->render();

            // Persist card record
            EngagementCard::create([
                'employee_id' => $employee->id,
                'content'     => $content,
                'start_date'  => $start_date,
                'end_date'    => $end_date,
                'card_no'     => $card_no,
            ]);

            // Generate PDF in-memory
            $pdf = Pdf::loadHTML($content)->setPaper('a4', 'portrait');
            $pdfData = $pdf->output();

            // Add PDF directly to zip from memory
            $zip->addFromString("{$card_no}.pdf", $pdfData);
        }

        $zip->close();

        return Response::download($zipFileName)->deleteFileAfterSend(true);
    }

    public function downloadEngagementCardsZip()
    {
        $office_id = 1;
        $start_date = Carbon::createFromFormat('d/m/Y', '01/09/2025');
        $end_date = Carbon::createFromFormat('d/m/Y', '30/09/2025');

        return $this->generateBatchEngagementCardsPdfZip($office_id, $start_date, $end_date);
    }



}
