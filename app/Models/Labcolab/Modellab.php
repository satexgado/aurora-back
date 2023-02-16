<?php

namespace App\Models\Labcolab;

use Illuminate\Database\Eloquent\Model;

class Modellab extends Model
{
    protected $table = 'aur_model_lab_file';
    protected $guarded = ['id'];
     

    // public function extensions()
    // {
    //     return $this->hasMany('App\Models\Labcolab\Extendfilemodel','modele');
    // }
 
     public function inscription() 
    {
        return $this->hasOne('App\Models\Inscription','id','inscription');
    }
}
