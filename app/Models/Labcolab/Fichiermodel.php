<?php

namespace App\Models\Labcolab;

use Illuminate\Database\Eloquent\Model;

class Fichiermodel extends Model
{
    protected $table = 'aur_modele_fichier';
    protected $guarded = ['id'];
     

    public function extensions()
    {
        return $this->hasMany('App\Models\Labcolab\Extendfilemodel','modele');
    }
 
     public function inscription() 
    {
        return $this->hasOne('App\Models\Inscription','id','inscription');
    }
}
