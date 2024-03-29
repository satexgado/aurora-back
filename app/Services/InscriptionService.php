<?php

namespace App\Services;

use App\ApiRequest\ApiRequest;
use App\Models\Structure\Inscription;
use App\Services\BaseService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Swift_TransportException;

class InscriptionService extends BaseService
{

    public function __construct(Inscription $model)
    {
        parent::__construct($model);
    }


    public function employees(ApiRequest $request)
    {
        return $this->model::with(['affectation_structures.structure', 'affectation_structures.fonctions', 'affectation_structures.poste', 'affectation_structures.role'])->consume($request);
    }


    public function validate(Request $request)
    {
        $request->validate([
            'prenom' => 'required',
            'nom' => 'required',
            'date_naissance' => 'required|date',
            'lieu_naissance' => 'required',
            'telephone' => 'required',
            'identifiant' => 'required',
            'sexe' => 'required|in:HOMME,FEMME',
            'email' => 'required|email|unique:inscription,email',
            'photo' => 'sometimes|image',
        ]);
    }

    public function getByRole($role)
    {
        return $this->model::with(['affectation_structures.fonctions', 'affectation_structures.structure', 'affectation_structures.poste', 'affectation_structures.role'])->whereHas('roles', function ($q) use ($role) {
            return $q->where('roles.id', $role);
        })->consume(null);
    }


    // TODO add image thumbnail to load image faster
    public function add(Request $request)
    {

        $inscription = Inscription::create($request->except('photo') + ['inscription' => Auth::id()]);


        // TOFO
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $imagePath = $file->storeAs('inscription/' . $inscription->email . '/photo', $file->getClientOriginalName(), 'local');
            $inscription->update(['photo' => $imagePath]);
        }

        try {
            $inscription->sendEmailVerificationNotification();
        } catch (Exception $e) {
            $inscription->forceDelete();
            throw new Swift_TransportException($e);
        }


        return $inscription;
    }



    public function show(int $id)
    {
        return $this->model::with(['affectation_structures.structure', 'affectation_structures.fonctions', 'affectation_structures.poste', 'affectation_structures.role'])->findOrFail($id);
    }

    public function getAutresStructures(Request $request)
    {
        return $this->model::consume($request);
    }


    // TODO delete image on update
    public function edit(Request $request, int $id)
    {

        $inscription = Inscription::findOrFail($id);

        $inscription->update($request->except('photo'));


        // TOFO
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $imagePath = $file->storeAs('inscription/' . $inscription->email . '/photo', $file->getClientOriginalName(), 'local');
            $inscription->update(['photo' => $imagePath]);

            // Delete previous image
            if (Storage::exists($inscription->photo)) {
                Storage::delete($inscription->photo);
            }
        }


        return $inscription->refresh();
    }
}
