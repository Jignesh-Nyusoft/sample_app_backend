<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplateModel extends Model
{
    use HasFactory;
    protected $table = "email_templates";


public function emailHeader(){
    
return $this->belongsTo(EmailTemplateHeader::class,'header_id');

}


public function emailFooter(){
    
return $this->belongsTo(EmailTemplateFooter::class,'footer_id');
    
    
}

}
