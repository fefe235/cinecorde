<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{protected $primaryKey = 'id_cat'; // clé primaire personnalisée
    protected $fillable = ['title_cat'];
    public function movies()
    {
        return $this->belongsToMany(
            Movies::class,
            'category_movie',
            'id_cat',
            'id_movie'
        )->select('movies.id_movie', 'movies.movie_title', 'movies.slug', 'movies.image', 'movies.avg_note')
         ->distinct();
    }
    

}
