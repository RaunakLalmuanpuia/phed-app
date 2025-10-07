<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EngagementCard;
use App\Models\Otp;
use App\Models\User;
use App\Util\AppUtil;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BioController extends Controller
{
    //

    public function index(){

        return inertia('Frontend/Bio/Index',[
        ]);
    }

    private function throwInvalidForgotPasswordError(string $str)
    {
        $error = \Illuminate\Validation\ValidationException::withMessages([
            'userId' => [$str],
        ]);
        throw $error;
    }

    public function sendOtp(Request $request)
    {
        $data = $this->validate($request, [
            'userId' => 'required'
        ]);

        $mobileRegex = '/^[0-9]{10}$/';

        if (!preg_match($mobileRegex, $data['userId'])) {
            throw ValidationException::withMessages([
                'userId' => ['Invalid mobile number.']
            ]);
        }

        $existPhone = Employee::query()->where('mobile', $data['userId'])->exists();

        if (!$existPhone) {
            $this->throwInvalidForgotPasswordError('Employee not found with mobile number.');
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
            throw ValidationException::withMessages([
                'userId' => ['Invalid mobile number.']
            ]);
        }

        $verified = Otp::query()
            ->where('type', 'BIO')
            ->where('recipient', $data['userId'])
            ->where('otp', $data['otp'])
            ->latest()
            ->first();



        if (!$verified) {
            throw ValidationException::withMessages([
                'otp' => ['Invalid or expired OTP.']
            ]);
        }

        $verified->used=true;
        $verified->save();

        // Get employee by mobile
        $employee = Employee::query()
            ->where('mobile', $data['userId'])
            ->first();

        return response()->json([
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

        return inertia('Frontend/Bio/Show', [
            'data' => $employee
        ]);
    }


    public function downloadEngagementCard(EngagementCard $model)
    {

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

}
