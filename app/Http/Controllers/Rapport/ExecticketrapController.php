<?php

namespace App\Http\Controllers\Rapport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Rapport\Ticketrapport;  
use App\Models\Rapport\Execticketrap;

class ExecticketrapController extends Controller
{
     public function index()
    {
        return Execticketrap::with('ticket')->Orderby('id','DESC')->get();
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
            'statut_depot'=> 'required|string',
            'date_depot'=> 'nullable|date',
            'heure_depot'=> 'nullable',
            'moyen_depot'=> 'nullable|string',
            'entite_depot'=> 'nullable|string',
            'personnelle'=> 'nullable',
            'coordonnee'=> 'nullable',
            'raison_non_depot'=> 'nullable|string',
            'statut'=> 'required|string',
            'observation'=> 'required|string'
        ]);
        $reg = new  Execticketrap([
            'ticket'=>  $request->get('ticket'),
            'statut_depot'=>  $request->get('statut_depot'),
            'date_depot'=>  $request->get('date_depot'),
            'heure_depot'=> $request->get('heure_depot'),
            'moyen_depot'=>  $request->get('moyen_depot'),
            'entite_depot'=>  $request->get('entite_depot'),
            'personnelle'=>  $request->get('personnelle'),
            'coordonnee'=>  $request->get('coordonnee'),
            'raison_non_depot'=>  $request->get('raison_non_depot'),
            'statut'=>  $request->get('statut'),
            'observation'=>  $request->get('observation')
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
      return Execticketrap::with('inscription','coordonnee','personnelle')
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
        $Execticketrap = Execticketrap::findOrFail($id);
        $Execticketrap->update($request->all());
        return $Execticketrap;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Execticketrap = Execticketrap::findOrFail($id);
        $Execticketrap->delete();
        return $Execticketrap;
    }
     public function byticket($id) 
    {
        $ticket=Ticketrapport::with('courrier','inscription')->where('id',$id)->first();
        $exect= Execticketrap::with('inscription')
        ->where('ticket',$id)
        ->Orderby('id','DESC')
        ->get();
        return response()->json([
        'ticket'=>$ticket,
        'exect'=>$exect
      ]);
    }
    public function byexecwithrap($id)
    {
      $currentexec= Execticketrap::with('inscription','coordonnee',
            'personnelle')
      ->where('id',$id)
      ->first();
      $suivi=Ticketrapport::whereHas('executions', function($query) use($currentexec){
        $query->where('ticket','!=',$currentexec->ticket);
      })->Orderby('id','DESC')->get();
      return response()->json([
        'execution'=>$currentexec,
        'suivi'=>$suivi
      ]);
    }
}
