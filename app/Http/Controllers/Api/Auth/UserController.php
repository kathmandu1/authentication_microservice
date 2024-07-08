<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\VerificationCode;
use App\Repositories\UserRepository;
use App\Services\Otp\OtpService as OtpOtpService;
use App\Services\SMS\OTPService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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

    //
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required_without_all:phone|string|email',
                'password' => 'required|string',
                'phone' => 'required_without_all:email|numeric',
            ]);

            $credentials = $request->only('email', 'phone', 'password');
            if (!Auth::attempt($credentials)) {
                throw new Exception('Email or password not match');
            }

            $user = $request->user();
            $token = $user->createToken('Personal Access Token')->accessToken;

            return response()->json([
                'token' => $token,
                'user' => new UserResource($user),
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    public function register(Request $request)
    {
        try {
            if ($request->phone) {
                $request->validate([
                    'name' => 'required|string',
                    'phone' => 'required|string|unique:users',
                    'password' => 'required|string|same:confirm_password',
                    'dob' => 'nullable|date',
                ]);
            } else {
                $request->validate([
                    'name' => 'required|string',
                    'email' => 'required|string|email|unique:users',
                    'password' => 'required|string|same:confirm_password',
                    'dob' => 'nullable|date',
                ]);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'dob' => $request->dob,
                'user_source' => 'Api',
            ]);

            $user->assignRole('user');

            $otp = new VerificationCode();
            $otp->otp = mt_rand(100000, 999999);
            $otp->email = $request->email;
            $otp->phone = $request->phone;
            $otp->user_id = $user->id;
            $otp->save();

            $token = $user->createToken('Personal Access Token')->accessToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
            ], 201);
            // return (new UserResource($user))
            // ->response()
            // ->setStatusCode(201);
        } catch (Exception $e) {
            $message = $request->name . ' is facing problem in registeration from mobile ';
            Log::error($message . ' error is ' . $e->getMessage());

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->token()->revoke();

            return response()->json(['message' => 'Successfully logged out']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function forgetPassword(Request $request, OTPService $otpService)
    {

        $validatedData = $request->validate([
            'email' => 'required|string|email',
            'phone' => 'regex:/^\d{10}$/|min:10',
        ]);

        // Generate OTP
        $otp = rand(100000, 999999);

        $user = $this->userRepository->findByEmail($validatedData['email']);
        if (!$user) {
            return response()->json(['error' => 'Email not found'], 404);
        }
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(5);
        $user->save();

        $this->otpOtpServiceVerification->store([
            'email' => $request->email,
            'user_id' => $user->id,
            'otp' => $otp,
            'expire_at' => Carbon::now()->addMinutes(5),
            'phone' => $request->phone,

        ]);

        $otpService->sendOtp($user->phone, $otp);

        return response()->json(['success' => true]);
    }

    public function resetPassword(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            // 'otp' => 'required|numeric',
            'password' => 'required|string|same:confirm_password',
        ]);

        $user = $this->userRepository->findByEmail($validatedData['email']);
        // if (!$user) {
        //     return response()->json(['error' => 'Email not found'], 404);
        // }
        // //if expired
        // if (Carbon::parse($user->otp_expires_at)->isPast()) {
        //     return response()->json(['error' => 'OTP expired'], 422);
        // }
        // //if otp not match
        // if (!$user || $user->otp !== $validatedData['otp']) {
        //     return response()->json(['error' => 'Invalid OTP'], 422);
        // }

        // Update password
        $user->password = Hash::make($validatedData['password']);
        $user->otp = null;
        $user->otp_expires_at = now();
        $user->save();

        return response()->json(['success' => true]);
    }

    public function user(Request $request)
    {
        $user = $this->userRepository->where('id', $request->user()->id)->first();
        $user->defaultShippingAddress = $request->user()->defaultShippingAddresses()->first();

        return response()->json($user);
        // return response()->json($request->user());
    }

    public function getAllUsers(Request $request)
    {
        $users = User::all();

        return response()->json($users);
    }

    public function delete(Request $request)
    {
        try {
            // auth()->user()->delete();
        } catch (Exception $e) {
            return response(['message' => 'something error while deleting user']);
        }

        return response(204);
    }

    /**
     * Social Login.
     */
    public function socialLogin(Request $request)
    {
        // dd($request->all());
        $provider = 'google'; // or $request->input('provider_name') for multiple providers
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
