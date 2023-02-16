<?php

namespace App\Http\Controllers\Labcolab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Labcolab\Observfichier;
use Illuminate\Support\Facades\Auth;

class ObservfichierController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Observfichier::with('fichier','participant','inscription')
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
            'participant'=> 'required',
            'type'=> 'required|string'
        ]);

        foreach( $request->get('participant') as $obj){
            $reg = new  Observfichier([
                'participant'=> (int)$obj,
                'fichier'=>  $request->get('value'),
                'type'=>  $request->get('type')
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
        $Observfichier = Observfichier::findOrFail($id);
        $Observfichier->delete();
        return $Observfichier;
    }
    public function verify($rec,$res){
        return Observfichier::where('participant','=',$rec)
        ->where('fichier','=',$res)
        ->get();
    }
    //observe by fichier
    public function byfichier($fil){
        return Observfichier::with('participant')
        ->where('fichier',$fil)
        ->Orderby('id','DESC')
        ->get();
    }
}
