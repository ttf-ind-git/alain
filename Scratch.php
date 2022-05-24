<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Scratch extends Model
{
    use HasFactory;

     protected $table = 'scratch_form';

     protected $fillable = [
       'first_name', 'last_name', 'store_id', 'receipt_number' ,'ip_address'
   	 ];
    

}
