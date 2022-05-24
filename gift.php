<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class gift extends Model
{
    use HasFactory;

     protected $table = 'gift_details';

     protected $fillable = [
       'name', 'total_gifts'
   	 ];

   	  public function prewinner()
	   {
	      // return $this->belongsTo('App\Employee','employee_id');
	       return $this->belongsTo(prewinner::class,"gift_id");
	   }

}
