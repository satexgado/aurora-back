<?php

namespace App\Http\Controllers\Ged;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder as myBuilder;
use App\Http\Shared\Optimus\Bruno\EloquentBuilderTrait;
use App\Http\Shared\Optimus\Bruno\LaravelController;
use App\Models\Ged\GedDossierAdministratif;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GedDossierAdministratifController extends LaravelController
{
    use EloquentBuilderTrait;

    public function getAll(Request $request)
    {

        // Parse the resource options given by GET parameters
        $resourceOptions = $this->parseResourceOptions();

        $query = GedDossierAdministratif::query();
        $this->applyResourceOptions($query, $resourceOptions);

        if(isset($request->paginate)) {
            $items = $query->paginate($request->paginate);
            $parsedData = $items;

        } else {
            $items = $query->get();
            // Parse the data using Optimus\Architect
            $parsedData = $this->parseData($items, $resourceOptions, 'data');
        }

        // Create JSON response of parsed data
        return $this->response($parsedData);
    }

    public function filterIsIns(myBuilder $query, $method, $clauseOperator, $value, $in)
    {
        if ($value) {
            $query->where('inscription_id', Auth::id());
        }
    }


    public function filterSearchString(myBuilder $query, $method, $clauseOperator, $value)
    {
        if($value) {
            $words = explode(" ", $value);
            $query->where(function ($query) use($words) {
                for ($i = 0; $i < count($words); $i++){
                   $query->where('libelle', 'like',  '%' . $words[$i] .'%');
                }      
           });
        }
    }

    public function store(Request $request)
    {

        $item = GedDossierAdministratif::create([
            'inscription_id' => Auth::id(),
            'libelle' => $request->libelle,
            'description' => $request->description,
            'structure_id' => $request->structure_id,
        ]);

        return response()
        ->json($item->load([
            'structure']));
    }

    public function update(Request $request, $id)
    {

        $item = GedDossierAdministratif::findOrFail($id);
        $data = $request->all();

        $item->fill($data)->save();

        return response()
        ->json($item->load([
            'structure']));
    }

    public function destroy($id)
    {
        $item = GedDossierAdministratif::findOrFail($id);

        $item->delete();

        return response()
        ->json(['msg' => 'Suppression effectué']);
    }

    public function restore($id)
    {
        $restoreDataId = GedDossierAdministratif::withTrashed()->findOrFail($id);
        if($restoreDataId && $restoreDataId->trashed()){
           $restoreDataId->restore();
        }
        return response()
        ->json($restoreDataId);
    }

    public function attachAffectation(Request $request)
    {

        $item_id = $request->id;
        $relation_name = $request->relation_name;
        $relation_id = $request->relation_id;
        $item = GedDossierAdministratif::find($item_id);
        $item->{$relation_name}()->syncWithoutDetaching([$relation_id => ['inscription_id'=> Auth::id()]]);

        return response()->json([
            'message' => 'Element affecter'
        ]);
    }

    public function detachAffectation(Request $request)
    {
        $item_id = $request->id;
        $relation_name = $request->relation_name;
        $relation_id = $request->relation_id;
        $item = GedDossierAdministratif::find($item_id);
        $item->{$relation_name}()->detach($relation_id);

        return response()->json([
            'message' => 'Element Désaffecter'
        ]);
    }


    public function setAffectation(Request $request)
    {
        $item_id = $request->id;
        DB::beginTransaction();
        $result = array();


        try {
            if(is_array($item_id)) {
                foreach($item_id as $id) {
                    $result[$id] = $this->doSetAffectation($id, $request->affectation);
                }
            } else {
                $result[$item_id] = $this->doSetAffectation($item_id, $request->affectation);
            }
            DB::commit();
        } catch (\Throwable $e) {

            DB::rollback();
            throw $e;
        }

        return response()->json([
            'message' => 'Affectation mis à jour',
            'result'=>$result
        ]);
    }

    public function doSetAffectation($id, $affectation) {
        $item = GedDossierAdministratif::find($id);

        $result = null;

        foreach($affectation as $key=>$value)
        {
            $pivotData = array_fill(0, count($value), ['inscription_id'=> Auth::id()]);
            $syncData  = array_combine($value, $pivotData);
            $result = $item->{$key}()->sync($syncData);
        }

        return $result;
    }

    public function getAffectation(GedDossierAdministratif $GedDossierAdministratif)
    {

        return response()
        ->json(['data' => 'need to update it']);
    }
}
