<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class documents extends Model
{
    use HasFactory;
    
    protected $table = 'documents';


   public function participants()
   {
      // return $this->belongsTo('App\Employee','employee_id');
       return $this->belongsTo(participants::class,"id");
   }
}
