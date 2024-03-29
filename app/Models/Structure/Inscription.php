<?php

namespace App\Models\Structure;

use App\ApiRequest\ApiRequestConsumer;
use App\Models\Authorization\Role;
use App\Models\Authorization\RolesUser;
use App\Notifications\ValidationInscription;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Auth;

class Inscription extends Authenticatable implements MustVerifyEmail
{
    use SoftDeletes, Notifiable, \Laravel\Sanctum\HasApiTokens, ApiRequestConsumer;
    protected $table = 'inscription';
    protected $fillable = [
        'prenom', 'nom', 'date_naissance', 'lieu_naissance',
        'identifiant', 'telephone', 'photo', 'sexe',
        'inscription', 'email', 'email_verified_at', 'password'
    ];
    protected $appends = ['nom_complet'];
    protected $hidden = ['password'];

    public function inscription()
    {
        return $this->belongsTo(Inscription::class, 'inscription');
    }

    // protected function getPhotoAttribute($value)
    // {
    //     return env('IMAGE_PREFIX_URL') . '/storage/' . $value;
    // }

    public function getPhotoAttribute()
    {
        if ($this->attributes['photo']) {
            $document_scanne = "http://127.0.0.1:8000/storage/" . $this->attributes['photo'];
            return $document_scanne;
        }
        return 0;
    }

    public function estDansStructures()
    {
        return $this->belongsToMany(Structure::class, AffectationStructure::class, 'user', 'structure');
    }

    public function structures()
    {
        return $this->hasMany(Structure::class, 'inscription');
    }

    public function affectation_structure()
    {
        return $this->hasOne(AffectationStructure::class, 'user');
    }

    public function affectation_structures()
    {
        return $this->hasMany(AffectationStructure::class, 'user');
    }

    public function getNomCompletAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function isResponsableStructure()
    {
        return $this->belongsToMany(Structure::class, ResponsableStructure::class, 'responsable', 'structure');
    }

    public function isEmployeStructures()
    {
        return $this->belongsToMany(Structure::class, AffectationStructure::class, 'user', 'structure');
    }

    public function cr_taches()
    {
        return $this->hasMany(\App\Models\Courrier\Crtache::class, 'inscription_id');
    }

    public function tache_linkeds()
    {
        return $this->hasMany(\App\Models\Courrier\Crtache::class, 'inscription_id')->where(function($query){
            $query->whereHas('responsables', function($query){
                $query->where('inscription.id', Auth::id() );
            });
            $query->orWhereHas('structures._employes', function($query){
                $query->where('inscription.id', Auth::id() );
            });
        })
        ->whereNull('cr_tache.archived_at')
        ->orderBy('cr_tache.updated_at', 'desc')->orderBy('cr_tache.date_limit', 'desc');
    }

    public function cr_taches_collab()
	{
        return $this->belongsToMany(\App\Models\Courrier\Crtache::class, 'cr_affectation_tache_personne', 'personne', 'tache');
	}

    public function roles()
    {
        return $this->belongsToMany(Role::class, AffectationStructure::class, 'user', 'role');
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new ValidationInscription($this->inscription()->first()));
    }
}
