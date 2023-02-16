<?php

namespace App\Http\Controllers\Labcolab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Labcolab\Intervenantfichier; 
use App\Models\Labcolab\Extendfilemodel; 

class IntervenantfichierController extends Controller
{
  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Intervenantfichier::Orderby('id','DESC')->get();
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
            'contenu'=> 'required|string',
            'statut'=> 'required|string'
        ]);
        $reg = new  Intervenantfichier([
            'fichier'=>  $request->get('fichier'),
            'contenu'=>  $request->get('contenu'),
            'statut'=>  $request->get('statut')
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
      return Intervenantfichier::with('inscription')->where('id',$id)->first();
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
        $Intervenantfichier = Intervenantfichier::findOrFail($id);
        $Intervenantfichier->update($request->all());
        return $Intervenantfichier;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Intervenantfichier = Intervenantfichier::findOrFail($id);
        $Intervenantfichier->delete();
        return $Intervenantfichier;
    }
    //By file
    public function byfile($id)
    {
      $file= Extendfilemodel::with('inscription','observateurs.participant')
      ->where('id',$id)
      ->get();
      $intervenant=Intervenantfichier::with('inscription')
      ->where('fichier',$id)
      ->get();
      return response()->json([
        'file'=>$file,
        'intervenant'=>$intervenant
      ]);
    }
    //kanban
     public function kanbanbyfile($id)
    {
      return Intervenantfichier::with('inscription')
      ->where('fichier',$id)
      ->get();
    }
}
