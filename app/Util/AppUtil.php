<?php

namespace App\Util;


use Illuminate\Support\Facades\Http;

class AppUtil{
    public static function sendOtp($otp,$recipient)
    {
        $response=Http::withHeaders([
            'Authorization' => "Bearer " . env('SMS_TOKEN'),
        ])->get("https://sms.msegs.in/api/send-otp",[
            'template_id' => '1407175265827075678',
            'message' => 'Your OTP verification code is '.$otp.'. Validity of this OTP is 10 minutes. EGOVMZ',
            'recipient'=>$recipient
        ]);
        if ($response->successful()) {
            return true;
        }

        return false;
    }
}

