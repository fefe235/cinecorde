<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class categories extends Model
{
    protected $fillable = ['id_cat','title_cat'];
    public function movies()
    {
        return $this->hasMany(movies::class);
    }
}
