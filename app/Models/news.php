<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class news extends Model
{
    protected $table = 'news';            // Nom de la table (optionnel si conforme)
    protected $primaryKey = 'id_news';     // ✅ Clé primaire personnalisée
    public $incrementing = true;            // Si c’est un AUTO_INCREMENT (default true)
    protected $keyType = 'int';
    protected $fillable = ['title_news','news','image','release_date'];
}
