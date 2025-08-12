<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */protected $primaryKey = 'user_id';
public $incrementing = true;
protected $keyType = 'int';
    protected $fillable = [
        'name',
        'email',
        'password',
        'nbr_like_total',
        'rank'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }
    public function favoriteMovies()
    {
        return $this->belongsToMany(Movies::class, 'favorites', 'user_id', 'movie_id');
    }
    
    public function critiques()
    {
        return $this->hasMany(Critiques::class, 'id_user', 'user_id');
    }
    

}
