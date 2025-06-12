<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class critiques extends Model
{
    
    protected $table = 'critiques';
    protected $fillable = ['note','critique','id_movie','id_user','nbr_like'];
}
