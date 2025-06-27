<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_id',
        'to_id',
        'message',
    ];

    // Relation : message envoyé par un utilisateur
    public function sender()
    {
        return $this->belongsTo(User::class, 'from_id');
    }

    // Relation : message reçu par un utilisateur
    public function receiver()
    {
        return $this->belongsTo(User::class, 'to_id');
    }
}
