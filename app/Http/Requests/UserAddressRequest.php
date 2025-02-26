<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Route;

class UserAddressRequest extends FormRequest
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
            'address_id' => [Rule::requiredIf(function () { 
                return Route::currentRouteName() == 'update-address'; 
            })], 
   
            'zip_code'      => 'required|numeric',
            'country'       => 'required|string|max:80',
            'state'         => 'required|string|max:80',
            'city'          => 'required|string|max:50',
            'address'       => 'max:255|nullable',
            'street'        => 'max:190|nullable',
            'is_default'    => 'required|in:1,0',
            'is_pickup'     => 'required|in:1,0',
            'location_lat'  => 'max:255|nullable',
            'location_long' => 'max:255|nullable',
        
        ];
    
    }

    public function messages(): array
    {
        return [
     
            'address_id.required' => 'The address ID is required when updating an address.',
            'zip_code.required'   => 'The zip code is required.',
            'zip_code.numeric'    => 'The zip code must be a number.',
            'country.required'    => 'The country is required.',
            'country.string'      => 'The country must be a valid string.',
            'country.max'         => 'The country name may not be greater than 80 characters.',
            'state.required'      => 'The state is required.',
            'state.string'        => 'The state must be a valid string.',
            'state.max'           => 'The state name may not be greater than 80 characters.',
            'city.required'       => 'The city is required.',
            'city.string'         => 'The city must be a valid string.',
            'city.max'            => 'The city name may not be greater than 50 characters.',
            'address.max'         => 'The address may not be greater than 255 characters.',
            'street.max'          => 'The street name may not be greater than 190 characters.',
            'is_default.required' => 'Please specify whether this Delivery address is the default .',
            'is_default.in'       => 'The default Delivery field must be either 1 or 0.',
            'is_pickup.required'  => 'Please specify whether this Delivery address is a pickup location.',
            'is_pickup.in'        => 'The pickup default Delivery field must be either 1 or 0.',
            'location_lat.max'    => 'The latitude may not be greater than 255 characters.',
            'location_long.max'   => 'The longitude may not be greater than 255 characters.',
        ];
    }
    
}
