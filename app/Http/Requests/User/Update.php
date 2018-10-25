<?php

namespace App\Http\Requests\User;
use Illuminate\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
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
    public function rules(Request $request)
    {
        if ($request->has('password') && $request->password != null) {
            return [
                'name'            => 'required|max:100|min:2',
                'email'           => 'required|max:100|min:2|unique:users,email, ' . $request->id,
                'password'        => 'string|min:6|confirmed',
            ];
        } else {
            return [
                'name'            => 'required|max:100|min:2',
                'email'           => 'required|max:100|min:2|unique:users,email, ' . $request->id,
            ];
        }
    }
}
