<?php

namespace App\Http\Controllers;

use App\Models\Structure\Inscription;
use App\Models\Courrier\CrStatut;
use App\Models\Courrier\CrType;
use App\Models\Courrier\CrNature;
use App\Models\Courrier\CrUrgence;
use App\Models\Structure\Structure;
use App\Models\Courrier\CrProvenance;
use App\Models\Courrier\CrCoordonnee;
use App\Models\Courrier\CrDossier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

class FormDependanciesController extends Controller
{
    public function entrant()
    {

        $data['cr_statuts']     =  CrStatut::get();
        $data['cr_types']       =  CrType::get();
        $data['cr_natures']     =  CrNature::get();
        $data['cr_urgences']    =  CrUrgence::get();
        $data['structures']     =  Structure::with('_employes')->get();
        $data['cr_provenances'] =  CrProvenance::get();
        $data['cr_coordonnees'] =  CrCoordonnee::get();
        $data['cr_dossiers']     =  CrDossier::get();


        return response()
        ->json(['data' => $data]);
    }
}
