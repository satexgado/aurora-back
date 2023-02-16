<?php

namespace App\Models\Labcolab;

use Illuminate\Database\Eloquent\Model;

class Intervenantfichier extends Model
{
    protected $table = 'aur_fichier_intervenant';
    protected $guarded = ['id'];
     

      public function fichier()
    {
        return $this->hasOne('App\Models\Labcolab\Extendfilemodel','id','fichier');
    }
     public function parent_id()
    {
        return $this->hasOne('App\Models\Labcolab\Intervenantfichier','id','parent_id');
    }

    public function enfants()
    {
        return $this->hasMany('App\Models\Labcolab\Intervenantfichier','parent_id');
    }

     public function inscription() 
    {
        return $this->hasOne('App\Models\Inscription','id','inscription');
    }
}
