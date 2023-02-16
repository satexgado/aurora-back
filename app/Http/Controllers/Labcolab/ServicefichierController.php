<?php

namespace App\Http\Controllers\Labcolab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth;
use App\Models\Labcolab\Servicefichier; 

class ServicefichierController extends Controller
{
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Servicefichier::Orderby('id','DESC')->get();
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
            'service'=> 'required|int'
        ]);
        $reg = new  Servicefichier([
            'fichier'=>  $request->get('fichier'),
            'service'=>  $request->get('service')
        ]);
        if(count($this->verify($request->get('service'), $request->get('fichier')))==0)
        {
            $reg->inscription=Auth::id();
            $reg->save();
            return $reg->load('service');
        }
        else{
            return response()->json([
                'message'=>'Désolè le service existe déjà'
            ]);
        }
       

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      return Servicefichier::with('inscription')->where('id',$id)->first();
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
        $Servicefichier = Servicefichier::findOrFail($id);
        $Servicefichier->update($request->all());
        return $Servicefichier;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Servicefichier = Servicefichier::findOrFail($id);
        $Servicefichier->delete();
        return $Servicefichier;
    }
    //By file
    public function byfile($id)
    {
        return Servicefichier::with('service')->where('fichier',$id)->get();
    }

     public function verify($rec,$res){
        return Servicefichier::where('service','=',$rec)
        ->where('fichier','=',$res)
        ->get();
    }
}
