<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class movies extends Model
{
    protected $table = 'movies';            // Nom de la table (optionnel si conforme)
    protected $primaryKey = 'id_movie';     // ✅ Clé primaire personnalisée
    public $incrementing = true;            // Si c’est un AUTO_INCREMENT (default true)
    protected $keyType = 'int'; 
    protected $fillable = ['tmdb_id','movie_title','slug', 'synopsis', 'year','casting','image','trailler','avg_note','id_cat'];

    public function categories(){
        return $this->belongsTo(categories::class);
    }

    public function critiques()
{
    return $this->hasMany(critiques::class, 'id_movie', 'id_movie');
}

}
