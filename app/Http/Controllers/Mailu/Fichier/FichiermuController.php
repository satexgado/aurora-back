<?php

namespace App\Http\Controllers\Mailu\Fichier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mailu\Fichier\Fichiermu;
use Illuminate\Support\Facades\Auth;

class FichiermuController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Fichiermu::with('categorie','type','inscription')
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
            'type'=> 'required|int',
            'categorie'=> 'required|int',
            'nom'=>'required|string',
            'contenu'=>'required|string',
            'statut'=>'required|string',
            'nom_file'=>'required|string',
            'fichier'=>'required'
        ]);
        $reg = new  Fichiermu([
            'type'=>  $request->get('type'),
            'categorie'=>  $request->get('categorie'),
            'nom'=>  $request->get('nom'),
            'contenu'=>  $request->get('contenu'),
            'statut'=>  $request->get('statut'),
            'nom_file'=>  $request->get('nom_file')
        ]);
        $chemin= $request->file('fichier')->store('fichiermu');
        $var=str_replace('fichiermu/','',$chemin);
        $reg->fichier=$var;
        $reg->inscription=Auth::id();
        $reg->save();
        return $reg->load('categorie','type','inscription');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $Fichiermu = Fichiermu::findOrFail($id);
        $Fichiermu->update($request->all());
        return $Fichiermu->load('categorie','type','inscription');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Fichiermu = Fichiermu::findOrFail($id);
        $Fichiermu->delete();

        return $Fichiermu;
    }

        public function storefile(Request $request,$id)
    {

        if($request->has('fichier2')){
            $val= $this->validate($request,[
                'type'=> 'required|int',
                'categorie'=> 'required|int',
                'nom'=>'required|string',
                'contenu'=>'required|string',
                // 'statut'=>'required|string',
                'nom_file'=>'required|string',
                'fichier2'=> 'required'
            ]);
        
            $chemin= $request->file('fichier2')->store('fichiermu');
               
            $var=str_replace('fichiermu/','',$chemin); 

            $Fichiermu = Fichiermu::where('id','=',$id)->findOrFail($id);
            $Fichiermu->fichier=$var;
            $Fichiermu->type=$request->get('type'); 
            $Fichiermu->categorie=$request->get('categorie'); 
            $Fichiermu->nom=$request->get('nom'); 
            $Fichiermu->contenu=$request->get('contenu'); 
            // $Fichiermu->statut=$request->get('statut'); 
            $Fichiermu->nom_file=$request->get('nom_file');
            $Fichiermu->save();
            return $Fichiermu->load('categorie','type','inscription');
        }
        else{
               return 'no file';
        }
    }

      //Model public 
     public function bypub()
    {
        return Fichiermu::with('categorie','type','inscription')
        ->where('statut','local')
        ->Orderby('id','DESC')
        ->get();
    }
   //Model privé
     public function bypriv()
    {
      return Fichiermu::with('categorie','type','inscription')
      ->where('inscription',Auth::id())
      ->where('statut','Privé')
      ->Orderby('id','DESC')->get();
    }


}
