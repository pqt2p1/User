<?php

namespace Pqt2p1\User\Http\Requests\RoleRequest;

use Illuminate\Foundation\Http\FormRequest;
use Pqt2p1\User\Http\Requests\BaseFormRequest;

class CreateRoleRequest extends BaseFormRequest
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
           
        ];
    }
}
