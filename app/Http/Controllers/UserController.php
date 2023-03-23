<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Http\Shared\Optimus\Bruno\EloquentBuilderTrait;
use  App\Http\Shared\Optimus\Bruno\LaravelController;
use App\Models\Structure\Inscription;
use App\Services\InscriptionService;
use Illuminate\Database\Eloquent\Builder as myBuilder;
use Illuminate\Support\Facades\Cache;
use Auth;

class UserController extends LaravelController
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

    // public function getAll(Request $request)
    // {

    //     // Parse the resource options given by GET parameters
    //     $resourceOptions = $this->parseResourceOptions();

    //     $query = Inscription::query();
    //     $this->applyResourceOptions($query, $resourceOptions);

    //     if (isset($request->paginate)) {
    //         $items = $query->paginate($request->paginate);
    //         $parsedData = $items;
    //     } else {
    //         $items = $query->get();
    //         // Parse the data using Optimus\Architect
    //         $parsedData = $this->parseData($items, $resourceOptions, 'data');
    //     }

    //     // Create JSON response of parsed data
    //     return $this->response($parsedData);
    // }

    public function getAll(Request $request)
    {

        // Parse the resource options given by GET parameters
        $resourceOptions = $this->parseResourceOptions();

        $query = Inscription::query();
        $this->applyResourceOptions($query, $resourceOptions);

        if (isset($request->paginate)) {
            $items = $query->paginate($request->paginate);
            $items->setCollection($this->getUsersStatuts($items->getCollection()));
            $parsedData = $items;
        } else {
            $items = $query->get();
            $items = $this->getUsersStatuts($items);
            // Parse the data using Optimus\Architect
            $parsedData = $this->parseData($items, $resourceOptions, 'data');
        }

        // Create JSON response of parsed data
        return $this->response($parsedData);
    }

    public function sortNomComplet(myBuilder $query, $value)
    {
        $query->orderBy('inscription.prenom')->orderBy('inscription.nom');
    }

    public function restrictedModule(Request $request, $module)
    {
        $is_restricted = $request->user()->restricted_modules()->where('libelle_module', $module)->count();
        return response()->json($is_restricted);
    }

    public function filterStructureId(myBuilder $query, $method, $clauseOperator, $value)
    {
        if($value) {
            $q = $value;
            $query->whereHas('estDansStructures', function ($query) use ($q) {
                $query->where('structures.id', $q);
            });
        }
    }

    public function filterFilleulsId(myBuilder $query, $method, $clauseOperator, $value)
    {
        if ($value) {
            $q = explode(",", $value);
            $query->whereHas('visi_filleuls', function ($query) use ($q) {
                $query->whereIn('id_inscription', $q);
            });
        }
    }

    public function attachAffectation(Request $request)
    {

        $item_id = $request->id;
        $relation_name = $request->relation_name;
        $relation_id = $request->relation_id;
        $item = Inscription::find($item_id);
        $item->{$relation_name}()->syncWithoutDetaching([$relation_id => ['inscription_id_id' => 1]]);

        return response()->json([
            'message' => 'Element affecter'
        ]);
    }

    public function detachAffectation(Request $request)
    {
        $item_id = $request->id;
        $relation_name = $request->relation_name;
        $relation_id = $request->relation_id;
        $item = Inscription::find($item_id);
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

            $item = Inscription::find($item_id);

            foreach ($request->affectation as $key => $value) {
                $pivotData = array_fill(0, count($value), ['inscription_id' => 1]);
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
        $item = Inscription::findOrFail($id);
        $data['journals'] = $item->journals;
        $data['epingles'] = $item->epingles;
        return response()
            ->json(['data' => $data]);
    }

    public function getZenContactPlume($id)
    {
        $item = Inscription::findOrFail($id);
        $contact = Inscription::whereHas('zen_contact_message_recus', function ($query) {
            $query->where('inscription_id', 1);
        })->orWhereHas('zen_contact_message_envoyes', function ($query) {
            $query->where('inscription_id', 1);
        })->where('id_inscription', '!=', 1)->withCount([
            'zen_message_envoyes as messages_normal_count' => function ($query) {
                $query->where('zen_affectation_contact_message.contact', 1)
                    ->where('niveau', 1)
                    ->whereDoesntHave('vues', function ($query) {
                        $query->where('id_inscription', 1);
                    });
            },
            'zen_message_envoyes as messages_urgent_count' => function ($query) {
                $query->where('zen_affectation_contact_message.contact', 1)
                    ->where('niveau', 2)
                    ->whereDoesntHave('vues', function ($query) {
                        $query->where('id_inscription', 1);
                    });
            },
            'zen_message_envoyes as messages_tres_urgent_count' => function ($query) {
                $query->where('zen_affectation_contact_message.contact', 1)
                    ->where('niveau', 3)
                    ->whereDoesntHave('vues', function ($query) {
                        $query->where('id_inscription', 1);
                    });
            }
        ])->get();

        return response()
            ->json($contact);
    }

    public function onlineUsers() {
        // Get the array of users
        $users = Cache::get('online-users');
        if(!$users) return null;
        
        // Add the array to a collection so you can pluck the IDs
        $onlineUsers = collect($users);
        // Get all users by ID from the DB (1 very quick query)
        $dbUsers = Inscription::find($onlineUsers->pluck('id')->toArray());
        
        // Prepare the return array
        $displayUsers = [];

        // Iterate over the retrieved DB users
        foreach ($dbUsers as $user){
            // Get the same user as this iteration from the cache
            // so that we can check the last activity.
            // firstWhere() is a Laravel collection method.
            $onlineUser = $onlineUsers->firstWhere('id', $user['id']) ;
            // Append the data to the return array
            $displayUsers[] = [
                'id' => $user->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'photo' => $user->photo,
                'last_activity_at' => $onlineUser['last_activity_at'],
                // This Bool operation below, checks if the last activity
                // is older than 3 minutes and returns true or false,
                // so that if it's true you can change the status color to orange.
                'away' => $onlineUser['last_activity_at'] < now()->subMinutes(3),
            ];
        }
        return collect($displayUsers);
    }

    public function getUsersStatuts($dbUsers) {

        // Get the array of users
        $users = Cache::get('online-users');
        if(!$users) return $dbUsers;

        // Add the array to a collection so you can pluck the IDs
        $onlineUsers = collect($users);

        $dbUsers = $dbUsers->map(function($user) use ($onlineUsers) {

            // Get the same user as this iteration from the cache
            // so that we can check the last activity.
            // firstWhere() is a Laravel collection method.
            $onlineUser = $onlineUsers->firstWhere('id', $user['id']) ;

            if($onlineUser) {
                $user->last_activity_at = $onlineUser['last_activity_at'];
                $user->away = $onlineUser['last_activity_at'] < now()->subMinutes(3);
            }
            
            return $user;
            
        });
        
        return $dbUsers;
    }
}
