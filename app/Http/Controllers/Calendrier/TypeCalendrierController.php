<?php

namespace App\Http\Controllers\Calendrier;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder as myBuilder;
use App\Http\Shared\Optimus\Bruno\EloquentBuilderTrait;
use App\Http\Shared\Optimus\Bruno\LaravelController;
use App\Models\Calendrier\CalTypeCalendrier;
use Illuminate\Support\Facades\DB;
use Auth;

class TypeCalendrierController extends LaravelController
{
    use EloquentBuilderTrait;

    public function getAll(Request $request)
    {

        // Parse the resource options given by GET parameters
        $resourceOptions = $this->parseResourceOptions();

        $query = CalTypeCalendrier::query();
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

    public function filterEtablissementById(myBuilder $query, $method, $clauseOperator, $value)
    {
        if($value) {
            $query->whereHas('etablissements', function($query) use ($value){
                $query->where('id_etabli', $value );
             });
        }
    }

    public function select(Request $request)
    {
        $q = "%" .$request->search . "%";
        $item = CalTypeCalendrier::where('libelle_type', 'like',$q)->get(['id_type_postes as value','libelle_type as libelle_type']);
        return response()
        ->json($item);
    }

    public function select2(Request $request)
    {
        $q = "%" .$request->search . "%";
        $item = CalTypeCalendrier::where('libelle_type', 'like',$q)->get(['id_type_postes as id','libelle_type as text']);
        return response()
        ->json(['items' => $item]);
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
            $query->where('libelle_type', 'like', "%" .$value . "%");
        }
    }

    public function store(Request $request)
    {

        $item = CalTypeCalendrier::create([
            'inscription' => Auth::id(),
            'libelle_type' => $request->libelle_type,
            'couleur' => $request->couleur,
        ]);

        return response()
        ->json($item);
    }

    public function update(Request $request, $id)
    {

        $item = CalTypeCalendrier::findOrFail($id);

        $item->fill($request->all())->save();

        return response()
        ->json($item);
    }

    public function destroy($id)
    {
        $item = CalTypeCalendrier::findOrFail($id);

        $item->delete();

        return response()
        ->json(['msg' => 'Suppression effectué']);
    }


    public function setAffectation(Request $request)
    {
        $Batiment_id = $request->id;

        DB::beginTransaction();

        try {

            $item = CalTypeCalendrier::find($Batiment_id);

            foreach($request->affectation as $key=>$value)
            {
                $pivotData = array_fill(0, count($value), ['inscription'=> 1]);
                $syncData  = array_combine($value, $pivotData);
                $item->{$key}()->sync($syncData);
            }

            DB::commit();
        } catch (\Throwable $e) {

            DB::rollback();
            throw $e;
        }

        return response()->json([
            'message' => 'Affectation mis à jour'
        ]);
    }

    public function getAffectation(CalTypeCalendrier $batiment)
    {

        $data['amphis'] = $batiment->batiments()->get(['id_amphis as id','libelle_amphis as text']);
        $data['restaurants'] = $batiment->restaurants()->get(['id_restaurant as id','libelle_restaurant as text']);
        $data['parkings'] = $batiment->parkings()->get(['id_parking as id','libelle_parking as text']);
        $data['etablissements'] = $batiment->etablissements()->get(['id_etabli as id','nom_etabli as text']);

        return response()
        ->json(['data' => $data]);
    }

    public function attachAffectation(Request $request)
    {

        $item_id = $request->id;
        $relation_name = $request->relation_name;
        $relation_id = $request->relation_id;
        $item = CalTypeCalendrier::find($item_id);
        $item->{$relation_name}()->syncWithoutDetaching([$relation_id => ['inscription'=> Auth::id()]]);

        return response()->json([
            'message' => 'Element affecter'
        ]);
    }

    public function detachAffectation(Request $request)
    {
        $item_id = $request->id;
        $relation_name = $request->relation_name;
        $relation_id = $request->relation_id;
        $item = CalTypeCalendrier::find($item_id);
        $item->{$relation_name}()->detach($relation_id);

        return response()->json([
            'message' => 'Element Désaffecter'
        ]);
    }


}
