<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderShipment extends Model
{
    use HasFactory;   
    protected  $guarded = ['id'];

 
    
function Order(){
    
 return $this->belongsTo(Order::class,'order_d');
 
}

public function courierPartner()
{
    return $this->hasOne(CourierPartner::class, 'id', 'courier_partner_id');
}


}
