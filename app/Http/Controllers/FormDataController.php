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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

class VerificationController extends Controller
{
    public function getAffectation()
    {

        $data['cr_statuts']     =  CrStatut::all();
        $data['cr_types']       =  CrType::all();
        $data['cr_natures']     =  CrNature::all();
        $data['cr_urgences']    =  CrUrgence::all();
        $data['structures']     =  Structure::with('_employes')->all();
        $data['cr_provenances'] =  CrProvenance::all();
        $data['cr_coordonnees'] =  CrCoordonnee::all();

        return response()
        ->json(['data' => $data]);
    }
}
