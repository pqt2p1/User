<?php

namespace Pqt2p1\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordUserRequest extends BaseFormRequest
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
        return [
            'token' => 'required',
            'email' => 'required|email|max:255',
            'password' => 'required|min:8|confirmed|max:255',
        ];
    }
}
