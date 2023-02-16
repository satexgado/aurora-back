<?php

namespace App\Models\Rapport;

use Illuminate\Database\Eloquent\Model;

class Ticketrapport extends Model
{
    protected $table = 'aur_ticket_rapport';
    protected $guarded = ['id'];
     
     public function courrier() 
    {
        return $this->hasOne('App\Models\Courrier\CrCourrier','id','courrier');
    }

    public function executions()
    {
        return $this->hasMany('App\Models\Rapport\Execticketrap','ticket');
    }
 
   
     public function inscription() 
    {
        return $this->hasOne('App\Models\Inscription','id','inscription');
    }

}
