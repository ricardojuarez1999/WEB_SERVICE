<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class View_dashboard extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'namePage',
        'total_views',
        'real_views',
        'date',
    ];
}
