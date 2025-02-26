<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    

    public function getDataAttribute($value)
    {
        try {
            return json_decode($value, true);
        } catch (\Throwable $th) {
            return null;
        }
        
    }
 

    public function getIdAttribute($value)
    {
        try {
            return $value;
        } catch (\Throwable $th) {
            return null;
        }
        
    }

}
