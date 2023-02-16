<?php

namespace App\Http\Controllers\Suivre;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Suivre\Execticketsuiv;
use Illuminate\Support\Facades\Auth;
use App\Models\Suivre\Ticketsuiv; 

class ExecticketsuivController extends Controller
{
    public function index()
    {
        return Execticketsuiv::with('ticket')->Orderby('id','DESC')->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'ticket'=> 'required|int',
            'heure'=> 'required',
            'date'=> 'required|date',
            'moyen'=> 'required|string',
            'date_ouverture'=> 'nullable|date',
            'entite_contact'=> 'required|int',
            'confirmation'=> 'required|string',
            'nouv_contact'=> 'nullable|int',
            'ordonnance'=> 'required|string',
            'statut_suivi'=> 'required|string',
            'statut_traitement'=> 'required|string',
            'duree'=> 'required|int',
            'passer_main'=> 'required|string',
            'nouv_resp'=> 'nullable|int'
        ]);
        $reg = new  Execticketsuiv([
            'ticket'=>  $request->get('ticket'),
            'heure'=>  $request->get('heure'),
            'date'=>  $request->get('date'),
            'moyen'=> $request->get('moyen'),
            'date_ouverture'=>  $request->get('date_ouverture'),
            'entite_contact'=>  $request->get('entite_contact'),
            'confirmation'=>  $request->get('confirmation'),
            'nouv_contact'=>  $request->get('nouv_contact'),
            'ordonnance'=>  $request->get('ordonnance'),
            'statut_suivi'=>  $request->get('statut_suivi'),
            'statut_traitement'=>  $request->get('statut_traitement'),
            'duree'=>  $request->get('duree'),
            'passer_main'=>  $request->get('passer_main'),
            'nouv_resp'=>  $request->get('nouv_resp')
        ]);
        $reg->inscription=Auth::id();
        $reg->save();

        return $reg->load('inscription');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      return Execticketsuiv::with('inscription','entite_contact',
            'nouv_contact','nouv_resp')
      ->where('id',$id)
      ->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $Execticketsuiv = Execticketsuiv::findOrFail($id);
        $Execticketsuiv->update($request->all());
        return $Execticketsuiv;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Execticketsuiv = Execticketsuiv::findOrFail($id);
        $Execticketsuiv->delete();
        return $Execticketsuiv;
    }
     public function byticket($id) 
    {
        $ticket=Ticketsuiv::with('courrier','inscription')->where('id',$id)->first();
        $exect= Execticketsuiv::with('inscription')
        ->where('ticket',$id)
        ->Orderby('id','DESC')
        ->get();
        return response()->json([
        'ticket'=>$ticket,
        'exect'=>$exect
      ]);
    }
    public function byexecwithsuiv($id)
    {
      $currentexec= Execticketsuiv::with('inscription','entite_contact',
            'nouv_contact','nouv_resp')
      ->where('id',$id)
      ->first();
      $suivi=Ticketsuiv::whereHas('executions', function($query) use($currentexec){
        $query->where('ticket','!=',$currentexec->ticket);
      })->Orderby('id','DESC')->get();
      return response()->json([
        'execution'=>$currentexec,
        'suivi'=>$suivi
      ]);
    }
}
