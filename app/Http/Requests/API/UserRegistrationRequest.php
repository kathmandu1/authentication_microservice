<?php

namespace App\Http\Requests\API;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRegistrationRequest extends FormRequest
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
            'name' => 'required|string',
            'confirm_password' => 'required|min:8',
            'password' => 'required|string|min:8|same:confirm_password',
            'email' => 'required|string|email|unique:users',
        ];

        $email_registration = [
            'email' => 'required|string|email|unique:users',
        ];

        $phone_registration = [
            'phone' => 'required|string|unique:users',
        ];

        if (!isset(request()->phone) && !isset(request()->email)) {
            $validator = array_merge($validator, $email_registration);
            $validator = array_merge($validator, $phone_registration);
        } elseif (!isset(request()->phone)) {
            $validator = array_merge($validator, $email_registration);
        } elseif (!isset(request()->email)) {
            $validator = array_merge($validator, $phone_registration);
        } else {
            $validator = array_merge($validator, $phone_registration);
        }

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
            'name.required' => 'Please provide name',
            'phone.required' => 'Please provide phone',
            'email.required' => 'Please provide email',
            'password.required' => 'Please provide password',
            'password.min:8' => 'Length password must be at least 8 character',
        ];
    }

    //@todo use localization later
    // public function messages()
    // {
    //     return [
    //         'name.required' => __('registration.name_required'),
    //         'phone.required' => __('api.registration.required', ['thing' => 'phone']),
    //         'email.required' => __('api.registration.required', ['thing' => 'email']),
    //         'password.required' => __('api.registration.password', ['thing' => 'password']),
    //         'password.min:8' => __('api.registration.password.min:8')
    //     ];
    // }
}
