<?php

namespace App\Http\Controllers\Courrier;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder as myBuilder;
use App\Http\Shared\Optimus\Bruno\EloquentBuilderTrait;
use App\Http\Shared\Optimus\Bruno\LaravelController;
use App\Models\Courrier\CrCoordonnee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CrCoordonneeController extends LaravelController
{
    use EloquentBuilderTrait;

    public function getAll(Request $request)
    {

        // Parse the resource options given by GET parameters
        $resourceOptions = $this->parseResourceOptions();

        $query = CrCoordonnee::query();
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

    public function filterGroupesId(myBuilder $query, $method, $clauseOperator, $value)
    {
        if ($value) {
            $ids = explode(",", $value);
             $query->whereHas('cr_coordonnee_groupes', function($query) use ($ids) {
                $query->whereIn('cr_affectation_coordonnee_groupe.groupe_id', $ids);
            });
        }
    }

    public function filterSearchString(myBuilder $query, $method, $clauseOperator, $value)
    {
        if($value) {
            $query->orWhere('libelle', 'like', "%" .$value . "%");
        }
    }

    public function filterHasTag(myBuilder $query, $method, $clauseOperator, $value)
    {
        if($value) {
            $query->orWhere('tag', 'like', "%" .$value . "%");
        }
    }

    public function store(Request $request)
    {

        $item = CrCoordonnee::create([
            'inscription_id' => Auth::id(),
            'libelle' => $request->libelle,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'condition_suivi' => $request->condition_suivi,
            'commentaire' => $request->commentaire,
            'tag' => $request->tag,
        ]);

        
        if($request->exists('groupes_id'))
        {
            $json = utf8_encode($request->groupes_id);
            $groupes = json_decode($json);
            if(!is_array($groupes))
            {
                $groupes = explode(',', $groupes);
            }
            $pivotData = array_fill(0, count($groupes), ['inscription_id'=> 1]);
            $attachData  = array_combine($groupes, $pivotData);
            $item->cr_coordonnee_groupes()->attach($attachData);
        }


        return response()
        ->json($item->load('cr_coordonnee_groupes'));
    }

    public function update(Request $request, $id)
    {

        $item = CrCoordonnee::findOrFail($id);

        $data = $request->all();

        $item->fill($data)->save();

        if($request->exists('groupes_id'))
        {
            $json = utf8_encode($request->groupes_id);
            $groupes = json_decode($json);
            if(!is_array($groupes))
            {
                $groupes = explode(',', $groupes);
            }
            $pivotData = array_fill(0, count($groupes), ['inscription_id'=> 1]);
            $attachData  = array_combine($groupes, $pivotData);
            $item->cr_coordonnee_groupes()->sync($attachData);
        }

        return response()
        ->json($item->load('cr_coordonnee_groupes'));
    }

    public function destroy($id)
    {
        $item = CrCoordonnee::findOrFail($id);

        $item->delete();

        return response()
        ->json(['msg' => 'Suppression effectué']);
    }

    public function restore($id)
    {
        $restoreDataId = CrCoordonnee::withTrashed()->findOrFail($id);
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
        $item = CrCoordonnee::find($item_id);
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
        $item = CrCoordonnee::find($item_id);
        $item->{$relation_name}()->detach($relation_id);

        return response()->json([
            'message' => 'Element Désaffecter'
        ]);
    }


    public function setAffectation(Request $request)
    {
        $item_id = $request->id;
        $result = null;
        DB::beginTransaction();

        try {

            $item = CrCoordonnee::find($item_id);

            foreach($request->affectation as $key=>$value)
            {
                $pivotData = array_fill(0, count($value), ['inscription_id'=> Auth::id()]);
                $syncData  = array_combine($value, $pivotData);
                $result = $item->{$key}()->sync($syncData);
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

    public function getAffectation($id)
    {
        $item = CrCoordonnee::findOrFail($id);

        $data['cr_coordonnee_groupes'] = $item->cr_coordonnee_groupes()->get();
        return response()
        ->json(['data' => $data]);
    }
}
