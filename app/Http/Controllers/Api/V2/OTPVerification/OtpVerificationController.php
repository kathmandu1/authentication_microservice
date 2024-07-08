<?php

namespace App\Http\Controllers\Api\V2\OTPVerification;

use App\Exceptions\OtpVerificationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\OtpVerificationStoreRequest;
use App\Jobs\Registration\OtpJob;
use App\Models\User;
use App\Models\VerificationCode;
use App\Repositories\OtpRepository;
use App\Repositories\UserRepository;
use App\Services\Otp\OtpService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OtpVerificationController extends Controller
{
    public function __construct(
        protected OtpRepository $otpRepository,
        protected OtpService $otpService,
        protected VerificationCode $verificationCode,
        protected UserRepository $userRepository,
    ) {
    }

    public function update(OtpVerificationStoreRequest $otpVerificationStoreRequest, $otp)
    {
        try {

            $otpVerify = $this->otpService->update($otpVerificationStoreRequest->toDto());

            if (!$otpVerify) {
                throw new OtpVerificationException('OTP code you have provided is not valid');
            }
            // if (Carbon::parse($user->otp_expires_at)->isPast()) {
            //     throw new OtpVerificationException('OTP code you have provided is expired');
            // }
        } catch (OtpVerificationException $e) {
            $message = $otpVerificationStoreRequest->user_id . ' is facing problem in  update otp from mobile ';
            Log::error($message . ' error is ' . $e->getMessage());

            return response(['message' => $e->getMessage(), 'success' => false], 400);
        } catch (Exception $e) {
            $message = $otpVerificationStoreRequest->user_id . ' is facing problem in update otp from mobile ';
            Log::error($message . ' error is ' . $e->getMessage());

            return response(['message' => $e->getMessage(), 'success' => false], 400);
        }

        return response(['message', 'Successfully Verify OTP']);
    }

    /**
     *  @OA\Post(
     *     path="/api/v2/verification/verify",
     *     summary="Api end point to verify OTP along with phone",
     *     tags={"Otp Verification"},
     *     security={{"bearer":{}}},
     *      @OA\Parameter(
     *         name="otp",
     *         in="query",
     *         description="otp code you receive in phone ",
     *         required=true,
     *         @OA\Schema(type="string"),
     *        ),
     *      @OA\Parameter(
     *         name="phone",
     *         in="query",
     *         description="phone number where you have send otp ",
     *         required=false,
     *         @OA\Schema(type="string"),
     *        ),
     *      @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="email address where you have send otp ",
     *         required=false,
     *         @OA\Schema(type="string"),
     *        ),
     *     @OA\Response(
     *        response="204",
     *        description="Successful response",
     *         @OA\JsonContent(
     *          )
     *     ),
     * )
     */
    public function verifyme(Request $otpVerificationStoreRequest)
    {

        $validatedData = $otpVerificationStoreRequest->validate([
            'email' => 'required_without_all:phone|string|email',
            'phone' => 'required_without_all:email|numeric',
        ]);
        try {
            // $user = User::where(['email' => $otpVerificationStoreRequest->email])->first();
            if ($otpVerificationStoreRequest->has('email')) {
                $user = $this->userRepository->findByEmail($otpVerificationStoreRequest->email);
            }
            if ($otpVerificationStoreRequest->has('phone')) {
                $user = $this->userRepository->findByPhone($otpVerificationStoreRequest->phone);
            }
            $data = [
                'user_id' => $user->id,
                'otp' => $otpVerificationStoreRequest->otp,
                'status' => false,
                'reset_token' => uniqid(base64_encode(Str::random(60))),
            ];

            // if (Carbon::parse($user->otp_expires_at)->isPast()) {
            //     return response()->json(['message' => 'OTP expired'], 422);
            // }
            $otpVerificationStoreRequestData = new Request($data);
            $otpVerify = $this->otpService->update($otpVerificationStoreRequestData);

            $verificationCodeData = $this->verificationCode->where([
                'user_id' => $user->id,
                'otp' => $otpVerificationStoreRequest->otp,
            ])->first();

            if (!$otpVerify) {
                throw new OtpVerificationException('OTP code you have provided is not valid');
            }

            if ($otpVerificationStoreRequest->has('email')) {
                $user = $user->update([
                    'email_verified_at' => Carbon::now(),
                ]);
            }
            if ($otpVerificationStoreRequest->has('phone')) {
                $user = $user->update([
                    'phone_verify_at' => Carbon::now(),
                ]);
            }
        } catch (OtpVerificationException $e) {
            $message = $otpVerificationStoreRequest->phone . ' is facing problem in verify me from mobile ';
            Log::error($message . ' error is ' . $e->getMessage());

            return response(['message' => $e->getMessage(), 'success' => false], 400);
        } catch (Exception $e) {
            $message = $otpVerificationStoreRequest->phone . ' is facing problem in verify me from mobile ';
            Log::error($message . ' error is ' . $e->getMessage());

            return response(['message' => $e->getMessage(), 'success' => false], 400);
        }

        return response(['data' => [
            'reset_token' => $verificationCodeData->reset_token,
        ],
            'message' => 'Successfully Verify OTP',
            'success' => true,
        ]);
    }

    public function checkOtpVerify(Request $otpVerificationStoreRequest)
    {
        try {
            $user = Auth::user();
            if (is_null($user->email_verified_at)) {
                throw new Exception('OTP code is not verify');
            }
        } catch (OtpVerificationException $e) {
            return response(['message' => $e->getMessage(), 'success' => false], 400);
        } catch (Exception $e) {
            return response(['message' => $e->getMessage(), 'success' => false], 400);
        }

        return response(['message' => 'user is verifyed', 'success' => true]);
    }

    /**
     *  @OA\Post(
     *     path="/api/v2/verification/resendotp",
     *     summary="Api end point to verify OTP along with phone",
     *     tags={"Otp Verification"},
     *     security={{"bearer":{}}},
     *      @OA\Parameter(
     *         name="phone",
     *         in="query",
     *         description="phone number where you have send otp ",
     *         required=false,
     *         @OA\Schema(type="string"),
     *        ),
     *      @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="email address where you have send otp ",
     *         required=false,
     *         @OA\Schema(type="string"),
     *        ),
     *     @OA\Response(
     *        response="204",
     *        description="Successful response",
     *         @OA\JsonContent(
     *          )
     *     ),
     * )
     */
    public function resendOtp(Request $request)
    {

        $validatedData = $request->validate([
            'email' => 'required_without_all:phone|string|email',
            'phone' => 'required_without_all:email|numeric',
        ]);
        try {
            if ($request->has('email')) {
                $user = $this->userRepository->findByEmail($request->email);
            }
            if ($request->has('phone')) {
                $user = $this->userRepository->findByPhone($request->phone);
            }

            if (Auth::check()) {
                $email = Auth::user()->email;
            } else {
                $email = $user->email;
            }

            $otpUser = $this->verificationCode
                // ->where('email', $email)
                ->where('user_id', $user->id)
                ->where('reset_token', null)
                ->first();
            if (!$otpUser) {
                return $this->errorResponse('Something Went Wrong', 400);
            }

            $now = now();
            $expireAt = Carbon::parse($otpUser->expire_at);

            if ($otpUser->expire_at === null || $expireAt <= $now) {
                $this->generateAndResendOtp($otpUser);

                return $this->successResponse('OTP resend success');
            } else {
                return $this->errorResponse('Your OTP has not expired yet', 400);
            }
        } catch (Exception $e) {
            return $this->errorResponse('An error occurred while processing your request', 500);
        }
    }

    private function generateAndResendOtp($otpUser)
    {
        $otpUser->otp = mt_rand(111111, 999999);
        $otpUser->expire_at = now()->addMinute(2);
        $otpUser->update();
        OtpJob::dispatch($otpUser);
    }

    private function successResponse($message = 'Success', $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
        ], $statusCode);
    }

    private function errorResponse($message, $statusCode)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $statusCode);
    }
}
