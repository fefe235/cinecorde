<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class movies extends Model
{
    protected $table = 'movies';
    protected $fillable = ['tmdb_id','movie_title','slug', 'synopsis','categorie_id', 'year','casting','image','trailler','avg_note','id_cat'];

    public function categorie(){
        return $this->belongsTo(categories::class);
    }
}
