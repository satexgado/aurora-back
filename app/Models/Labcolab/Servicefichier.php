<?php

namespace App\Models\Labcolab;

use Illuminate\Database\Eloquent\Model;

class Servicefichier extends Model
{
    protected $table = 'aur_service_fichier';
    protected $guarded = ['id'];
     
     public function service()
    {
        return $this->hasOne('App\Models\Structure','id','service');
    }

      public function fichier()
    {
        return $this->hasOne('App\Models\Labcolab\Extendfilemodel','id','fichier');
    }

     public function inscription() 
    {
        return $this->hasOne('App\Models\Inscription','id','inscription');
    }
}
