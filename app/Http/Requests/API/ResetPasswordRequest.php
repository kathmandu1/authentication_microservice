<?php

namespace App\Http\Requests\API;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $validator = [
            // 'email' => 'required_without_all:phone|string|email',
            // 'phone' => 'required_without_all:email|numeric',
            // 'otp' => 'required|numeric|size:6',
            'confirm_password' => 'required|min:8',
            'password' => 'required|string|min:8|same:confirm_password',
            'reset_token' => 'required',
        ];

        // $email_registration = [
        //     'email' => 'required|string|email',
        // ];

        // $phone_registration = [
        //     'phone' => 'required|regex:/^\d{10}$/|size:10',
        // ];

        // if (!isset(request()->phone) && !isset(request()->email)) {
        //     $validator = array_merge($validator, $email_registration);
        //     $validator = array_merge($validator, $phone_registration);
        // } elseif (!isset(request()->phone)) {
        //     $validator = array_merge($validator, $email_registration);
        // } elseif (!isset(request()->email)) {
        //     $validator = array_merge($validator, $phone_registration);
        // } else {
        //     $validator = array_merge($validator, $phone_registration);
        // }

        return $validator;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'error' => 'Validation errors',
            'message' => $validator->errors()->first(),
            'errors' => $validator->errors(),
        ]));
    }

    public function messages()
    {
        return [
            'otp.required' => 'Please provide otp',
            'otp.numeric' => 'OTP must be a number',
            'otp.size:6' => 'OTP must be of six digit',
            'phone.required' => 'Please provide phone number',
            'email.required' => 'Please provide email',
            'password.required' => 'Please provide password',
            'password.min:8' => 'Length password must be at least 8 character',
            'password.same:confirm_password' => 'required|string|same:confirm_password',
        ];
    }
}
