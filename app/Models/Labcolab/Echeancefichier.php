<?php

namespace App\Models\Labcolab;

use Illuminate\Database\Eloquent\Model;

class Echeancefichier extends Model
{
    protected $table = 'aur_echeance_fichier_ext';
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
