<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch($this->method()) {
            case 'GET':
            case 'DELETE':
                return [];
            case 'POST':
                return [
                    'first_name' => 'required|max:100',
                    'last_name' => 'required|max:100',
                    'email' => 'required|email|unique:admins',
                    'password' => 'required|min:6',
                ];
            case 'PUT':
            case 'PATCH':
            return [
                'first_name' => 'required|max:100',
                'last_name' => 'required|max:100',
                'email' => 'required|email',
                'password' => 'nullable|min:6',
            ];
            default:break;
        }
        return [];

    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'first_name.required' => 'A first_name with  is required',
            'last_name.required' => 'A last_name with  is required',
            'email.required'  => 'An email is required',
            'password.required'  => 'An password is required',

        ];
    }
}
