<?php

namespace App\Http\Controllers\Mailu\Model;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mailu\Model\Categmodelmu;  

class CategmodelmuController extends Controller
{
    public function index()
    {
        return Categmodelmu::Orderby('id','DESC')->get();
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
            'couleur'=> 'required|string'
        ]);
        $reg = new  Categmodelmu([
            'nom'=>  $request->get('nom'),
            'couleur'=>  $request->get('couleur')
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
      return Categmodelmu::with('inscription')->where('id',$id)->first();
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
        $Categmodelmu = Categmodelmu::findOrFail($id);
        $Categmodelmu->update($request->all());
        return $Categmodelmu->load('inscription');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Categmodelmu = Categmodelmu::findOrFail($id);
        $Categmodelmu->delete();
        return $Categmodelmu;
    }
}
