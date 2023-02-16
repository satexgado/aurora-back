<?php

namespace App\Http\Controllers\Labcolab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth;
use App\Models\Labcolab\Echeancefichier; 

class EcheancefichierController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Echeancefichier::Orderby('id','DESC')->get();
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
            'fichier'=> 'required|int',
            'date_debut'=> 'required|date',
            'date_fin'=> 'required|date'
        ]);
        $reg = new  Echeancefichier([
            'fichier'=>  $request->get('fichier'),
            'date_debut'=>  $request->get('date_debut'),
            'date_fin'=>  $request->get('date_fin')
        ]);
        $reg->inscription=Auth::id();
        $reg->save();
        return $reg;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      return Echeancefichier::with('inscription')->where('id',$id)->first();
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
        $Echeancefichier = Echeancefichier::findOrFail($id);
        $Echeancefichier->update($request->all());
        return $Echeancefichier;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Echeancefichier = Echeancefichier::findOrFail($id);
        $Echeancefichier->delete();
        return $Echeancefichier;
    }
    //By file
    public function byfile($id)
    {
        return Echeancefichier::where('fichier',$id)->get();
    }
}
