<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;

class ColorRequest extends FormRequest
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
            'color_name'      => 'required|string|min:1|max:190|unique:colors,name,'.$this->id,
            'color_code'      => 'required|string|min:3|max:125',
            'status'          => 'required'
        ];
    }

public function messages()
{
    return [
    'color_name.required'          => 'Color name is required',
    'color_name.min'               => 'Required Minimum charchter is 3',
    'color_code.required'          => 'Color code is required',
    'color_code.min'               => 'Required Minimum charchter is 3', 
    'color_name.unique'            => 'Requested Color name already Taken!', 
    'status.required'              => 'Please select a status',            
 ];
}
}
