<?php

namespace App\Http\Requests;

// use App\Rules\PasswordMatch;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ChangePasswordRequest
 * @package App\Http\Requests
 */
class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        // return [
        //     'old_password' => ['required'],
        //     'new_password' => 'required|min:8|confirmed'
        // ];
        return [
            'old_password' => 'required',
        ];
    }

    // /* Custom message for validation
    // * @return array
    // */
    // public function messages()
    // {
    //     return [
    //         'old_password.required' => 'Please enter Current Password',
    //         'new_password.required' => 'Please enter New Password'
    //     ];
    // }
}
