<?php

namespace App\Models\Labcolab;

use Illuminate\Database\Eloquent\Model;

class Motclefichier extends Model
{
    protected $table = 'aur_mot_cles_fichiers';
    protected $guarded = ['id'];
     

      public function fichier()
    {
        return $this->hasOne('App\Models\Labcolab\Extendfilemodel','id','fichier');
    }

     public function inscription() 
    {
        return $this->hasOne('App\Models\Inscription','id','inscription');
    }
}
