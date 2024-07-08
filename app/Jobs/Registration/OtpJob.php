<?php

namespace App\Jobs\Registration;

use App\Mail\OtpVerificationMail;
use App\Models\VerificationCode;
use App\Services\SMS\OTPService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class OtpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public VerificationCode $verificationCode)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $verificationCode = $this->verificationCode;
        $otpService = new OTPService();
        $otpService->sendOtp($verificationCode->phone, $verificationCode->otp);
        Mail::to($verificationCode->email)->send(new OtpVerificationMail($verificationCode));
    }
}
