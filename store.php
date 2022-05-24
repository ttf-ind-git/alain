<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class store extends Model
{
    use HasFactory;

     protected $table = 'store';

     protected $fillable = [
       'name', 'location', 'address'
   	 ];

   	  public function participant()
	   {
	      // return $this->belongsTo('App\Employee','employee_id');
	       return $this->belongsTo(participants::class,"store_id");
	   }
}
