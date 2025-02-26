<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


    public function Product(){
        
    return $this->hasOne(Product::class,'id','product_id')->with('size');
   
    } 
   


}
