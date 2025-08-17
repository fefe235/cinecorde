<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movies extends Model
{
    protected $table = 'movies';            // Nom de la table (optionnel si conforme)
    protected $primaryKey = 'id_movie';     // ✅ Clé primaire personnalisée
    public $incrementing = true;            // Si c’est un AUTO_INCREMENT (default true)
    protected $keyType = 'int'; 
    protected $fillable = ['tmdb_id','movie_title','slug', 'synopsis', 'year','casting','image','trailler','avg_note'];


    public function categories()
    {
        return $this->belongsToMany(
            Categories::class,    // classe exacte
            'category_movie',     // table pivot
            'id_movie',           // clé étrangère du modèle courant
            'id_cat'              // clé étrangère du modèle lié
        )
        ->select('categories.id_cat', 'categories.title_cat')
        ->distinct();
    }

    public function critiques()
{
    return $this->hasMany(Critiques::class, 'id_movie', 'id_movie');
}
public function favoritedBy()
{
    return $this->belongsToMany(User::class, 'favorites', 'movie_id', 'user_id')
                ->withTimestamps();
}

}
