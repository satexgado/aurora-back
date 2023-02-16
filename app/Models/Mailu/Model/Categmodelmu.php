<?php

namespace App\Models\Mailu\Model;

use Illuminate\Database\Eloquent\Model;

class Categmodelmu extends Model
{
    protected $table = 'im_categorie_modele';
    protected $guarded = ['id'];
     
    public function modeles()
    {
        return $this->hasMany('App\Models\Mailu\Model\Modelmu','categorie');
    }
 
   
     public function inscription() 
    {
        return $this->hasOne('App\Models\Inscription','id','inscription');
    }
}
