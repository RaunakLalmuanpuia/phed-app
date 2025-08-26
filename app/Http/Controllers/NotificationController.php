<?php

namespace App\Http\Controllers;

use App\Models\EditRequest;
use App\Models\TransferRequest;
use App\Models\DeletionRequest;

class NotificationController extends Controller
{
    public function myNotifications()
    {
        // 🔹 Edit Requests
        $editRequests = EditRequest::with('employee.office')
            ->where('approval_status', 'pending')
            ->get()
            ->map(function ($req) {
                return [
                    'id' => "edit-" . $req->id,
                    'type' => 'Edit Request',
                    'employee_name' => $req->employee->name,
                    'employee_id' => $req->employee->id, // <-- only employee ID
                    'office' => $req->employee->office->name ?? '-',
                    'created_at' => $req->request_date,
                ];
            })
            ->toArray();

        // 🔹 Transfer Requests
        $transferRequests = TransferRequest::with(['employee.office', 'currentOffice', 'requestedOffice'])
            ->where('approval_status', 'pending')
            ->get()
            ->map(function ($req) {
                return [
                    'id' => "transfer-" . $req->id,
                    'type' => 'Transfer Request',
                    'employee_name' => $req->employee->name,
                    'employee_id' => $req->employee->id, // <-- only employee ID
                    'office' => ($req->currentOffice?->name ?? '-') . ' → ' . ($req->requestedOffice?->name ?? '-'),
                    'created_at' => $req->request_date,
                ];
            })
            ->toArray();

        // 🔹 Deletion Requests
        $deletionRequests = DeletionRequest::with('employee.office')
            ->where('approval_status', 'pending')
            ->get()
            ->map(function ($req) {
                return [
                    'id' => "delete-" . $req->id,
                    'type' => 'Deletion Request',
                    'employee_name' => $req->employee->name,
                    'employee_id' => $req->employee->id, // <-- only employee ID
                    'office' => $req->employee->office->name ?? '-',
                    'created_at' => $req->request_date,
                ];
            })
            ->toArray();

        // 🔹 Merge all and sort
        $notifications = collect($editRequests)
            ->merge($transferRequests)
            ->merge($deletionRequests)
            ->sortByDesc('created_at')
            ->values()
            ->all(); // plain PHP array

        return inertia('Backend/Notification/List', [
            'notifications' => $notifications,
        ]);
    }


}
