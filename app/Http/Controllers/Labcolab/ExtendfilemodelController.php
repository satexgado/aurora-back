<?php

namespace App\Http\Controllers\Labcolab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Labcolab\Extendfilemodel;  

class ExtendfilemodelController extends Controller
{
    public function index()
    {
        return Extendfilemodel::Orderby('id','DESC')->get();
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
            'nom'=> 'required|string',
            'contenu'=> 'nullable|string',
            'statut'=> 'required|string',
            'definir_modele'=>'required|string',
            'modele'=> 'required|int'
        ]);
        $reg = new  Extendfilemodel([
            'nom'=>  $request->get('nom'),
            'contenu'=>  $request->get('contenu'),
            'statut'=> $request->get('statut'),
            'definir_modele'=>  $request->get('definir_modele'),
            'modele'=>  $request->get('modele')
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
      return Extendfilemodel::with('inscription','observateurs')
      ->where('id',$id)->first();
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
        $Extendfilemodel = Extendfilemodel::findOrFail($id);
        $Extendfilemodel->update($request->all());
        return $Extendfilemodel->load('inscription');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Extendfilemodel = Extendfilemodel::findOrFail($id);
        $Extendfilemodel->delete();
        return $Extendfilemodel;
    }
    //Extend file by model
     public function bymodel($id)
    {
        return Extendfilemodel::with('inscription')
        ->where('modele',$id)
        ->where('statut','!=','modele')
        ->where('statut','!=','Validé')
        ->Orderby('id','DESC')
        ->get();
    }

     public function withobserv()
    {
      return Extendfilemodel::with('inscription','observateurs.participant')
      ->where('inscription',Auth::id())
      ->orWhereHas('observateurs', function($query){
        $query->where('participant',Auth::id());
      })
      ->Orderby('id','DESC')->get();
    }
       public function bykan()
    {
      return Extendfilemodel::with('inscription','observateurs.participant')
      ->where('statut','!=','Validé')
      ->where('statut','!=','modele')
      // ->where('inscription',Auth::id())
      // ->orWhereHas('observateurs', function($query){
      //   $query->where('participant',Auth::id());
      // })
      ->Orderby('id','DESC')->get();
    }

    public function modele($id)
    {
      return Extendfilemodel::with('inscription')
      ->where('modele',$id)
      ->where('definir_modele','Oui')
      ->Orderby('id','DESC')->get();
    }
    //Valid
     public function valid($id)
    {
      return Extendfilemodel::with('inscription')
      ->where('modele',$id)
      ->where('statut','Validé')
      ->Orderby('id','DESC')->get();
    }




}
