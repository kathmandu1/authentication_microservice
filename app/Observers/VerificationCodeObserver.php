<?php

namespace App\Observers;

use App\Jobs\Registration\OtpJob;
use App\Mail\OtpVerificationMail;
use App\Models\VerificationCode;
use App\Services\SMS\OTPService;
use Illuminate\Support\Facades\Mail;

class VerificationCodeObserver
{
    /**
     * Handle the VerificationCode "created" event.
     */
    public function created(VerificationCode $verificationCode): void
    {
        // dd($verificationCode->otp);
        //send sms using  OTPService
        // $otpService = new OTPService();

        // $otpService->sendOtp($verificationCode->phone, $verificationCode->otp);
        // Mail::to($verificationCode->email)->send(new OtpVerificationMail($verificationCode));
        OtpJob::dispatch($verificationCode);
    }

    /**
     * Handle the VerificationCode "updated" event.
     */
    public function updated(VerificationCode $verificationCode): void
    {
        //
    }

    /**
     * Handle the VerificationCode "deleted" event.
     */
    public function deleted(VerificationCode $verificationCode): void
    {
        //
    }

    /**
     * Handle the VerificationCode "restored" event.
     */
    public function restored(VerificationCode $verificationCode): void
    {
        //
    }

    /**
     * Handle the VerificationCode "force deleted" event.
     */
    public function forceDeleted(VerificationCode $verificationCode): void
    {
        //
    }

    public function saving(VerificationCode $verificationCode)
    {
        // Mail::to($verificationCode->email)->send(new OtpVerificationMail($verificationCode));
    }
}
