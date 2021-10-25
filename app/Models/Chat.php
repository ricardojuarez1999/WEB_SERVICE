<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'user1',
        'user2',
        'status',
        'date'
    ];
    public function user1(){
        return $this->belongsTo('App\User','user1');
    }
    public function user2(){
        return $this->belongsTo('App\User','user2');
    }
}
