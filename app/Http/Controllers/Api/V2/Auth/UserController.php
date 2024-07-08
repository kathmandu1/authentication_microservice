<?php

namespace App\Http\Controllers\Api\V2\Auth;

use App\Exceptions\OtpVerificationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\ResetPasswordRequest;
use App\Http\Requests\API\UserLoginRequest;
use App\Http\Requests\API\UserRegistrationRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\V2\Auth\UserResource as AuthUserResource;
use App\Models\User;
use App\Models\VerificationCode;
use App\Repositories\UserRepository;
use App\Services\Otp\OtpService as OtpOtpService;
use App\Services\SMS\OTPService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;

class UserController extends Controller
{
    public function __construct(
        protected UserRepository $userRepository,
        protected OtpOtpService $otpOtpService,
        protected VerificationCode $verificationCode,
        protected OtpOtpService $otpOtpServiceVerification
    ) {

    }

    /**
     * @OA\Post(
     *      path="/api/v2/auth/login",
     *      operationId="loginUser",
     *      tags={"Authentication"},
     *      summary="Login into system",
     *      description="Returns project data",
     *      @OA\Parameter(
     *         name="device-token",
     *         in="header",
     *         description="User device token get mobile app ",
     *         required=false,
     *         @OA\Schema(type="string")
     *        ),
     *      @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=false,
     *         @OA\Schema(type="string")
     *        ),
     *        @OA\Parameter(
     *         name="phone",
     *         in="query",
     *         description="User's phone if user want to login with phone",
     *         required=false,
     *         @OA\Schema(type="string")
     *        ),
     *       @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User's password",
     *         required=true,
     *         @OA\Schema(type="string")
     *        ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    // todo @binod please
    public function login(UserLoginRequest $request)
    {
        try {
            $deviceToken = $request->header('device-token');
            $credentials = $request->only('email', 'phone', 'password');
            if (!Auth::attempt($credentials)) {
                throw new Exception('Email or password not match');
            }

            $user = $request->user();
            $token = $user->createToken('Personal Access Token')->accessToken;

            if(!is_null($deviceToken)){
                $user->update(['device_token' => $deviceToken ]);
            }

            // $user->defaultShippingAddress = $request->user()->defaultShippingAddresses()->first();
            return response()->json([
                'data' => [
                    'token' => $token,
                    'user' => new AuthUserResource($user),
                ],
                'message' => 'user login successfully',
                'success' => true,
                // 'token' => $token,

            ]);


        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'success' => false], 401);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v2/auth/register",
     *     summary="Register a new user",
     *      operationId="registerUser",
     *     tags={"Authentication"},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="User's name",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *       @OA\Parameter(
     *         name="gender",
     *         in="query",
     *         description="User gender",
     *         required=true,
     *         @OA\Schema(type="string")
     *        ),
     *       @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User's password",
     *         required=true,
     *         @OA\Schema(type="string")
     *        ),
     *         @OA\Parameter(
     *         name="confirm_password",
     *         in="query",
     *         description="confirm password",
     *         required=true,
     *         @OA\Schema(type="string")
     *        ),
     *     @OA\Response(response="201", description="User registered successfully"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function register(UserRegistrationRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'user_source' => 'mobile',
            ]);

            $user->assignRole('user');

            $otp = new VerificationCode();
            $otp->otp = mt_rand(100000, 999999);
            $otp->email = $request->email;
            $otp->phone = $request->phone;
            $otp->user_id = $user->id;
            $now = now();
            $otp->expire_at = $now->addMinute(2);
            $otp->save();

            $token = $user->createToken('Personal Access Token')->accessToken;
            DB::commit();

            return response()->json([
                'data' => [
                    'token' => $token,
                ],

                // 'user' => $user,
                'message' => 'Signup successfully',
                'success' => true,
            ], 201);
            // return (new UserResource($user))
            // ->response()
            // ->setStatusCode(201);
        } catch (Exception $e) {
            DB::rollBack();
            $message = $request->usphoneer_id . ' is facing problem in register from mobile ';
            Log::error($message . ' error is ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage(), 'success' => false], 400);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->token()->revoke();

            return response()->json(['message' => 'Successfully logged out', 'success' => true]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function forgetPassword(Request $request, OTPService $otpService)
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required_without_all:phone|string|email',
                // 'phone' => 'regex:/^\d{10}$/|min:10',
                'phone' => 'required_without_all:email|numeric',
            ]);

            // Generate OTP
            $otp = rand(100000, 999999);

            if ($request->has('email')) {
                $user = $this->userRepository->findByEmail($validatedData['email']);
            }
            if ($request->has('phone')) {
                $user = $this->userRepository->findByPhone($validatedData['phone']);
            }

            if (!$user) {
                return response()->json(['message' => 'Email or phone not found', 'success' => false], 404);
            }
            $otpUser = $this->verificationCode
                // ->where('email', $email)
                ->where('user_id', $user->id)
                ->where('reset_token', null)
                ->latest()
                ->first();
            $now = now();

            if (!is_null($otpUser)) {
                $expireAt = Carbon::parse($otpUser->expire_at);

                if ($expireAt > $now) {
                    throw new OtpVerificationException('Otp already has been send', 410);
                }
            }
            // $expireAt = Carbon::parse($otpUser->expire_at);

            // if ($expireAt > $now) {
            //     throw new OtpVerificationException('Otp already has been send', 410);
            // }
            DB::beginTransaction();
            $user->otp = $otp;
            $user->otp_expires_at = now()->addMinute(2);
            $user->save();

            $verificationModel = $this->otpOtpServiceVerification->store([
                'email' => $request->email,
                'user_id' => $user->id,
                'otp' => $otp,
                'expire_at' => now()->addMinute(2),
                'phone' => $request->phone,

            ]);

            // if ($request->has('phone')) {
            //     $otpService->sendOtp($user->phone, $otp);
            // }
            // if ($request->has('email')) {
            //     Mail::to($user->email)->send(new OtpVerificationMail($verificationModel));
            // }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'successfully send reset password code']);
        } catch (OtpVerificationException $e) {
            DB::rollBack();

            return response(['message' => $e->getMessage(), 'success' => false], 410);
        } catch (Exception $e) {
            DB::rollBack();
            $message = $request->phone . ' is facing problem in forgot password from mobile ';
            Log::error($message . ' error is ' . $e->getMessage());

            return response()->json(['message' => 'something went wrong', 'success' => false], 400);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            // $validatedData = $request->validate([
            //     'email' => 'required|email',
            //     'otp' => auth('api')->check() ? '' : 'required|numeric',
            //     // 'otp' => 'required|numeric',
            //     'password' => 'required|string|same:confirm_password',
            // ]);

            $request->validate([
                'reset_token' => 'required',
                'password' => 'required|string|same:confirm_password',
            ]);

            $user_id = $this->verificationCode->where([
                'reset_token' => $request->reset_token,
            ])->first()->user_id;

            $user = $this->userRepository->where('id', $user_id)->first();

            // $user = $this->userRepository->findByEmail($validatedData['email']);
            if (!$user) {
                return response()->json(['message' => 'User not found or invalid token', 'success' => false], 404);
            }
            //if expired
            // if (Carbon::parse($user->otp_expires_at)->isPast()) {
            //     return response()->json(['message' => 'OTP expired'], 422);
            // }
            //if otp not match
            // if (!$user || $user->otp !== $validatedData['otp']) {
            //     return response()->json(['message' => 'Invalid OTP'], 422);
            // }

            // Update password
            $user->password = Hash::make($request->password);
            $user->otp = null;
            $user->otp_expires_at = now();
            $user->save();

            return response()->json(['success' => true, 'message' => 'successfully  reset password']);
        } catch (Exception $e) {
            $message = $request->reset_token . ' is facing problem in reset password from mobile ';
            Log::error($message . ' error is ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage(), 'success' => false], 400);
        }
    }

     /**
     * @OA\Get(
     *     path="/api/v2/auth/user",
     *     summary="Api endpoints for Get Detail of authorize user",
     *     tags={"Authentication"},
     *     security={{"bearer":{}}},
     *     @OA\Response(
     *        response="200",
     *        description="Successful response",
     *     ),
     * )
     */
    public function user(Request $request)
    {
        try {
            $user = $this->userRepository->where('id', $request->user()->id)->first();
            $user->defaultShippingAddress = $request->user()->defaultShippingAddresses()->first();

            return new AuthUserResource($user);
        } catch (Exception $e) {
        }

        // return response()->json($request->user());
    }


    public function delete(Request $request)
    {
        try {
            // auth()->user()->delete();
        } catch (Exception $e) {
            return response(['message' => 'something error while deleting user', 'success' => false], 400);
        }

        return response(204);
    }

    /**
     * @OA\Schema(
     *    schema="SocialLoginRequestSchema",
     *    @OA\Property(
     *        property="provider",
     *        type="string",
     *        description="User login provider like apple, google, facebook",
     *        nullable=false,
     *        format="string",
     *        example="google"
     *    ),
     *    @OA\Property(
     *        property="access_token",
     *        type="string",
     *        description="User access token generated by google,apple in mobile site",
     *        nullable=false,
     *        example="ya29.a0AfB_byC71GGiApEs5vzbmK7axOXRRVE1eKVuYZuS83tS7CxJBN1PQojAxhMcaKG0FPD1OwZzNoWXHsJ3qaPcGzZRvdu6eb9zYG80UDOKxLeJ9L91A10rGH-t-LTGYrjZhkWJu8gXWYs2of3muLRhOW75j92m9pRmdgaCgYKAbcSARASFQGOcNnCCaqX6K7b2dxa_hfqlhmivQ0169"
     *    ),
     * )
     *
     * @OA\Post(
     *      path="/api/v2/oauth",
     *      operationId="socialLoginUser",
     *      tags={"Authentication"},
     *      summary="Login into system by google,apple",
     *      description="Returns token",
     *      @OA\RequestBody(
     *            @OA\JsonContent(ref="#/components/schemas/SocialLoginRequestSchema")
     *       ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     **/
    public function socialLogin(Request $request)
    {
        // return $request->all();
        // dd($request->all());
        $provider = $request->provider; // or $request->input('provider_name') for multiple providers
        $token = $request->access_token;

        try {
            // get the provider's user. (In the provider server)
            $providerUser = Socialite::driver($provider)->stateless()->userFromToken($token);
            // check if access token exists etc..
            // search for a user in our server with the specified provider id and provider name

            // dd($providerUser);

            $user = User::where([
                'provider' => $provider,
                'provider_id' => $providerUser->id,
            ])->first();

            if (
                User::where('email', $providerUser->getEmail())->where('provider', '!=', $provider)->exists()
                ||
                User::where('email', $providerUser->getEmail())->whereNull('provider')->exists()
            ) {
                throw new Exception('This email is already register,please use password');
            }

            if (!$user) {
                $user = User::create([
                    'name' => $providerUser->name,
                    'email' => $providerUser->email,
                    'provider_token' => $providerUser->token,
                    'email_verified_at' => now(),
                    'provider' =>  $provider,
                    'provider_id' => $providerUser->id,

                ]);
            }

            Auth::login($user);

            $token = $user->createToken('Personal Access Token')->accessToken;

            $user = User::where('provider', $provider)->where('provider_id', $providerUser->id)->first();
            // // if there is no record with these data, create a new user
            // if ($user == null) {
            //     $user = User::create([
            //         'provider_name' => $provider,
            //         'provider_id' => $providerUser->id,
            //     ]);
            // }SS
            // create a token for the user, so they can login
            // $token = $user->createToken(env('APP_NAME'))->accessToken;
            // // return the token for usage
            // return response()->json([
            //     'success' => true,
            //     'token' => $token
            // ]);

            // $user = $request->user();
            // $token = $user->createToken('Personal Access Token')->accessToken;
        } catch (Exception $e) {
            return response(['error' => $e->getMessage(), 'success' => false], 500);
        }

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }
}
