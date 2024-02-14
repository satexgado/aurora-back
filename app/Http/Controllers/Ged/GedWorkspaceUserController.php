<?php

namespace App\Http\Controllers\Ged;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder as myBuilder;
use App\Http\Shared\Optimus\Bruno\EloquentBuilderTrait;
use App\Http\Shared\Optimus\Bruno\LaravelController;
use App\Models\Ged\GedWorkspaceUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GedWorkspaceUserController extends LaravelController
{
    use EloquentBuilderTrait;

    public function getAll(Request $request)
    {

        // Parse the resource options given by GET parameters
        $resourceOptions = $this->parseResourceOptions();

        $query = GedWorkspaceUser::query();
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

        $item = GedWorkspaceUser::create([
            'inscription_id' => Auth::id(),
            'personne_id' => $request->personne_id,
            'groupe_id' => $request->groupe_id,
            'workspace_id' => $request->workspace_id,
        ]);

        return response()
        ->json($item->load('personne_inscription'));
    }

    public function multistore(Request $request)
    {

        DB::beginTransaction();
        $result = array();

        try { 
            
            if($request->exists('removedUsers'))
            {
                // $json = utf8_encode($request->removedPartages);
                // $data = json_decode($json);
                if(is_array($request->removedUsers)){
                    foreach($request->removedUsers as $element) {
                        $remove = GedWorkspaceUser::find($element);
                        if($remove) {
                            $remove->delete();
                        }
                    }
                }
            }

            if($request->exists('users'))
            {
                // $json = utf8_encode($request->users);
                // $data = json_decode($json);
                if(is_array($request->users)){
                    foreach($request->users as $element) {
                        $item = GedWorkspaceUser::updateOrCreate([
                            'workspace_id' => $element['workspace_id'],
                            'personne_id' => $element['personne_id']
                        ],[
                            'inscription_id' => Auth::id(),
                            'personne_id' => $element['personne_id'],
                            'groupe_id' => $element['groupe_id']??null,
                            'workspace_id' => $element['workspace_id']
                        ]);

                        $result[] = $item->load('personne_inscription');
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

        $item = GedWorkspaceUser::findOrFail($id);

        $data = $request->all();

        $item->fill($data)->save();

        return response()
        ->json($item->load('personne_inscription'));
    }

    public function destroy($id)
    {
        $item = GedWorkspaceUser::findOrFail($id);

        $item->delete();

        return response()
        ->json(['msg' => 'Suppression effectué']);
    }

    public function restore($id)
    {
        $restoreDataId = GedWorkspaceUser::withTrashed()->findOrFail($id);
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
        $item = GedWorkspaceUser::find($item_id);
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
        $item = GedWorkspaceUser::find($item_id);
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

            $item = GedWorkspaceUser::find($item_id);

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

    public function getAffectation(GedWorkspaceUser $GedWorkspaceUser)
    {

        return response()
        ->json(['data' => 'need to update it']);
    }
}
