<?php

namespace App\Http\Controllers;

use App\Models\EditRequest;
use App\Models\TransferRequest;
use App\Models\DeletionRequest;

class NotificationController extends Controller
{
    public function myNotifications()
    {
        $user = auth()->user();

        // 🔹 Determine filter conditions based on role
        if ($user->hasRole('Admin')) {
            $statusFilter = ['pending'];
            $officeIds = null; // all offices
        } elseif ($user->hasRole('Manager')) {
            $statusFilter = ['approved', 'rejected']; // exclude pending
            $officeIds = $user->offices->pluck('id')->toArray(); // manager's offices
        } else {
            return inertia('Backend/Notification/List', [
                'notifications' => [],
            ]);
        }

        // 🔹 Edit Requests
        $editRequests = EditRequest::with('employee.office')
            ->when($officeIds, function ($q) use ($officeIds) {
                $q->whereHas('employee.office', function ($q) use ($officeIds) {
                    $q->whereIn('offices.id', $officeIds);
                });
            })
            ->whereIn('approval_status', $statusFilter)
            ->get()
            ->map(function ($req) {
                return [
                    'id' => "edit-" . $req->id,
                    'type' => 'Edit Request',
                    'employee_name' => $req->employee->name,
                    'employee_id' => $req->employee->id,
                    'office' => $req->employee->office->name ?? '-',
                    'created_at' => $req->request_date,
                    'status' => $req->approval_status,
                ];
            })
            ->toArray();

        // 🔹 Transfer Requests
        $transferRequests = TransferRequest::with(['employee.office', 'currentOffice', 'requestedOffice'])
            ->when($officeIds, function ($q) use ($officeIds) {
                $q->whereHas('employee.office', function ($q) use ($officeIds) {
                    $q->whereIn('offices.id', $officeIds);
                });
            })
            ->whereIn('approval_status', $statusFilter)
            ->get()
            ->map(function ($req) {
                return [
                    'id' => "transfer-" . $req->id,
                    'type' => 'Transfer Request',
                    'employee_name' => $req->employee->name,
                    'employee_id' => $req->employee->id,
                    'office' => ($req->currentOffice?->name ?? '-') . ' → ' . ($req->requestedOffice?->name ?? '-'),
                    'created_at' => $req->request_date,
                    'status' => $req->approval_status,
                ];
            })
            ->toArray();

        // 🔹 Deletion Requests
        $deletionRequests = DeletionRequest::with('employee.office')
            ->when($officeIds, function ($q) use ($officeIds) {
                $q->whereHas('employee.office', function ($q) use ($officeIds) {
                    $q->whereIn('offices.id', $officeIds);
                });
            })
            ->whereIn('approval_status', $statusFilter)
            ->get()
            ->map(function ($req) {
                return [
                    'id' => "delete-" . $req->id,
                    'type' => 'Deletion Request',
                    'employee_name' => $req->employee->name,
                    'employee_id' => $req->employee->id,
                    'office' => $req->employee->office->name ?? '-',
                    'created_at' => $req->request_date,
                    'status' => $req->approval_status,
                ];
            })
            ->toArray();

        // 🔹 Merge all and sort
        $notifications = collect($editRequests)
            ->merge($transferRequests)
            ->merge($deletionRequests)
            ->sortByDesc('created_at')
            ->values()
            ->all();

        return inertia('Backend/Notification/List', [
            'notifications' => $notifications,
        ]);
    }



}
