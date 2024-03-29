<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 28 Dec 2021 16:27:03 +0000.
 */

namespace App\Models\Ged;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Auth;

/**
 * Class GedElement
 *
 * @property int $id
 * @property bool $actif
 * @property bool $cacher
 * @property bool $bloquer
 * @property string $password
 * @property string $objet_type
 * @property int $objet_id
 * @property \Carbon\Carbon $archivated_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 *
 * @property \Illuminate\Database\Eloquent\Collection $ged_element_personnes
 * @property \Illuminate\Database\Eloquent\Collection $structures
 * @property \Illuminate\Database\Eloquent\Collection $ged_favoris
 * @property \Illuminate\Database\Eloquent\Collection $ged_partages
 *
 * @package App\Models
 */
class GedElement extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes;
	protected $table = 'ged_element';

	protected $casts = [
		'actif' => 'bool',
		'cacher' => 'bool',
		'bloquer' => 'bool',
		'objet_id' => 'int'
	];

	protected $dates = [
		'archivated_at'
	];

	protected $hidden = [
		'password'
	];

	protected $fillable = [
		'actif',
		'cacher',
		'bloquer',
		'password',
		'objet_type',
		'objet_id',
		'archivated_at'
	];

	protected $appends = ['user_favoris', 'comments_count'];
    protected $with = ['ged_partages.personne_inscription'];

    protected $classified = ['password'];

    public function getUserFavorisAttribute()
	{

		if(Auth::check())
		{
			return $this->favoris()->where('inscription_id', Auth::id())->count();
		}
		return false;
    }

    public function getCommentsCountAttribute()
	{
		return $this->cr_commentaires()->count();
    }

	public function ged_element_personnes()
	{
		return $this->hasMany(\App\Models\Ged\GedElementPersonne::class, 'element');
	}

	public function structures()
	{
		return $this->belongsToMany(\App\Models\Structure::class, 'ged_element_structure', 'element', 'structure')
					->withPivot('id', 'inscription', 'deleted_at')
					->withTimestamps();
	}

	public function dossier_administratifs()
	{
		return $this->belongsToMany(\App\Models\Ged\GedDossierAdministratif::class, 'ged_element_dossier_administratif', 'element', 'dossier_administratif')
					->withPivot('id', 'inscription', 'deleted_at')
					->withTimestamps();
	}

	public function ged_modeles()
	{
		return $this->belongsToMany(\App\Models\Ged\GedModele::class, 'ged_element_modele', 'element', 'modele')
					->withPivot('id', 'inscription', 'deleted_at')
					->withTimestamps();
	}

	public function workspace_users()
	{
		return $this->belongsToMany(\App\Models\Ged\GedWorkspaceUser::class, 'ged_element_workspace_users', 'element', 'workspace_users')
					->withPivot('id', 'inscription', 'deleted_at')
					->withTimestamps();
	}

	public function coordonnee_linked_workspaces()
	{
		return $this->belongsToMany(\App\Models\Courrier\CrCoordonnee::class, 'ged_element_workspace_coordonnee', 'element', 'coordonnee')
					->withPivot('id', 'inscription', 'deleted_at', 'workspace')
					->withTimestamps();
	}

	public function ged_favoris()
	{
		return $this->hasMany(\App\Models\Ged\GedFavori::class, 'element');
	}

    public function favoris()
	{
		return $this->belongsToMany(\App\Models\Inscription::class, 'ged_favori', 'element', 'inscription_id');
    }

	public function ged_partages()
	{
		return $this->hasMany(\App\Models\Ged\GedPartage::class, 'element');
	}

    public function partage_a_personnes()
	{
		return $this->belongsToMany(\App\Models\Inscription::class, 'ged_partage', 'element', 'personne');
	}

    public function objet()
    {
        return $this->morphTo();
    }

    public function cr_commentaires()
	{
		return $this->belongsToMany(\App\Models\Courrier\CrCommentaire::class, 'ged_affectation_commentaire_element', 'element', 'commentaire');
	}
}
