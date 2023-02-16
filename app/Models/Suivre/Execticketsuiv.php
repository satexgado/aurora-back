<?php

namespace App\Models\Suivre;

use Illuminate\Database\Eloquent\Model;

class Execticketsuiv extends Model
{
    protected $table = 'aur_execution_ticket';
    protected $guarded = ['id'];
     
     public function ticket() 
    {
        return $this->hasOne('App\Models\Suivre\Ticketsuiv','id','ticket');
    }

    public function entite_contact() 
    {
        return $this->hasOne('App\Models\Inscription','id','entite_contact');
    }

    public function nouv_contact() 
    {
        return $this->hasOne('App\Models\Inscription','id','nouv_contact');
    }

    public function nouv_resp() 
    {
        return $this->hasOne('App\Models\Inscription','id','nouv_resp');
    }
   
     public function inscription() 
    {
        return $this->hasOne('App\Models\Inscription','id','inscription');
    }
}
