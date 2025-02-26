<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductFilterRequest extends FormRequest
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
            'brand'        => 'array|nullable',      
            'brand.*'      => 'integer|exists:brands,id',
            'suitable'     => 'array|nullable',      
            'suitable.*'   => 'integer|exists:suitables,id',
            'category'     => 'array|nullable',      
            'category.*'   => 'integer|exists:categories,id', 
            'size'         => 'array|nullable',
            'size.*'       => 'integer|exists:sizes,id',
            'color'        => 'array|nullable',
            'color.*'      => 'integer|exists:colors,id',
            'condition'    => 'array|nullable',
            'condition.*'  => 'integer|exists:conditions,id',
            'material'     => 'array|nullable',
            'material.*'   => 'integer|exists:materials,id',
            'start_price'  => 'numeric|nullable|min:0', 
            'end_price'    => 'numeric|nullable|min:0|gte:start_price', 
            'sortby'       => 'string|in:Newest,low-to-high,high-to-low|nullable', 
        ];  
    
    }

    public function messages(): array
    {
        return [
            'brand.array'                  => 'The brand field must be an array.',
            'brand.*.integer'              => 'Each brand ID must be an integer.',
            'brand.*.exists'               => 'Each brand ID must exist in the brands table.',
    
            'suitable.array'               => 'The suitable field must be an array.',
            'suitable.*.integer'           => 'Each suitable ID must be an integer.',
            'suitable.*.exists'            => 'Each suitable ID must exist in the suitables table.',

            'category.array'               => 'The category field must be an array.',
            'category.*.integer'           => 'Each category ID must be an integer.',
            'category.*.exists'            => 'Each category ID must exist in the categories table.',
    
            'size.array'                   => 'The size field must be an array.',
            'size.*.integer'               => 'Each size ID must be an integer.',
            'size.*.exists'                => 'Each size ID must exist in the sizes table.',
    
            'color.array'                  => 'The color field must be an array.',
            'color.*.integer'              => 'Each color ID must be an integer.',
            'color.*.exists'               => 'Each color ID must exist in the colors table.',
    
            'condition.array'              => 'The condition field must be an array.',
            'condition.*.integer'          => 'Each condition ID must be an integer.',
            'condition.*.exists'           => 'Each condition ID must exist in the conditions table.',
    
            'material.array'               => 'The material field must be an array.',
            'material.*.integer'           => 'Each material ID must be an integer.',
            'material.*.exists'            => 'Each material ID must exist in the materials table.',
    
            'start_price.numeric'          => 'The start price must be a number.',
            'start_price.min'              => 'The start price must be at least 0.',
    
            'end_price.numeric'            => 'The end price must be a number.',
            'end_price.min'                => 'The end price must be at least 0.',
            'end_price.gte'                => 'The end price must be greater than or equal to the start price.',
    
            'sortby.string'                => 'The sortby field must be a string.',
            'sortby.in'                    => 'The sortby field must be one of the following values: Newest, low-to-high, high-to-low.',
        ];
    }
    

}
