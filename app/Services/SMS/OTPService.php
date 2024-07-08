<?php

namespace App\Services\SMS;

use App\Models\Setting;

class OTPService
{
    public function sendOtp($phone, $otp)
    {
        if (!$this->checkSmsSetting()) {
            return false;
        }
        $authToken = env('SMS_AUTH_TOKEN');
        $args = http_build_query([
            'auth_token' => $authToken,
            'to'    => $phone,
            // 'text'  => 'your otp is '.$otp));
            'text' => 'Dear Customer, Please use your OTP ' . $otp . ' to sign up with Mero Discounts. Also, we request you not to share this OTP with anyone. Happy Shopping!',
        ]);
        $url = 'https://sms.aakashsms.com/sms/v3/send/';

        // Make the call using API.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1); ///
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // Response
        $response = curl_exec($ch);
        curl_close($ch);
    }

    public function sendSms($phone, $messsage)
    {
        if (!$this->checkSmsSetting()) {
            return false;
        }

        $authToken = env('SMS_AUTH_TOKEN');
        $args = http_build_query([
            'auth_token' => $authToken,
            'to'    => $phone,
            // 'text'  => 'your otp is '.$otp));
            'text' => $messsage,
        ]);
        $url = 'https://sms.aakashsms.com/sms/v3/send/';

        // Make the call using API.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1); ///
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // Response
        $response = curl_exec($ch);
        curl_close($ch);
    }

    protected function checkSmsSetting()
    {
        // $smsSetting = Setting::where('key', 'sms_otp_use_setting')->first();
        // if ($smsSetting->value == 1) {
        //     return true;
        // }

        // return false;
        return true;
    }
}
