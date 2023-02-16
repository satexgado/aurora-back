<?php

namespace App\Models\Labcolab;

use Illuminate\Database\Eloquent\Model;

class Observfichier extends Model
{
    protected $table = 'aur_observateur_fichier';
    protected $guarded = ['id'];
     

      public function fichier()
    {
        return $this->hasOne('App\Models\Labcolab\Extendfilemodel','id','fichier');
    }

    public function participant() 
    {
        return $this->hasOne('App\Models\Inscription','id','participant');
    }

     public function inscription() 
    {
        return $this->hasOne('App\Models\Inscription','id','inscription');
    }

}
