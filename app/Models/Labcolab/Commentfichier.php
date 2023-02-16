<?php

namespace App\Models\Labcolab;

use Illuminate\Database\Eloquent\Model;

class Commentfichier extends Model
{
    protected $table = 'aur_comment_fichier';
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
