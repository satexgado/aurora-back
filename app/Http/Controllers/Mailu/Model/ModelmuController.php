<?php

namespace App\Http\Controllers\Mailu\Model;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mailu\Model\Modelmu;  

class ModelmuController extends Controller
{
   public function index()
    {
        return Modelmu::with('categorie','inscription')->Orderby('id','DESC')->get();
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
            'categorie'=> 'required|int'
        ]);
        $reg = new  Modelmu([
            'nom'=>  $request->get('nom'),
            'contenu'=>  $request->get('contenu'),
            'statut'=> $request->get('statut'),
            'categorie'=>  $request->get('categorie')
        ]);
        $reg->inscription=Auth::id();
        $reg->save();
        return $reg->load('categorie','inscription');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      return Modelmu::with('inscription','categorie')
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
        $Modelmu = Modelmu::findOrFail($id);
        $Modelmu->update($request->all());
        return $Modelmu->load('categorie','inscription');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Modelmu = Modelmu::findOrFail($id);
        $Modelmu->delete();
        return $Modelmu;
    }
    //Model public 
     public function bypub()
    {
        return Modelmu::with('categorie','inscription')
        ->where('statut','Public')
        ->Orderby('id','DESC')
        ->get();
    }
   //Model privé
     public function bypriv()
    {
      return Modelmu::with('categorie','inscription')
      ->where('inscription',Auth::id())
      ->where('statut','Privé')
      ->Orderby('id','DESC')->get();
    }

}
