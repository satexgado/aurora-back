<?php

namespace App\Http\Controllers\Calendrier;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder as myBuilder;
use App\Http\Shared\Optimus\Bruno\EloquentBuilderTrait;
use App\Http\Shared\Optimus\Bruno\LaravelController;
use App\Models\Calendrier\CalCalendrier;
use App\Models\Calendrier\VisiCahierAssistance;
use Illuminate\Support\Facades\DB;
use Auth;
use Carbon\Carbon;

class CalendrierController extends LaravelController
{
    use EloquentBuilderTrait;

    public function getAll(Request $request)
    {

        // Parse the resource options given by GET parameters
        $resourceOptions = $this->parseResourceOptions();

        $query = CalCalendrier::query();
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

    public function getMorphClass($name) {

        switch ($name) {
            case 'cahier':
                return (new VisiCahierAssistance())->getMorphClass();
            default:
                return false;
        }
    }

    public function filterEtablissementById(myBuilder $query, $method, $clauseOperator, $value)
    {
        if($value) {
            $query->whereHas('etablissements', function($query) use ($value){
                $query->where('id_etabli', $value );
             });
        }
    }

    public function filterMatiereId(myBuilder $query, $method, $clauseOperator, $value, $in)
    {
        if ($value) {
            $query->whereHas('visi_matieres', function($query) use ($value){
                $query->where('id_matiere', $value );
             });
        }
    }

    public function select(Request $request)
    {
        $q = "%" .$request->search . "%";
        $item = CalCalendrier::where('type', 'like',$q)->get(['id_type_postes as value','type as type']);
        return response()
        ->json($item);
    }

    public function select2(Request $request)
    {
        $q = "%" .$request->search . "%";
        $item = CalCalendrier::where('type', 'like',$q)->get(['id_type_postes as id','type as text']);
        return response()
        ->json(['items' => $item]);
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
            $query->where('libelle', 'like', "%" .$value . "%");
        }
    }

    public function store(Request $request)
    {

        if($request->exists('repeter') && $request->repeter)
        {
            $item = CalCalendrier::create([
                'inscription_id' => Auth::id(),
                'type' => $request->type,
                'lieu' => $request->lieu,
                'rrule' => $request->rrule,
                'duration' => $request->duration,
                'all_day' => $request->all_day,
                'description' => $request->description,
                'libelle' => $request->libelle,
                'affectable_type' => $request->affectable_type,
                'affectable_id' => $request->affectable_id,
            ]);
        } else {
            $date_debut = new Carbon($request->date_debut);
            $date_fin = new Carbon($request->date_fin);

            $item = CalCalendrier::create([
                'inscription_id' => Auth::id(),
                'type' => $request->type,
                'lieu' => $request->lieu,
                'date_debut' => $date_debut,
                'date_fin' => $date_fin,
                'all_day' => $request->all_day,
                'description' => $request->description,
                'libelle' => $request->libelle,
                'affectable_type' => $request->affectable_type,
                'affectable_id' => $request->affectable_id,
            ]);

        }

        if($request->exists('relation_name'))
        {
            $relation_name = $this->getMorphClass($request->relation_name) ;
            if($relation_name) {
                $item->affectable_type = $relation_name;
                $item->affectable_id = $request->relation_id;
                $item->save();
            }
        }


        if($request->exists('participants'))
        {
            $json = utf8_encode($request->participants);
            $participants = json_decode($json);
            if(!is_array($participants))
            {
                $participants = explode(',', $participants);
            }
            $pivotData = array_fill(0, count($participants), ['inscription_id'=> 1]);
            $attachData  = array_combine($participants, $pivotData);
            $item->participants()->attach($attachData);
        }

        if($request->exists('personnels'))
        {
            $json = utf8_encode($request->personnels);
            $personnels = json_decode($json);
            if(!is_array($personnels))
            {
                $personnels = explode(',', $personnels);
            }
            $pivotData = array_fill(0, count($personnels), ['inscription_id'=> 1]);
            $attachData  = array_combine($personnels, $pivotData);
            $item->personnels()->attach($attachData);
        }

        if($request->exists('matieres'))
        {
            $json = utf8_encode($request->matieres);
            $matieres = json_decode($json);
            if(!is_array($matieres))
            {
                $matieres = explode(',', $matieres);
            }
            $pivotData = array_fill(0, count($matieres), ['inscription_id'=> 1]);
            $attachData  = array_combine($matieres, $pivotData);
            $item->visi_matieres()->attach($attachData);
        }

        if($request->exists('matiere_id')) {
            $item->visi_matieres()->attach($request->matiere_id);
        }
        return response()
        ->json($item->load('cal_type_calendrier','participants'
        // 'visi_matieres',
        // 'personnels.user', 'personnels.visi_masque_personnel', 'personnels.visi_poste', 'personnels.visi_matieres'
        ));
    }

    public function update(Request $request, $id)
    {


        $data = $request->all();
        $item = CalCalendrier::findOrFail($id);
        $item->fill($data)->save();

        if($request->exists('participants'))
        {
            $json = utf8_encode($request->participants);
            $participants = json_decode($json);
            if(!is_array($participants))
            {
                $participants = explode(',', $participants);
            }

            $pivotData = array_fill(0, count($participants), ['inscription_id'=> 1]);
            $syncData  = array_combine($participants, $pivotData);
            $item->participants()->sync($syncData);
        }

        if($request->exists('personnels'))
        {
            $json = utf8_encode($request->personnels);
            $personnels = json_decode($json);
            if(!is_array($personnels))
            {
                $personnels = explode(',', $personnels);
            }

            $pivotData = array_fill(0, count($personnels), ['inscription_id'=> 1]);
            $syncData  = array_combine($personnels, $pivotData);
            $item->personnels()->sync($syncData);
        }

        if($request->exists('matieres'))
        {
            $json = utf8_encode($request->matieres);
            $matieres = json_decode($json);
            if(!is_array($matieres))
            {
                $matieres = explode(',', $matieres);
            }

            $pivotData = array_fill(0, count($matieres), ['inscription_id'=> 1]);
            $syncData  = array_combine($matieres, $pivotData);
            $item->visi_matieres()->sync($syncData);
        }

        if($request->exists('matiere_id')) {
            $item->visi_matieres()->sync($request->matiere_id);
        }


        return response()
        ->json($item->load('cal_type_calendrier', 'participants'
        // , 'visi_matieres',
        // 'personnels.user', 'personnels.visi_masque_personnel', 'personnels.visi_poste', 'personnels.visi_matieres'
        ));
    }

    public function destroy($id)
    {
        $item = CalCalendrier::findOrFail($id);

        $item->delete();

        return response()
        ->json(['msg' => 'Suppression effectué']);
    }


    public function setAffectation(Request $request)
    {
        $Batiment_id = $request->id;

        DB::beginTransaction();

        try {

            $item = CalCalendrier::find($Batiment_id);

            foreach($request->affectation as $key=>$value)
            {
                $pivotData = array_fill(0, count($value), ['inscription_id'=> 1]);
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

    public function getAffectation(CalCalendrier $batiment)
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
        $item = CalCalendrier::find($item_id);
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
        $item = CalCalendrier::find($item_id);
        $item->{$relation_name}()->detach($relation_id);

        return response()->json([
            'message' => 'Element Désaffecter'
        ]);
    }


}
