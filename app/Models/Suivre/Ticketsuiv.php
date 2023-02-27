<?php

namespace App\Models\Suivre;

use Illuminate\Database\Eloquent\Model;

class Ticketsuiv extends Model
{
    protected $table = 'aur_tickets';
    protected $guarded = ['id'];

     public function courrier()
    {
        return $this->hasOne('App\Models\Dash\CrCourrier','id','courrier');
    }
    public function executions()
    {
        return $this->hasMany('App\Models\Suivre\Execticketsuiv','ticket');
    }


     public function inscription()
    {
        return $this->hasOne('App\Models\Inscription','id','inscription');
    }
}
