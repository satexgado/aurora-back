<?php

namespace App\Models\Mailu\Fichier;

use Illuminate\Database\Eloquent\Model;

class Fichiermu extends Model
{
    protected $table = 'im_fichier';
    protected $guarded = ['id'];

      public function categorie()
    {
        return $this->hasOne('App\Models\Mailu\Model\Categmodelmu','id','categorie');
    } 

      public function type()
    {
        return $this->hasOne('App\Models\Ged\FichierType','id','type');
    } 

     public function inscription() 
    {
        return $this->hasOne('App\Models\Inscription','id','inscription');
    }
}
