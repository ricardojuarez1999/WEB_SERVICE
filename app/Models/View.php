<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['useridentityUser','count','date'];

    public function user(){
        return $this->belongsTo('App\User','useridentityUser');
    }
}
