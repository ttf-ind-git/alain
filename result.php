<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class result extends Model
{
    use HasFactory;

    protected $table = 'result';

   public function participants()
   {
      // return $this->belongsTo('App\Employee','employee_id');
       return $this->belongsTo(participants::class,"id");
   }
}
