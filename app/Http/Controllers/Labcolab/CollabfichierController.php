<?php

namespace App\Http\Controllers\Labcolab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use App\Models\Labcolab\Collabfichier;
use Illuminate\Support\Facades\Auth;

class CollabfichierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Collabfichier::with('fichier','participant','inscription')
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
            'value'=> 'required|int',
            'participant'=> 'required'
        ]);

        foreach( $request->get('participant') as $obj){
            $reg = new  Collabfichier([
                'participant'=> (int)$obj,
                'fichier'=>  $request->get('value')
            ]);
            if(count($this->verify((int)$obj, $request->get('value')))==0){
                $reg->inscription=Auth::id();
                $reg->save();
            }
        }
        return $this->byfichier($request->get('value'));
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Collabfichier = Collabfichier::findOrFail($id);
        $Collabfichier->delete();
        return $Collabfichier;
    }
    public function verify($rec,$res){
        return Collabfichier::where('participant','=',$rec)
        ->where('fichier','=',$res)
        ->get();
    }
    //Collab by fichier
    public function byfichier($fil){
        return Collabfichier::with('participant')
        ->where('fichier',$fil)
        ->Orderby('id','DESC')
        ->get();
    }

}
