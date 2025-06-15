<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class critiques extends Model
{
    
    protected $table = 'critiques';

    protected $primaryKey = 'id_critique'; // ← indique la vraie clé primaire

    public $incrementing = true; // si c'est un entier auto-incrémenté
    protected $keyType = 'int';  // ou 'string' si c'est un UUID
    protected $fillable = ['note','critique','id_movie','id_user','nbr_like'];
 
    public function likes()
{
    return $this->hasMany(Like::class, 'critique_id', 'id_critique');

}
public function user()
{
    return $this->belongsTo(User::class, 'id_user', 'user_id'); // adapte les colonnes si besoin
}
}
