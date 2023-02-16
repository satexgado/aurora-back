<?php

namespace App\Http\Controllers\Labcolab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Labcolab\Commentfichier;
use Illuminate\Support\Facades\Auth;

class CommentfichierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Commentfichier::with('inscription')
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
            'commentaire'=> 'required|string',
            'value'=> 'required|int'
        ]);

         $reg = new  Commentfichier([
            'commentaire'=>  $request->get('commentaire'),
            'fichier'=>$request->get('value')
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
        //
    }

    public function commentbyfichier($id)
    {
        return Commentfichier::with('inscription')
        ->where('fichier','=',$id)
        ->Orderby('id','DESC')
        ->get();
    }
}
