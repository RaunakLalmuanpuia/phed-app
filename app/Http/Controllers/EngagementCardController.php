<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EngagementCard;
use Illuminate\Http\Request;
use PDF;

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

        $pdf = PDF::loadHTML($card->content);

        return $pdf->download("EngagementCard_{$employee->id}.pdf");
    }

}
