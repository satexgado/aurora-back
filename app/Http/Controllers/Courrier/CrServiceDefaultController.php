<?php

namespace App\Http\Controllers\Courrier;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder as myBuilder;
use App\Http\Shared\Optimus\Bruno\EloquentBuilderTrait;
use App\Http\Shared\Optimus\Bruno\LaravelController;
use App\Models\Courrier\CrServiceDefault;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CrServiceDefaultController extends LaravelController
{
    use EloquentBuilderTrait;

    public function getAll(Request $request)
    {

        // Parse the resource options given by GET parameters
        $resourceOptions = $this->parseResourceOptions();

        $query = CrServiceDefault::query();
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
            if($value) {
                $query->whereHas('structure', function($query) use ($value) {
                    $query->where(DB::raw('lower(structure.libelle)'), 'like', "%" .Str::lower($value). "%");
                    // $query->orWhere(DB::raw('lower(cr_courrier.objet)'), 'like', "%" .Str::lower($value). "%");
                    // $query->orWhere(DB::raw('lower(cr_courrier.numero)'), 'like', "%" .Str::lower($value). "%");
                });
            }
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $item = CrServiceDefault::create([
                'inscription_id' => Auth::id(),
                'structure_id' => $request->structure_id,
            ]);

            if($request->exists('users_id'))
            {
                $json = utf8_encode($request->users_id);
                $data = json_decode($json);
                if(is_array($data)){
                    $pivotData = array_fill(0, count($data), ['inscription_id' => Auth::id()]);
                    $syncData  = array_combine($data, $pivotData);
                    $item->personnes()->sync($syncData);
                }
            }

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }

        return response()
        ->json($item->load(['structure','personnes']));
    }

    public function update(Request $request, $id)
    {

        DB::beginTransaction();

        try {

            $item = CrServiceDefault::findOrFail($id);

            $data = $request->all();

            $item->fill($data)->save();

            if($request->exists('users_id'))
            {
                $json = utf8_encode($request->users_id);
                $data = json_decode($json);
                if(is_array($data)){
                    $pivotData = array_fill(0, count($data), ['inscription_id' => Auth::id()]);
                    $syncData  = array_combine($data, $pivotData);
                    $item->personnes()->sync($syncData);
                }
            }

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }


        return response()
        ->json($item->load(['structure','personnes']));
    }

    public function destroy($id)
    {
        $item = CrServiceDefault::findOrFail($id);

        $item->delete();

        return response()
        ->json(['msg' => 'Suppression effectué']);
    }

    public function restore($id)
    {
        $restoreDataId = CrServiceDefault::withTrashed()->findOrFail($id);
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
        $item = CrServiceDefault::find($item_id);
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
        $item = CrServiceDefault::find($item_id);
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

            $item = CrServiceDefault::find($item_id);

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

    public function getAffectation(CrServiceDefault $CrServiceDefault)
    {

        return response()
        ->json(['data' => 'need to update it']);
    }
}
