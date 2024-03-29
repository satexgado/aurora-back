<?php

namespace App\Http\Controllers\Ged;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder as myBuilder;
use App\Http\Shared\Optimus\Bruno\EloquentBuilderTrait;
use App\Http\Shared\Optimus\Bruno\LaravelController;
use App\Models\Ged\GedWorkspaceCoordonnee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GedWorkspaceCoordonneeController extends LaravelController
{
    use EloquentBuilderTrait;

    public function getAll(Request $request)
    {

        // Parse the resource options given by GET parameters
        $resourceOptions = $this->parseResourceOptions();

        $query = GedWorkspaceCoordonnee::query();
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
            $query->where('inscription', Auth::id());
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

        $item = GedWorkspaceCoordonnee::create([
            'inscription_id' => Auth::id(),
            'coordonnee_id' => $request->coordonnee_id,
            'groupe_id' => $request->groupe_id,
            'workspace_id' => $request->workspace_id,
        ]);

        return response()
        ->json($item);
    }

    public function multistore(Request $request)
    {

        DB::beginTransaction();
        $result = array();

        try { 
            
            if($request->exists('removedCoordonnees'))
            {
                // $json = utf8_encode($request->removedPartages);
                // $data = json_decode($json);
                if(is_array($request->removedCoordonnees)){
                    foreach($request->removedCoordonnees as $element) {
                        $remove = GedWorkspaceCoordonnee::find($element);
                        if($remove) {
                            $remove->delete();
                        }
                    }
                }
            }

            if($request->exists('coordonnees'))
            {
                // $json = utf8_encode($request->coordonnees);
                // $data = json_decode($json);
                if(is_array($request->coordonnees)){
                    foreach($request->coordonnees as $element) {
                        $item = GedWorkspaceCoordonnee::updateOrCreate([
                            'workspace_id' => $element['workspace_id'],
                            'coordonnee_id' => $element['coordonnee_id']
                        ],[
                            'inscription_id' => Auth::id(),
                            'coordonnee_id' => $element['coordonnee_id'],
                            'groupe_id' => $element['groupe_id']??null,
                            'workspace_id' => $element['workspace_id']
                        ]);
                        $result[] = $item->load('cr_coordonnee');
                    }
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }

        return response()
        ->json($result);
    }

    public function update(Request $request, $id)
    {

        $item = GedWorkspaceCoordonnee::findOrFail($id);

        $data = $request->all();

        $item->fill($data)->save();

        return response()
        ->json($item);
    }

    public function destroy($id)
    {
        $item = GedWorkspaceCoordonnee::findOrFail($id);

        $item->delete();

        return response()
        ->json(['msg' => 'Suppression effectué']);
    }

    public function restore($id)
    {
        $restoreDataId = GedWorkspaceCoordonnee::withTrashed()->findOrFail($id);
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
        $item = GedWorkspaceCoordonnee::find($item_id);
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
        $item = GedWorkspaceCoordonnee::find($item_id);
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

            $item = GedWorkspaceCoordonnee::find($item_id);

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

    public function getAffectation(GedWorkspaceCoordonnee $GedWorkspaceCoordonnee)
    {

        return response()
        ->json(['data' => 'need to update it']);
    }
}
