<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 28 Dec 2021 16:27:03 +0000.
 */

namespace App\Models\Ged;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

/**
 * Class Fichier
 *
 * @property int $id
 * @property int $type
 * @property string $libelle
 * @property string $fichier
 * @property int $inscription_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 *
 * @property \App\Models\FichierType $fichier_type
 * @property \Illuminate\Database\Eloquent\Collection $cr_fichiers
 *
 * @package App\Models
 */
class FichierNLink extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes;
	protected $table = 'fichier';

	protected $casts = [
		'type_id' => 'int',
		'inscription_id' => 'int'
	];

	protected static function boot()
	{
		parent::boot();
		
		static::deleted(function($item){
			if(Storage::disk('public')->exists($item->fichier)){
				Storage::disk('public')->delete($item->fichier);
				return response()
				->json(['msg' => 'Suppression effectué2']);
			}
	
			
		});
	}


	protected $fillable = [
		'type_id',
		'libelle',
		'fichier',
		'inscription_id'
	];

    public function toJson($options = 0) {
        $this->setClassifiedVisibility();
        return parent::toJson();
    }

    public function toArray() {
        $this->setClassifiedVisibility();
        return parent::toArray();
    }


	public function inscription()
	{
		return $this->belongsTo(\App\Models\Inscription::class, 'inscription_id');
	}

	public function fichier_type()
	{
		return $this->belongsTo(\App\Models\Ged\FichierType::class, 'type_id');
	}

	public function cr_fichiers()
	{
		return $this->hasMany(\App\Models\Courrier\CrFichier::class, 'fichier');
	}

    public function ged_element()
    {
        return $this->morphOne(\App\Models\Ged\GedElement::class, 'objet');
    }

    public function dossiers()
	{
		return $this->belongsToMany(\App\Models\Ged\Dossier::class, 'fichier_dossier', 'fichier_id', 'dossier_id');
	}

    public function courriers()
	{
		return $this->belongsToMany(\App\Models\Courrier\CrCourrier::class, 'cr_fichier', 'fichier_id', 'courrier_id');
	}

    public function dossier()
	{
		return $this->hasOneThrough(\App\Models\Ged\Dossier::class, 'fichier_dossier', 'fichier_id', 'dossier.id', 'fichier.id', 'dossier_id');
    }

    public function cr_commentaires()
	{
		return $this->belongsToMany(\App\Models\Courrier\CrCommentaire::class, 'cr_affectation_commentaire_fichier', 'fichier', 'commentaire');
	}

    public function cr_mails()
	{
		return $this->belongsToMany(\App\Models\Courrier\CrMail::class, 'cr_affectation_mail_fichier', 'fichier', 'mail');
	}

    public function mp_marche_etapes()
	{
		return $this->belongsToMany(\App\Models\MarchePublic\MpMarcheEtape::class, 'mp_affectation_marche_fichier', 'fichier', 'marche');
	}
}
