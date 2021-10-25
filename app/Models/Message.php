<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'idChat',
        'message',
        'sendUser',
        'image',
        'date'
    ];

    public function chat(){
        return $this->belongsTo('App\Chat','idChat');
    }

    public function user(){
        return $this->belongsTo('App\User','sendUser');
    }
}
