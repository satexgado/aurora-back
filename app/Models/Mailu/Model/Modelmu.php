<?php

namespace App\Models\Mailu\Model;

use Illuminate\Database\Eloquent\Model;

class Modelmu extends Model
{
    protected $table = 'im_modele';
    protected $guarded = ['id'];

      public function categorie()
    {
        return $this->hasOne('App\Models\Mailu\Model\Categmodelmu','id','categorie');
    } 

     public function inscription() 
    {
        return $this->hasOne('App\Models\Inscription','id','inscription');
    }
}
