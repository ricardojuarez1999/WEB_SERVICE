<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = ["useridentityUser","idImage","description",'date'];

    public function user(){
        return $this->belongsTo('App\User','useridentityUser');
    }
    public function idImage(){
        return $this->belongsTo('App\Image','id');
    }
}
