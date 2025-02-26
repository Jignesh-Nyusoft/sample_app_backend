<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'string', 'lowercase', 'email', 'max:255',Rule::unique(User::class)->ignore($this->user()->id)],
            'first_name'  => 'required|min:2|max:190',
            'last_name'   => 'required|min:2|max:190',
            'mobile'      => 'required|min:9|numeric',Rule::unique(User::class)->ignore($this->user()->id),
            'zip_code'    => 'required|min:2|max:15',
            'country_code'=> 'required|max:5',
        ];
    }
}
