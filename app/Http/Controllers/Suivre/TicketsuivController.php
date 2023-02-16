<?php

namespace App\Http\Controllers\Suivre;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Suivre\Ticketsuiv;
use Illuminate\Support\Facades\Auth;

class TicketsuivController extends Controller
{
    public function index()
    {
        return Ticketsuiv::with('courrier')
        ->Orderby('id','DESC')
        ->get();
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
            'courrier'=> 'required|int',
            'mobile'=> 'required|string',
            'orientation'=> 'required|string',
            'modalite'=> 'required|string',
            'date_modalite'=> 'required|date',
            'objectif'=> 'required|string',
            'date_exec'=> 'required|date',
            'duree'=> 'required|int',
            'dossier_exec'=> 'required|string',
            'niveau'=> 'required|string',
            'date_exp'=> 'required|date',
            'dossier_period'=> 'required|int'
            // 'executant'=> 'nullable|int'
        ]);
        $reg = new  Ticketsuiv([
            'courrier'=>  $request->get('courrier'),
            'mobile'=>  $request->get('mobile'),
            'orientation'=>  $request->get('orientation'),
            'modalite'=> $request->get('modalite'),
            'date_modalite'=>  $request->get('date_modalite'),
            'objectif'=>  $request->get('objectif'),
            'date_exec'=>  $request->get('date_exec'),
            'duree'=>  $request->get('duree'),
            'dossier_exec'=>  $request->get('dossier_exec'),
            'niveau'=>  $request->get('niveau'),
            'date_exp'=>  $request->get('date_exp'),
            'dossier_period'=>  $request->get('dossier_period')
        ]);
        $reg->inscription=Auth::id();
        $reg->save();

        return $reg->load('courrier');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      return Ticketsuiv::with('courrier','inscription')->where('id',$id)->first();
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
        $Ticketsuiv = Ticketsuiv::findOrFail($id);
        $Ticketsuiv->update($request->all());
        return $Ticketsuiv->load('courrier');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Ticketsuiv = Ticketsuiv::findOrFail($id);
        $Ticketsuiv->delete();
        return $Ticketsuiv;
    }
    
}
