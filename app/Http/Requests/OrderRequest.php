<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'address_id'       => 'required',
            'net_amount'       => 'required',
            'total_amount'     => 'required',
            'shipping_charges' => 'required',
            'service_fee'      => 'required',
            'stripe_platform_fee'  => 'required',
            'sales_tax_amount'     => 'required',
            'product_id'       => 'required|exists:products,id',
            //'coupon_id'    => 'nullable|exists:coupons,id',
            'discount_price'   => 'nullable|required_if:coupon_id,id',
            'courier_partner_id' => 'required|exists:courier_partners,id'
        ];
    }
    
    
    public function messages(): array
    {
        return [
            'address_id.required'        => 'The address ID is required.',
            'product_id.required'        => 'The product ID is required.',
            'product_id.exists'          => 'The selected product ID does not exist.',
            'coupon_id.exists'           => 'The selected coupon ID does not exist.',
            'discount_price.required_if' => 'The discount price is required when a valid coupon is applied.',
        ];
    }
    
}
