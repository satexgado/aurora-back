<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Http\Shared\Optimus\Bruno\EloquentBuilderTrait;
use  App\Http\Shared\Optimus\Bruno\LaravelController;
use App\Models\SavedState;
use App\Services\InscriptionService;
use Illuminate\Database\Eloquent\Builder as myBuilder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Auth;

class SavedStateController extends LaravelController
{
    use EloquentBuilderTrait;
    public  InscriptionService $service;


    public function __construct(InscriptionService $service)
    {
        // parent::__construct();
        $this->service = $service;
    }


    public function show($id)
    {
        return $this->service->show($id);
    }

    public function getAll(Request $request)
    {

        // Parse the resource options given by GET parameters
        $resourceOptions = $this->parseResourceOptions();

        $query = SavedState::query();
        $this->applyResourceOptions($query, $resourceOptions);

        if (isset($request->paginate)) {
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

    public function store(Request $request)
    {

        $item = SavedState::create([
            'inscription_id' => Auth::id(),
            'libelle' => $request->libelle,
            'module' => $request->module,
            'state' => $request->state,
        ]);

        return response()
        ->json($item);
    }

    public function update(Request $request, $id)
    {

        $item = SavedState::findOrFail($id);

        $data = $request->all();

        $item->fill($data)->save();

        return response()
        ->json($item);
    }

    public function destroy($id)
    {
        $item = SavedState::findOrFail($id);

        $item->delete();

        return response()
        ->json(['msg' => 'Suppression effectué']);
    }

    public function attachAffectation(Request $request)
    {

        $item_id = $request->id;
        $relation_name = $request->relation_name;
        $relation_id = $request->relation_id;
        $item = SavedState::find($item_id);
        $item->{$relation_name}()->syncWithoutDetaching([$relation_id => ['inscription_id' => 1]]);

        return response()->json([
            'message' => 'Element affecter'
        ]);
    }

    public function detachAffectation(Request $request)
    {
        $item_id = $request->id;
        $relation_name = $request->relation_name;
        $relation_id = $request->relation_id;
        $item = SavedState::find($item_id);
        $item->{$relation_name}()->detach($relation_id);

        return response()->json([
            'message' => 'Element Désaffecter'
        ]);
    }


    public function setAffectation(Request $request)
    {
        $item_id = $request->id;
        $attached = [];
        $detached = [];
        $result;

        DB::beginTransaction();

        try {

            $item = SavedState::find($item_id);

            foreach ($request->affectation as $key => $value) {
                $pivotData = array_fill(0, count($value), ['inscription_id' =>  Auth::id()]);
                $syncData  = array_combine($value, $pivotData);
                $result = $item->{$key}()->sync($syncData);
                $detached = $result['detached'];
                $attached = $result['attached'];
            }

            DB::commit();
        } catch (\Throwable $e) {

            DB::rollback();
            throw $e;
        }

        return response()->json([
            'message' => 'Affectation mis à jour',
            'attached' => $attached,
            'detached' => $detached,
            'result' => $result
        ]);
    }

    public function getAffectation($id)
    {
        $item = SavedState::findOrFail($id);
        $data['journals'] = $item->journals;
        $data['epingles'] = $item->epingles;
        return response()
            ->json(['data' => $data]);
    }

}
