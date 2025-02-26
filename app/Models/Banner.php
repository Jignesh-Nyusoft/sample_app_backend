<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{

 use HasFactory;
 protected $guarded = ['id']; 

 protected $appends = ['banner_image'];

 public function getBannerImageAttribute()
 {
    if(!empty($this->image)){
        return url($this->image);
       }
   
       return null;
 }
}
