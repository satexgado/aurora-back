<?php

namespace App\Http\Controllers\Labcolab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Labcolab\Motclefichier; 

class MotclefichierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Motclefichier::Orderby('id','DESC')->get();
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
            'mot'=> 'required|string'
        ]);
        $reg = new  Motclefichier([
            'fichier'=>  $request->get('fichier'),
            'mot'=>  $request->get('mot')
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
      return Motclefichier::with('inscription')->where('id',$id)->first();
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
        $Motclefichier = Motclefichier::findOrFail($id);
        $Motclefichier->update($request->all());
        return $Motclefichier;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Motclefichier = Motclefichier::findOrFail($id);
        $Motclefichier->delete();
        return $Motclefichier;
    }
    //By file
    public function byfile($id)
    {
        return Motclefichier::where('fichier',$id)->get();
    }
}
