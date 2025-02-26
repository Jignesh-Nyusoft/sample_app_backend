<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Route;

class ProductRequest extends FormRequest
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
    
            'image' => [
                Rule::requiredIf(function () {
                    return Route::currentRouteName() == 'product-store'; 
                }),
                'mimes:png,jpg,jpeg',
                'max:3072'
            ],

            'product_name'       => 'required|string|max:190',
            'category_id'        => 'required|integer|exists:categories,id',
            //'default_pickup_id'=> 'required|integer|exists:addresses,id',
            'brand_id'           => 'required|integer|exists:brands,id',
            'size_id'            => 'required|integer|exists:sizes,id',
            'material_id'        => 'required|integer|exists:materials,id',
            'color_id'           => 'required|integer|exists:colors,id',
            'suitable_id'        => 'required|integer|exists:suitables,id',
            'description'        => 'required|string',
            'cloth_type'         => 'required|string|in:new,old',
            'condition_id'       => 'required_if:cloth_type,old|integer|exists:conditions,id',
            'stock'              => 'required|integer|min:1',
            'price'              => 'required|numeric|min:0',
            //'image'            => 'mimes:png,jpg,jpeg|max:2048',
            'product_images'     => 'nullable|array',
            'product_images.*'   => 'mimes:png,jpg,jpeg|max:3072',
            'status'             => 'required|string|in:active,inactive',
        ];
    }
    



    public function messages(): array
    {
        return [
            'product_id.required'   => 'Product Id is required.',
            'product_name.required' => 'Product name is required.',
            'product_name.max'      => 'Product name cannot exceed 190 characters.',
            
            'category_id.required' => 'Category is required.',
            'category_id.integer'  => 'Category must be a valid integer.',
            'category_id.exists'   => 'Selected category does not exist.',
            
            'brand_id.required' => 'Brand is required.',
            'brand_id.integer'  => 'Brand must be a valid integer.',
            'brand_id.exists'   => 'Selected brand does not exist.',
            
            'size_id.required' => 'Size is required.',
            'size_id.integer'  => 'Size must be a valid integer.',
            'size_id.exists'   => 'Selected size does not exist.',
            
            'material_id.required' => 'Material is required.',
            'material_id.integer'  => 'Material must be a valid integer.',
            'material_id.exists'   => 'Selected material does not exist.',
            
            'condition_id.required' => 'Condition is required.',
            'condition_id.integer'  => 'Condition must be a valid integer.',
            'condition_id.exists'   => 'Selected condition does not exist.',
            
            'color_id.required'     => 'Color is required.',
            'color_id.integer'      => 'Color must be a valid integer.',
            'color_id.exists'       => 'Selected color does not exist.',
            
            'suitable_id.required' => 'Suitability is required.',
            'suitable_id.integer'  => 'Suitability must be a valid integer.',
            'suitable_id.exists'   => 'Selected suitability does not exist.',
            
            'description.required' => 'Description is required.',
            
            'cloth_type.required' => 'Cloth type is required.',
            'cloth_type.in'       => 'Cloth type must be either new or old.',
            
            'stock.required' => 'Stock is required.',
            'stock.integer'  => 'Stock must be a valid integer.',
            
            'price.required' => 'Price is required.',
            
            'image.required' => 'An image is required.',
            'image.mimes'    => 'The image must be a file of type: png, jpg, jpeg.',
            
            'product_images.array'   => 'Product images must be an array.',
            'product_images.*.mimes' => 'Each product image must be a file of type: png, jpg, jpeg.',
            
            'status.required' => 'Status is required.',
            'status.in'       => 'Status must be either active or inactive.',
        ];
    }
    




}
