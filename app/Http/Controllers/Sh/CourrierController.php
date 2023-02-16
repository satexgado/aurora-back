<?php

namespace App\Http\Controllers\Sh;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Courrier\CrCourrier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Inscription;
use App\Models\Courrier\CrCoordonnee;
use App\Models\Structure\Structure;
use App\Models\Ged\FichierType;

class CourrierController extends Controller
{
       public function allcourrier() 
       {  
          return DB::table('cr_courrier')->Orderby('id','DESC')->get();
       }

       public function getAlluserLikename($name)
    {
       return  Inscription::where('email','like','%' . $name . '%')
        ->orWhere('telephone','like','%' . $name . '%')
        ->orWhere('nom','like','%' . $name . '%')
        ->orWhere('prenom','like','%' . $name . '%')
        ->get();        
    }

      public function searchcordone($name)
    {
      return CrCoordonnee::where('libelle','like','%' . $name . '%')->get();
    }
       public function structure()
    {
      return DB::table('structures')->Orderby('id','DESC')->get();
    }
       public function fichiertype()
    {
      return Fichiertype::Orderby('id','DESC')->get();
    }
    
}
