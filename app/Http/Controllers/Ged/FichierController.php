<?php

namespace App\Http\Controllers\Ged;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder as myBuilder;
use App\Http\Shared\Optimus\Bruno\EloquentBuilderTrait;
use App\Http\Shared\Optimus\Bruno\LaravelController;
use App\Models\Ged\Fichier;
use App\Models\Ged\FichierNLink;
use App\Models\Ged\DossierNLink;
use App\Models\Ged\FichierType;
use App\Models\Ged\GedElement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Facades\File;

class FichierController extends LaravelController
{
    use EloquentBuilderTrait;

    public function getAll(Request $request)
    {

        // Parse the resource options given by GET parameters
        $resourceOptions = $this->parseResourceOptions();

        $query = Fichier::query();
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

    public function filterIsInInsFolder(myBuilder $query, $method, $clauseOperator, $value, $in)
    {
        if ($value) {
            $query->whereHas('dossiers', function($query) use ($value){
                $query->where('dossier.inscription_id', Auth::id() );
             });
        }
    }

    public function filterDossierId(myBuilder $query, $method, $clauseOperator, $value, $in)
    {
        if ($value) {
            $query->whereHas('dossiers', function($query) use ($value){
                $query->where('dossier.id', $value);
             });
        }
    }
    
    public function filterStructures(myBuilder $query, $method, $clauseOperator, $value, $in)
    {
        if ($value) {
            $query->whereHas('ged_element.structures', function($query) use ($value) {
                $query->where('structures.id', $value);
            });
        }
    }

    public function filterDossierAdministratifs(myBuilder $query, $method, $clauseOperator, $value, $in)
    {
        if ($value) {
            $query->whereHas('ged_element.dossier_administratifs', function($query) use ($value) {
                $query->where('ged_dossier_admistratif.id', $value);
            });
        }
    }

    public function filterNoParent(myBuilder $query, $method, $clauseOperator, $value, $in)
    {
        if ($value) {
            $query->doesntHave('dossiers');
        }
    }

    public function filterOwnerAllHome(myBuilder $query, $method, $clauseOperator, $value, $in)
    {
        if ($value) {

            $query->where(function($query) {
                $query->whereHas('ged_element.partage_a_personnes', function($query) {
                    $query->where('ged_partage.personne', Auth::id());
                 });
                 $query->whereHas('ged_element', function($query) {
                    $query->where('ged_element.cacher', '!=', 1);
                });
            });

            $query->orWhere(function($query){
                $query->doesntHave('dossiers');
                $query->where('inscription_id', Auth::id());
                $query->doesnthave('ged_element.structures');
            });  
            
        }
    }

    public function filterOwnerMineHome(myBuilder $query, $method, $clauseOperator, $value, $in)
    {
        if ($value) {
            $query->where(function($query){
                $query->doesntHave('dossiers');
                $query->where('inscription_id', Auth::id());
                $query->doesnthave('ged_element.structures');
            });  
            
        }
    }

    public function filterOwnerSharedHome(myBuilder $query, $method, $clauseOperator, $value, $in)
    {
        if ($value) {

            $query->where(function($query) {
                $query->whereHas('ged_element.partage_a_personnes', function($query) {
                    $query->where('ged_partage.personne', Auth::id());
                 });
                 $query->whereHas('ged_element', function($query) {
                    $query->where('ged_element.cacher', '!=', 1);
                });
            });            
        }
    }

    public function filterOwnerAllParent(myBuilder $query, $method, $clauseOperator, $value, $in)
    {
        if ($value) {

            $query->doesntHave('dossiers');

            $query->where(function($query) {
                $query->where(function($query) {
                    $query->where('inscription_id', '!=',Auth::id());
                     $query->whereHas('ged_element', function($query) {
                        $query->where('ged_element.cacher', '!=', 1);
                    });
                });
                $query->orWhere(function($query){
                    $query->where('inscription_id', Auth::id()); 
                }); 
            });  
            
        }
    }

    public function filterOwnerMineParent(myBuilder $query, $method, $clauseOperator, $value, $in)
    {
        if ($value) {
            $query->doesntHave('dossiers');
            $query->where('inscription_id', Auth::id());
        }
    }

    public function filterOwnerSharedParent(myBuilder $query, $method, $clauseOperator, $value, $in)
    {
        if ($value) {
            $query->doesntHave('dossiers');
            $query->where('inscription_id', '!=',Auth::id());
            $query->whereHas('ged_element', function($query) {
                $query->where('ged_element.cacher', '!=', 1);
            });    
        }
    }

    public function filterOwnerAllFolder(myBuilder $query, $method, $clauseOperator, $value, $in)
    {
        if ($value) {
            
            $query->whereHas('dossiers', function($query) use ($value){
                $query->where('dossier.id', $value);
             });

        }
    }

    public function filterOwnerMineFolder(myBuilder $query, $method, $clauseOperator, $value, $in)
    {
        if ($value) {
            $query->whereHas('dossiers', function($query) use ($value){
                $query->where('dossier.id', $value);
             });
            $query->where('inscription_id', Auth::id());
        }
    }

    public function filterOwnerSharedFolder(myBuilder $query, $method, $clauseOperator, $value, $in)
    {
        if ($value) {
            
            $query->whereHas('dossiers', function($query) use ($value){
                $query->where('dossier.id', $value);
            });
            $query->where('inscription_id', '!=',Auth::id());

            // $query->where(function($query) {
            //     $query->where(function($query){
            //         $query->whereHas('ged_element.partage_a_personnes', function($query) {
            //             $query->where('ged_partage.personne', Auth::id());
            //          });
            //     });  
            //     $query->orWhere(function($query) {
            //         $query->whereHas('ancestors', function($query) {
            //             $query->whereHas('ged_element.partage_a_personnes', function($query) {
            //                 $query->where('ged_partage.personne', Auth::id());
            //              });
            //         });
            //     });
            // });      
        }
    }


    public function filterCourrierId(myBuilder $query, $method, $clauseOperator, $value, $in)
    {
        if ($value) {
            $query->whereHas('courriers', function($query) use ($value){
                $query->where('cr_courrier.id', $value);
             });
        }
    }

    public function filterMarcheId(myBuilder $query, $method, $clauseOperator, $value, $in)
    {
        if ($value) {
            $query->whereHas('mp_marche_etapes', function($query) use ($value){
                $query->where('mp_marche_etape.id', $value);
             });
        }
    }

    public function filterTypeId(myBuilder $query, $method, $clauseOperator, $value, $in)
    {
        if ($value) {
            $ids = explode(",", $value);
            $query->whereIn('type_id',$ids);
        }
    }

    public function filterUserFavoris(myBuilder $query, $method, $clauseOperator, $value, $in)
    {
        if ($value) {
            $query->whereHas('ged_element.ged_favoris', function($query) {
                $query->where('ged_favori.inscription_id', Auth::id());
             });
        }
    }

    public function filterSharedByUser(myBuilder $query, $method, $clauseOperator, $value, $in)
    {
        if ($value) {
            $query->whereHas('ged_element.partage_a_personnes', function($query) {
                $query->where('ged_partage.inscription_id', Auth::id());
             });
        }
    }

    public function filterSharedToUser(myBuilder $query, $method, $clauseOperator, $value, $in)
    {
        if ($value) {
            $query->whereHas('ged_element.partage_a_personnes', function($query) {
                $query->where('ged_partage.personne', Auth::id());
             });
        }
    }

    public function filterBelongToStructureId(myBuilder $query, $method, $clauseOperator, $value, $in)
    {
        if ($value) {
            $query->whereHas('ged_element.structures', function($query) use ($value) {
                $query->where('ged_element_structure.structure', $value);
             });
        }
    }

    public function filterBelongToUser(myBuilder $query, $method, $clauseOperator, $value, $in)
    {
        if ($value) {
            $query->whereHas('ged_element.ged_element_personnes', function($query) {
                $query->where('ged_element_personne.personne', Auth::id());
             });
        }
    }

    public function filterUserAsAccess(myBuilder $query, $method, $clauseOperator, $value, $in)
    {
        if ($value) {
            $query->orWhereHas('ged_element.ged_element_personnes', function($query) {
                $query->where('ged_element_personne.personne', Auth::id());
             });
             $query->orWhereHas('dossiers.ged_element.ged_element_personnes', function($query) use ($value){
                $query->where('ged_element_personne.personne', Auth::id());
             });
             $query->orWhereHas('ged_element.structures._employes', function($query) use ($value) {
                $query->where('inscription.id', Auth::id());
             });
        }
    }


    public function filterCacher(myBuilder $query, $method, $clauseOperator, $value)
    {
        if ($value && $value !='') {
            $query->whereHas('ged_element', function($query) {
                $query->where('ged_element.cacher', 1);
            });
        } else {
            $query->whereHas('ged_element', function($query) {
                $query->where('ged_element.cacher', '!=', 1);
            });
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $path = $request->file('fichier')->store('document/'.date('Y').'/'.date('F'));
            // $nameonly=preg_replace('/\..+$/', '', $request->file('fichier')->getClientOriginalName());
            $n = strrpos($path,".");
            $extension = ($n===false) ? "" : substr($path,$n+1);
            $file = FichierType::where('extension','like', '%'.$extension.'%')->orWhere('extension','other')->first();
            $item = Fichier::create([
                'inscription_id' => Auth::id(),
                'libelle' => $request->libelle,
                'type_id' => $file->id,
                'fichier' => $path,
            ]);

            $element = new GedElement();
            $item->ged_element()->save($element);
            if($request->has('relation_name') && $request->has('relation_id')) {
                $relation_name = $request->relation_name;
                $relation_id = $request->relation_id;
                $item->{$relation_name}()->syncWithoutDetaching([$relation_id => ['inscription_id'=> Auth::id()]]);
            }

            if($request->has('gedRelation_name') && $request->has('gedRelation_id')) {
                $gedRelation_name = $request->gedRelation_name;
                $gedRelation_id = $request->gedRelation_id;
                $item->ged_element->{$gedRelation_name}()->syncWithoutDetaching([$gedRelation_id => ['inscription_id'=> Auth::id()]]);
            }
        DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }

        return response()
        ->json($item->load(['fichier_type', 'inscription', 'ged_element']));

    }

    public function multistore(Request $request)
    {
        DB::beginTransaction();

        try {
            if($request->fichier_count) {
                for($i =0; $i<$request->fichier_count; $i++) {
                    if($request->hasFile('fichier'.$i))
                    {
                        $path = $request->file('fichier'.$i)->store('document/'.date('Y').'/'.date('F'));
                        // $nameonly=preg_replace('/\..+$/', '', $request->file('fichier'.$i)->getClientOriginalName());
                        $n = strrpos($path,".");
                        $extension = ($n===false) ? "" : substr($path,$n+Auth::id());
                        $file = FichierType::where('extension','like', '%'.$extension.'%')->orWhere('extension','other')->first();
                        $fichier = Fichier::create([
                            'inscription' => Auth::id(),
                            'libelle' => $request->libelle,
                            'type' => $file->id,
                            'fichier' => $path,
                        ]);
                        $element = new GedElement();
                        $fichier->ged_element()->save($element);

                        if($request->has('relation_name') && $request->has('relation_id')) {
                            $relation_name = $request->relation_name;
                            $relation_id = $request->relation_id;
                            $fichier->{$relation_name}()->syncWithoutDetaching([$relation_id => ['inscription_id'=> Auth::id()]]);
                        }
                        if($request->has('gedRelation_name') && $request->has('gedRelation_id')) {
                            $gedRelation_name = $request->gedRelation_name;
                            $gedRelation_id = $request->gedRelation_id;
                            $fichier->ged_element->{$gedRelation_name}()->syncWithoutDetaching([$gedRelation_id => ['inscription_id'=> Auth::id()]]);
                        }
                    
                    }
                }
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }

        return response()
        ->json(['msg' => 'Suppression effectué']);
    }

    public function update(Request $request, $id)
    {

        $item = Fichier::findOrFail($id);

        $data = $request->all();

        $item->fill($data)->save();

        return response()
        ->json($item->load(['fichier_type', 'inscription', 'ged_element']));
    }

    public function destroy($id)
    {
        $item = FichierNLink::findOrFail($id);

        $item->delete();

        return response()
        ->json(['msg' => 'Suppression effectué']);
    }

    public function restore($id)
    {
        $restoreDataId = Fichier::withTrashed()->findOrFail($id);
        if($restoreDataId && $restoreDataId->trashed()){
           $restoreDataId->restore();
        }
        return response()
        ->json($restoreDataId->load(['fichier_type', 'inscription', 'ged_element']));
    }

    public function checkPassword(Request $request, $id) {
        $item = Fichier::findOrFail($id)->makeVisible(['password']);
        if(Hash::check($request->password, $item->ged_element->password)) {
            $item->bloquer = 0;
            $item->ged_element->bloquer = 0;
            return response()
            ->json($item);
        }

        return response()
            ->json(false);
    }

    public function attachAffectation(Request $request)
    {

        $item_id = $request->id;
        $relation_name = $request->relation_name;
        $relation_id = $request->relation_id;
        $item = Fichier::find($item_id);
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
        $item = Fichier::find($item_id);
        $item->{$relation_name}()->detach($relation_id);

        return response()->json([
            'message' => 'Element Désaffecter'
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
        $item = Fichier::find($id);

        $result = null;

        foreach($affectation as $key=>$value)
        {
            $pivotData = array_fill(0, count($value), ['inscription_id'=> Auth::id()]);
            $syncData  = array_combine($value, $pivotData);
            $result = $item->{$key}()->sync($syncData);
        }

        return $result;
    }
    public function getAffectation(Fichier $Fichier)
    {

        return response()
        ->json(['data' => 'need to update it']);
    }

    public function dowloadZip(Request $request)
    {

        $zip = new ZipArchive;
            $timeName = time();
            $zipFileName = $timeName . '.zip';
            $zipPath = asset($zipFileName);
            if ($zip->open(($zipFileName), ZipArchive::CREATE) === true) {
                 // Add File in ZipArchive

                $result = FichierNLink::whereIn('id', $request->all())->get();
                foreach($result as $element)
                {
                    $filename = $element->libelle;
                    if(File::extension($filename) !=  File::extension($element->fichier)) {
                        $filename = $filename.".".File::extension($element->fichier);
                    }

                    if(Storage::disk('public')->exists($element->fichier)) {
                        if (!$zip->addFile("storage/public/".$element->fichier, $filename)) {
                            echo 'Could not add file to ZIP: ' . $element->libelle;
                        }
                    }
                }
    
                $zip->close();

                if ($zip->open($zipFileName) === true) {
                    return response()->download($zipFileName)->deleteFileAfterSend(true);
                } else {
                    return false;
                }
            }
    }

    public function dowloadFolder(Request $request, $id)
    {
        $item = DossierNLink::findOrFail($id);
        $zip = new ZipArchive;
        $timeName = time();
        $zipFileName = $timeName . '.zip';
        $zipPath = asset($zipFileName);
        if ($zip->open(($zipFileName), ZipArchive::CREATE) === true) {
                // Add File in ZipArchive
            $this->addDossierToZip($item, $zip);

            $zip->close();

            if ($zip->open($zipFileName) === true) {
                return response()->download($zipFileName)->deleteFileAfterSend(true);
            } else {
                return false;
            }
        }
    }

    public function addDossierToZip(DossierNLink $dossier, ZipArchive $zip, $parentLink = "") {
       $fichiers = $dossier->fichiers()->get();
       $dossiers = $dossier->dossiers()->get();

        foreach($fichiers as $element)
        {
            $filename = $parentLink.$dossier->libelle."/".$element->libelle;
            if(File::extension($filename) !=  File::extension($element->fichier)) {
                $filename = $filename.".".File::extension($element->fichier);
            }

            if(Storage::disk('public')->exists($element->fichier)) {
                if (!$zip->addFile("storage/public/".$element->fichier, $filename)) {
                    echo 'Could not add file to ZIP: ' . $element->libelle;
                }
            }
        }

        foreach($dossiers as $element)
        {
            $this->addDossierToZip($element, $zip, $parentLink.$dossier->libelle."/");
        }
    
    }
}
