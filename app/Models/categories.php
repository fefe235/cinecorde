<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class categories extends Model
{protected $primaryKey = 'id_cat'; // clé primaire personnalisée
    protected $fillable = ['title_cat'];
    public function movies()
    {
        return $this->belongsToMany(
            movies::class,      // Modèle cible
            'category_movie',   // Table pivot
            'id_cat',           // Clé étrangère vers categories dans la table pivot
            'id_movie' 
                     // Clé étrangère vers movies dans la table pivot
        );
    }

}
