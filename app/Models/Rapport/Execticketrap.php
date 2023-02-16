<?php

namespace App\Models\Rapport;

use Illuminate\Database\Eloquent\Model;

class Execticketrap extends Model
{
    protected $table = 'aur_execution_rapport';
    protected $guarded = ['id'];
     
     public function ticket() 
    {
        return $this->hasOne('App\Models\Rapport\Ticketrapport','id','ticket');
    }

     public function personnelle() 
    {
        return $this->hasOne('App\Models\Inscription','id','personnelle');
    }
     public function coordonnee() 
    {
        return $this->hasOne('App\Models\Courrier\CrCoordonnee','id','coordonnee');
    }
 
   
     public function inscription() 
    {
        return $this->hasOne('App\Models\Inscription','id','inscription');
    }

}
