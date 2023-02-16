<?php

namespace App\Models\Labcolab;

use Illuminate\Database\Eloquent\Model;

class Extendfilemodel extends Model
{
    protected $table = 'aur_fichier_text';
    protected $guarded = ['id'];

      public function modele()
    {
        return $this->hasOne('App\Models\Labcolab\Fichiermodel','id','modele');
    } 

    public function observateurs()
    {
        return $this->hasMany('App\Models\Labcolab\Observfichier','fichier');
    }

     public function inscription() 
    {
        return $this->hasOne('App\Models\Inscription','id','inscription');
    }
}
