<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EngagementCard;
use App\Models\Otp;
use App\Util\AppUtil;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EmployeeBioController extends Controller
{

    public function sendOtp(Request $request)
    {
        $data = $this->validate($request, [
            'userId' => 'required'
        ]);

        $mobileRegex = '/^[0-9]{10}$/';


        if (!preg_match($mobileRegex, $data['userId'])) {

            return response()->json([
                'status'=>429,
                'message'=>'Invalid mobile number'
            ]);

        }

        $existPhone = Employee::query()->where('mobile', $data['userId'])->exists();

        if (!$existPhone) {
            return response()->json([
                'status'=>404,
                'message'=>'Employee not found with mobile number'
            ]);

        }

        $otp = env("APP_DEBUG") ? 1111 : rand(1000, 9999);

        Otp::query()->create([
            'recipient' => $data['userId'],
            'type' => 'BIO',
            'otp' => $otp,
        ]);

        // (Optional) Send OTP via SMS gateway here

        AppUtil::sendOtp($otp, $data['userId']);

        return response()->json([
            'status'=>200,
            'message' => 'OTP sent to ' . $data['userId'],
            'type' => 'BIO'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $data = $this->validate($request, [
            'userId' => 'required',
            'otp' => 'required'
        ]);

        $mobileRegex = '/^[0-9]{10}$/';

        if (!preg_match($mobileRegex, $data['userId'])) {
            return response()->json([
                'status'=>429,
                'message'=>'Invalid mobile number'
            ]);
        }

        $verified = Otp::query()
            ->where('type', 'BIO')
            ->where('recipient', $data['userId'])
            ->where('otp', $data['otp'])
            ->latest()
            ->first();



        if (!$verified) {
            return response()->json([
                'status'=>404,
                'message'=>'Invalid or expired OTP. '
            ]);

        }

        $verified->used=true;
        $verified->save();

        // Get employee by mobile
        $employee = Employee::query()
            ->where('mobile', $data['userId'])
            ->first();

        return response()->json([
            'status'=>200,
            'message' => 'OTP verified successfully.',
            'data' => $verified,
            'mobile' => $employee->mobile,
        ]);
    }

    public function show(Employee $employee)
    {
        $employee->load([
            'office', 'documents.type', 'transfers.oldOffice', 'transfers.newOffice',
            'scheme', 'deletionDetail', 'remunerationDetail', 'engagementCard',
        ]);

        return response()->json([
            'status'=>200,
            'data' => $employee,
        ]);


    }


    public function downloadEngagementCard(EngagementCard $model)
    {

        if (!$model) {
            return response()->json([
                'status'=>404,
                'message'=>'Engagement card not found. '
            ]);
        }

        $pdf = Pdf::loadHTML($model->content)
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
            ]);


        $pdfContent = $pdf->output();

        return response($pdfContent, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="EngagementCard_'.$model->card_no.'.pdf"');

    }
}
