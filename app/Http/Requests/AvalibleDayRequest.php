<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AvalibleDayRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
//                return [
//
//                ];
            case 'PUT':
            case 'PATCH':
//                $user = $this->route()->user;
//                return [
//                    'first_name' => 'required|max:100',
//                    'email' => 'email|unique:users,email,'.$user->id,
//                    'password' => 'nullable|confirmed|min:6',
//                ];
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

        ];
    }
}
