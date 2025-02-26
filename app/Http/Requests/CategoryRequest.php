<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'name'            => 'required|min:3|max:190',
            'status'          => 'required',
            'image'           => 'mimes:png,jpg,jpeg|max:2048',
        ];
    }

    public function messages()
    {
        return [
        'name.min'               => 'Required Minimum character is 3',
        'name.max'               => 'Max character is 190',
        'status.required'        => 'Please select a status',            
        'image.mimes'            => 'Brand image must be a file of type: png, jpg, jpeg.',
        'image.max'              => 'Brand image size may not be greater than 2 MB.',
    ];
    }
}
