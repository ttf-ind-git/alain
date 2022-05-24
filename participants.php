<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class participants extends Model
{
    use HasFactory;

     protected $table = 'scratch_form';

     protected $fillable = [
        'first_name', 'last_name', 'email', 'store_id', 'store' ,'receipt_number'
    ];

    // public function documents()
    // {
    //     return $this->hasMany(Document::class,"employee_id");
    // }

    public function documents()
    {
        return $this->hasMany(documents::class,"scratch_form_id");
    }

    public function result()
    {
        return $this->hasMany(result::class,"scratch_form_id");
    }

    public function store()
    {
        return $this->hasMany(store::class,"id","store_id");
    }

}
