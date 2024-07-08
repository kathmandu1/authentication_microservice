<?php

declare(strict_types=1);

namespace App\Services\Otp;

use App\DTO\OTP\OtpVerificationRequesttData;
use App\Models\VerificationCode;
use App\Traits\FileUpload;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OtpService
{
    use FileUpload;

    public function __construct(protected VerificationCode $verificationCode)
    {
    }

    /** @inheritDoc */
    public function store($request): VerificationCode|Exception
    {
        try {
            $request = new Request($request);

            return $this->verificationCode->create([
                'email' => $request->email,
                'user_id' => $request->user_id,
                'otp' => $request->otp,
                'expire_at' =>Carbon::now()->addHours(2),
                'expire_at' => now()->addMinute(2),

                'phone' => $request->phone,

            ]);

        } catch (Exception $e) {
            return $e;
        }

    }

    public function update($request) : bool|string
    {
        try {
            DB::beginTransaction();
            $verificationCode = $this->verificationCode->where([
                'user_id' => !is_null(auth('api')->user()) ? auth('api')->user()->id : $request->user_id,
                'otp' => $request->otp,
                'status' => false,

            ])->first();

            // dd($verificationCode);

            if (is_null($verificationCode)) {
                return false;
            }

            // dd($request instanceof OtpVerificationRequesttData);

            $verificationCode = $verificationCode->update(['status' => true, 'reset_token' => $request->reset_token]);
            if ($request instanceof OtpVerificationRequesttData) {
                auth()->user()->update(['email_verified_at' => Carbon::now()]);
            } elseif (!$request->has('user_id')) {
                auth()->user()->update(['email_verified_at' => Carbon::now()]);
            }

        } catch (Exception $e) {
            DB::rollBack();

            return $e->getMessage();
        }
        DB::commit();

        return $verificationCode;

    }
}
