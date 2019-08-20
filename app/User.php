<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
#use Laravel\Passport\HasApiTokens;
use App\PerfilUsuario;

class User extends Authenticatable {
    
    #use HasApiTokens, Notifiable;
    use Notifiable;

    protected $table = "users";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'perfil_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];



    # BelongsTo
    public function perfil () {
        return $this->belongsTo(PerfilUsuario::class, 'perfil_id');
    }



    # HasMany

    
}
