<?php

namespace App\Http\Controllers\Rapport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Rapport\Ticketrapport;  

class TicketrapportController extends Controller
{
    public function index()
    {
        return Ticketrapport::with('courrier')
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
            'orientation'=> 'required|string',
            'modalite'=> 'required|string',
            'date_modalite'=> 'required|date',
            'objectif'=> 'required|string',
            'date_exec'=> 'required|date',
            'duree'=> 'required|int',
            'niveau'=> 'required|string',
            'date_exp'=> 'required|date'
        ]);
        $reg = new  Ticketrapport([
            'courrier'=>  $request->get('courrier'),
            'orientation'=>  $request->get('orientation'),
            'modalite'=> $request->get('modalite'),
            'date_modalite'=>  $request->get('date_modalite'),
            'objectif'=>  $request->get('objectif'),
            'date_exec'=>  $request->get('date_exec'),
            'duree'=>  $request->get('duree'),
            'niveau'=>  $request->get('niveau'),
            'date_exp'=>  $request->get('date_exp')
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
      return Ticketrapport::with('courrier','inscription')->where('id',$id)->first();
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
        $Ticketrapport = Ticketrapport::findOrFail($id);
        $Ticketrapport->update($request->all());
        return $Ticketrapport->load('courrier');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Ticketrapport = Ticketrapport::findOrFail($id);
        $Ticketrapport->delete();
        return $Ticketrapport;
    }
}
