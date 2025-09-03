<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CenterRequest extends FormRequest
{
    /**
     * Determine if the center is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        switch($this->method()) {
            case 'GET':
            case 'DELETE':
                return [];
            case 'POST':
                return [
                    'name' => 'required|max:100',
                    'email' => 'required|email|unique:centrs,email',
                ];
            case 'PUT':
            case 'PATCH':
                $center = $this->route()->center;
                return [
                    'name' => 'required|max:100',
                    'email' => 'email|unique:centrs,email,'.$center->id,
                    'password' => 'nullable|confirmed|min:6',
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
    public function messages(): array
    {
        return [
            'email.unique' => 'Email is already taken',
            'email.email' => 'Email should be a valid email address',
        ];
    }
}
