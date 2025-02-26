<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{

use HasFactory;
protected $guarded = ['id'];


public function Order(){
    
return $this->belongsTo(Order::class,);

}


public function getPaymentMethodAttribute($value)
{
    return ucwords(strtolower($value));
}

}
