<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = ['user_id', 'critique_id'];
    public function critique()
{
    return $this->belongsTo(Critiques::class, 'critique_id');
}
}
