<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class prewinner extends Model
{
    use HasFactory;

    protected $table = 'prewinner';

    protected $fillable = [
        'receipt_no', 'gift_id'
    ];

   public function gift()
    {
        return $this->hasMany(gift::class,"id","gift_id");
    }

}
