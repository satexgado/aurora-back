<?php

namespace App\Http\Controllers\Labcolab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Labcolab\Fichiermodel;  

class FichiermodelController extends Controller
{
   public function index()
    {
        return Fichiermodel::withCount('extensions')->Orderby('id','DESC')->get();
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
            'description'=> 'required|string'
        ]);
        $reg = new  Fichiermodel([
            'nom'=>  $request->get('nom'),
            'description'=>  $request->get('description')
        ]);
        $reg->inscription=Auth::id();
        $reg->save();

        return $reg->loadCount('extensions');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      return Fichiermodel::with('inscription')->where('id',$id)->first();
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
        $Fichiermodel = Fichiermodel::findOrFail($id);
        $Fichiermodel->update($request->all());
        return $Fichiermodel->loadCount('extensions');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Fichiermodel = Fichiermodel::findOrFail($id);
        $Fichiermodel->delete();
        return $Fichiermodel;
    }
}
