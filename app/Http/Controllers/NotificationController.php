<?php

namespace App\Http\Controllers;

use App\Models\DocumentDeleteRequest;
use App\Models\DocumentEditRequest;
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


        $documentUpdateRequests = DocumentEditRequest::with('employee.office')
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
                    'type' => 'Document Update Request',
                    'employee_name' => $req->employee->name,
                    'employee_id' => $req->employee->id,
                    'office' => $req->employee->office->name ?? '-',
                    'created_at' => $req->request_date,
                    'status' => $req->approval_status,
                ];
            })
            ->toArray();

        $documentDeleteRequests = DocumentDeleteRequest::with('employee.office')
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
                    'type' => 'Document Delete Request',
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
            ->merge($documentUpdateRequests)
            ->merge($documentDeleteRequests)
            ->sortByDesc('created_at')
            ->values()
            ->all();

        return inertia('Backend/Notification/List', [
            'notifications' => $notifications,
        ]);
    }


    public function count()
    {
        $user = auth()->user();
        $notificationCount = 0;

        if ($user) {
            $officeIds = null;
            $statusFilter = [];
            $dateLimit = null;
            $dateColumn = null;

            if ($user->hasRole('Admin')) {
                // Admin: Only pending, no date filtering
                $statusFilter = ['pending'];
                $officeIds = null; // All offices
                // no date filtering, $dateLimit stays null
            } elseif ($user->hasRole('Manager')) {
                // Manager: Only approved/rejected from the last 5 days
                $statusFilter = ['approved', 'rejected'];
                $officeIds = $user->offices->pluck('id')->toArray();
                $dateLimit = now()->subDays(5);
                $dateColumn = 'approval_date'; // use approval_date for managers
            }

            if (!empty($statusFilter)) {
                $filterQuery = function ($q) use ($officeIds, $dateLimit, $dateColumn) {
                    if ($officeIds) {
                        $q->whereHas('employee.office', function ($sub) use ($officeIds) {
                            $sub->whereIn('offices.id', $officeIds);
                        });
                    }
                    if ($dateLimit && $dateColumn) {
                        $q->where($dateColumn, '>=', $dateLimit);
                    }
                };

                $notificationCount += \App\Models\EditRequest::whereIn('approval_status', $statusFilter)
                    ->where($filterQuery)
                    ->count();

                $notificationCount += \App\Models\TransferRequest::whereIn('approval_status', $statusFilter)
                    ->where($filterQuery)
                    ->count();

                $notificationCount += \App\Models\DeletionRequest::whereIn('approval_status', $statusFilter)
                    ->where($filterQuery)
                    ->count();

                $notificationCount += \App\Models\DocumentEditRequest::whereIn('approval_status', $statusFilter)
                    ->where($filterQuery)
                    ->count();

                $notificationCount += \App\Models\DocumentDeleteRequest::whereIn('approval_status', $statusFilter)
                    ->where($filterQuery)
                    ->count();
            }
        }

        return response()->json([
            'count' => $notificationCount,
        ]);
    }




}
