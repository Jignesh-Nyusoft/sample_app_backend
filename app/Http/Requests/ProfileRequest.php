<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
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
        return [
            'first_name'       => ['required', 'string', 'max:255'],
            'email'            => [
                'required',
                'string',
                'email',
                'max:255',
                'lowercase',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'last_name'        => ['required', 'string', 'min:2', 'max:190'],
                'mobile'           => [
                'required',
                'numeric',
                'min:9',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        
            'country_code'     => ['required', 'string', 'max:5'],
            'password'         => ['nullable', 'string', 'max:15'], 
            'confirm_password' => ['required_if:password,!=,null', 'same:password'], 
        
        ];
    }
    
}
