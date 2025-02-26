<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
 


    protected function minAmount(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => intval($value, ) ,
        );
    }


    protected function discountAmount(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => intval($value, ) ,
        );
    }
}
