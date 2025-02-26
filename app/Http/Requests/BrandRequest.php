<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'            => 'required|string|min:3|max:125|',
            'image'           => 'required|mimes:png,jpg,jpeg|min:1|max:2048',
            'status'          => 'required'
        ];
    }


    public function messages()
    {
        return [
        'name.min'               => 'Required Minimum character is 3',
        'name.max'               => 'Max character is 190', 
        'slug'                   => 'required|unique:colors',
        'status.required'        => 'Please select a status',            
        'image.required'         => 'Brand image file is required.',
        'image.mimes'            => 'Brand image must be a file of type: png, jpg, jpeg.',
        'image.max'              => 'Brand image size may not be greater than 2 MB.',
    ];
    }
}
