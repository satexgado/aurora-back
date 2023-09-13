<?php

namespace App\Http\Controllers\Ged;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder as myBuilder;
use App\Http\Shared\Optimus\Bruno\EloquentBuilderTrait;
use App\Http\Shared\Optimus\Bruno\LaravelController;
use App\Models\Ged\GedModele;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GedModeleController extends LaravelController
{
    use EloquentBuilderTrait;

    public function getAll(Request $request)
    {

        // Parse the resource options given by GET parameters
        $resourceOptions = $this->parseResourceOptions();

        $query = GedModele::query();
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
        DB::beginTransaction();

        try {

        $item = GedModele::create([
            'inscription_id' => Auth::id(),
            'libelle' => $request->libelle,
            'image' => $request->image,
            'allowed_type' =>  $request->allowed_type, 
            'structure_id' =>  $request->structure_id, 
            'description' => $request->description,
            'active' => $request->active,
        ]);

        if($request->exists('form_field'))
        {
            // $json = utf8_encode($request->form_field);
            // $data = json_decode($json);
            $data = json_decode($request->form_field);
            if(is_array($data)){
                foreach($data as $element) {
                    GedModeleFormField::create([
                        'inscription_id' => Auth::id(),
                        'modele_id' => $item->id,
                        'libelle' => $element->name,
                        'label' => $element->name,
                        'type' => $element->type,
                        'required' => $element->required
                    ]);
                }
            }
        }

        DB::commit();
    } catch (\Throwable $e) {
        DB::rollback();
        throw $e;
    }
        return response()
        ->json($item->load([
            'structure', 'ged_modele_form_fields']));
    }

    public function update(Request $request, $id)
    {

        $item = GedModele::findOrFail($id);
        $data = $request->all();
        $item->fill($data)->save();

        if($request->exists('removedFormField'))
        {
            $json = utf8_encode($request->removedFormField);
            $data = json_decode($json);
            if(is_array($data)){
                foreach($data as $element) {
                    $remove = GedModeleFormField::findOrFail($element);
                    $remove->delete();
                }
            }
        }

        if($request->exists('form_field'))
        {
            // $json = utf8_encode($request->form_field);
            // $data = json_decode($json);
            $data= json_decode($request->form_field);
            if(is_array($data)){
                foreach($data as $element) {
                    GedModeleFormField::updateOrCreate([
                        'id' => $element->id,
                    ],[
                        'inscription_id' => Auth::id(),
                        'modele_id' => $item->id,
                        'libelle' => $element->name,
                        'label' => $element->name,
                        'type' => $element->type,
                        'required' => $element->required
                    ]);
                }
            }
        }

        return response()
        ->json($item->load([
            'structure', 'ged_modele_form_fields']));
    }

    public function destroy($id)
    {
        $item = GedModele::findOrFail($id);

        $item->delete();

        return response()
        ->json(['msg' => 'Suppression effectué']);
    }

    public function restore($id)
    {
        $restoreDataId = GedModele::withTrashed()->findOrFail($id);
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
        $item = GedModele::find($item_id);
        $item->{$relation_name}()->syncWithoutDetaching([$relation_id => ['inscription_id'=> Auth::id()]]);

        return response()->json([
            'message' => 'Modele affecter'
        ]);
    }

    public function detachAffectation(Request $request)
    {
        $item_id = $request->id;
        $relation_name = $request->relation_name;
        $relation_id = $request->relation_id;
        $item = GedModele::find($item_id);
        $item->{$relation_name}()->detach($relation_id);

        return response()->json([
            'message' => 'Modele Désaffecter'
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
        $item = GedModele::find($id);

        $result = null;

        foreach($affectation as $key=>$value)
        {
            $pivotData = array_fill(0, count($value), ['inscription_id'=> Auth::id()]);
            $syncData  = array_combine($value, $pivotData);
            $result = $item->{$key}()->sync($syncData);
        }

        return $result;
    }

    public function getAffectation(GedModele $GedModele)
    {

        return response()
        ->json(['data' => 'need to update it']);
    }
}
