<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class schedule extends Model
{
    use HasFactory;

    protected $table = 'schedule';

     protected $fillable = [
       'date', 'prize', 'count'
   	 ];

    public function gift()
    {
        return $this->hasMany(gift::class,"id","prize");
    }
}
