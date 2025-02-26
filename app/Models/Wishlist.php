<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;
 
   protected $guarded = ['id'];

   public function Product(){
        
    return $this->hasOne(Product::class, 'id','product_id')
    ->select('id', 'user_id', 'size_id', 'product_name','price','description','image', 'created_at')
    ->with(['seller:id,first_name,last_name', 'size:id,name']);
  
   }



}
