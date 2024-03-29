<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 28 Dec 2021 16:27:03 +0000.
 */

namespace App\Models\Ged;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Auth;
use Kalnoy\Nestedset\NodeTrait;

/**
 * Class Dossier
 *
 * @property int $id
 * @property string $libelle
 * @property string $description
 * @property int $conservation
 * @property int $inscription
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 *
 * @property \App\Models\GedConservationRule $ged_conservation_rule
 *
 * @package App\Models
 */
class Dossier extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes;
	use NodeTrait;
	protected $table = 'dossier';

	protected $casts = [
		'conservation' => 'int',
		'dossier_id' => 'int',
		'inscription_id' => 'int'
	];

	protected $fillable = [
		'couleur',
		'libelle',
		'description',
		'conservation',
        'dossier_id',
		'inscription_id'
	];

	protected $appends = ['nb_element', 'size', 'is_user'];

    protected $with = ['inscription', 'ged_element'];

	  
    public function getParentKeyName()
    {
        return 'dossier_id';
    }

	public function getParentIdName()
	{
		return 'dossier_id';
	}

    public function getSizeAttribute()
	{
        $sum = 0; //This specific models count
        foreach($this->fichiers as $child){
            $sum += $child->size; //Sum up the count
        }
		foreach($this->dossiers as $child){
            $sum += $child->size; //Sum up the count
        }
		return $sum;
    }

    public function getIsUserAttribute()
	{
		if(Auth::check() && $this->attributes['inscription_id'] == Auth::id())
		{
			return true;
		}
		return false;
	}

    public function getNbElementAttribute()
	{
     return $this->dossiers()->count() + $this->fichiers()->count();
    }


	public function ged_conservation_rule()
	{
		return $this->belongsTo(\App\Models\Ged\GedConservationRule::class, 'conservation_id');
	}

	public function inscription()
	{
		return $this->belongsTo(\App\Models\Inscription::class, 'inscription_id');
	}

    public function ged_element()
    {
        return $this->morphOne(\App\Models\Ged\GedElement::class, 'objet');
    }

    public function dossier()
	{
		// return $this->descendants()->with('ged_element');
		return $this->belongsTo(\App\Models\Ged\Dossier::class, 'dossier_id')->where(function($query){
			$query->where(function($query){
                $query->where('inscription_id', Auth::id());
            });  

            $query->orWhere(function($query){
                $query->whereHas('ged_element.partage_a_personnes', function($query) {
                    $query->where('ged_partage.personne', Auth::id());
					$query->where('ged_element.cacher', '!=', 1);
                 });
            });  

			$query->orWhere(function($query) {
                $query->whereHas('ancestors', function($query) {
                    $query->where(function($query){
                        $query->where('inscription_id', Auth::id());
                    });  
                    $query->orWhere(function($query){
                        $query->whereHas('ged_element.partage_a_personnes', function($query) {
                            $query->where('ged_partage.personne', Auth::id());
                            $query->where('ged_element.cacher', '!=', 1);
                         });
                    });  
                });
            });

		})->with('dossier.ged_element');
	}

	public function dossiers()
	{
		// return $this->hasMany(\App\Models\Ged\Dossier::class, 'dossier_id');
		// return $this->ancestors()->where(function($query){
		// 	$query->where(function($query){
        //         $query->where('inscription_id', Auth::id());
        //         $query->doesnthave('ged_element.structures');
        //     });  

		// 	$query->orWhere(function($query) {
        //         $query->whereHas('ancestors', function($query) {
        //             $query->whereHas('ged_element.partage_a_personnes', function($query) {
        //                 $query->where('ged_partage.personne', Auth::id());
        //              });
        //         });
        //     });

        //     $query->orWhere(function($query){
        //         $query->whereHas('ged_element.partage_a_personnes', function($query) {
        //             $query->where('ged_partage.personne', Auth::id());
        //          });
        //     });  
		// });

		return $this->hasMany(\App\Models\Ged\Dossier::class, 'dossier_id')->with('dossiers.ged_element');
	}

    public function fichiers()
	{
		return $this->belongsToMany(\App\Models\Ged\Fichier::class, 'fichier_dossier', 'dossier_id', 'fichier_id');
	}
}
