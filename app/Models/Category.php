<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    protected $guarded = ['id'];
     
    protected $appends = ['category_image'];

    public function getCategoryImageAttribute()
    {
      if(!empty($this->image)){
        return url($this->image);
       }
   
       return null;

    }


  public function SubCategory(){

    return $this->hasMany(Category::class,'parent_id','id')->select('id','parent_id','name','image');

  
  }




}
