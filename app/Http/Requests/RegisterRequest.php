<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'first_name'       => 'required|string|min:2|max:190',
            'last_name'        => 'required|string|min:2|max:190',
            'mobile'           => 'required|numeric|min:9|unique:users,mobile',
            'email'            => 'required|email|unique:users,email',
            'username'            => 'required|unique:users,username',
            'zip_code'         => 'required|string|min:2|max:15',
            'country_code'     => 'required|string|max:5',
            'is_terms_agreed'  => 'nullable|in:1,0',
            'password'         => 'required|string|min:8|max:15',
            'is_business_profile' => 'nullable|in:1,0',
            'business_name'    => 'required_if:is_business_profile,1', 
            'business_email'   => 'required_if:is_business_profile,1', 
            'business_phone'   => 'required_if:is_business_profile,1', 
            'business_country_code'    => 'required_if:is_business_profile,1',  
            'business_full_address'    => 'required_if:is_business_profile,1', 
            'business_zip_code'    => 'required_if:is_business_profile,1', 
            'business_country'     => 'required_if:is_business_profile,1', 
            'business_state'       => 'required_if:is_business_profile,1', 
            'business_city'        => 'required_if:is_business_profile,1', 
            'business_location_lat'     => 'required_if:is_business_profile,1', 
            'business_location_long'    => 'required_if:is_business_profile,1', 
            
        ];
    }
    

    public function messages()
    {
        return [
            'first_name.required'    => 'First Name is required.',
            'first_name.min'         => 'First Name must have at least 2 characters.',
            'first_name.max'         => 'First Name can have a maximum of 190 characters.',
            
            'last_name.required'     => 'Last Name is required.',
            'last_name.min'          => 'Last Name must have at least 2 characters.',
            'last_name.max'          => 'Last Name can have a maximum of 190 characters.',
            
            'zip_code.required'      => 'Zip Code is required.',
            'zip_code.min'           => 'Zip Code must have at least 2 characters.',
            'zip_code.max'           => 'Zip Code can have a maximum of 15 characters.',
            
            'mobile.required'        => 'Mobile Number is required.',
            'mobile.numeric'         => 'Mobile Number must contain only numeric values.',
            'mobile.min'             => 'Mobile Number must have at least 9 digits.',
            'mobile.unique'          => 'This mobile number already exists. Please use another.',
            
            'email.required'         => 'Email is required.',
            'email.email'            => 'Please provide a valid email address.',
            'email.unique'           => 'This email is already registered. Please use another.',
            
            'country_code.required'  => 'Country Code is required.',
            'country_code.max'       => 'Country Code can have a maximum of 5 characters.',
            
            'is_terms_agreed.in'     => 'The terms agreement value must be either 1 (agreed) or 0 (not agreed).',
            
            'password.required'      => 'Password is required.',
            'password.min'           => 'Password must be at least 8 characters long.',
            'password.max'           => 'Password can have a maximum of 15 characters.',
            'password.confirmed'     => 'The password confirmation does not match.',
            
            'confirm_password.required_with' => 'Confirm Password is required when the password is provided.',
            'confirm_password.same'          => 'Confirm Password must match the Password.',
        
        
        
            'is_business_profile.in' => 'The :attribute must be either 1 or 0.',
            'business_name.required_if' => 'The business name is required when the profile is not a business profile.',
            'business_email.required_if' => 'The business email is required when the profile is not a business profile.',
            'business_phone.required_if' => 'The business phone number is required when the profile is not a business profile.',
            'business_country_code.required_if' => 'The business country code is required when the profile is not a business profile.',
            'business_full_address.required_if' => 'The business full address is required when the profile is not a business profile.',
            'business_zip_code.required_if' => 'The business zip code is required when the profile is not a business profile.',
            'business_country.required_if' => 'The business country is required when the profile is not a business profile.',
            'business_state.required_if' => 'The business state is required when the profile is not a business profile.',
            'business_city.required_if' => 'The business city is required when the profile is not a business profile.',
            'business_location_lat.required_if' => 'The business location latitude is required when the profile is not a business profile.',
            'business_location_long.required_if' => 'The business location longitude is required when the profile is not a business profile.',
        ];
    }
    


    public function bodyParameters(): array
    {
        return [
            'first_name' => [
                'description' => 'The first name of the user.',
                'example' => 'John',
            ],
            'last_name' => [
                'description' => 'The last name of the user.',
                'example' => 'Doe',
            ],
            'mobile' => [
                'description' => 'The mobile phone number of the user.',
                'example' => '1234567890',
            ],
            'email' => [
                'description' => 'The email address of the user.',
                'example' => 'john.doe@example.com',
            ],
            'zip_code' => [
                'description' => 'The zip code of the user\'s address.',
                'example' => '12345',
            ],
            'country_code' => [
                'description' => 'The country code of the user\'s address.',
                'example' => 'US',
            ],
        ];
    }

}

