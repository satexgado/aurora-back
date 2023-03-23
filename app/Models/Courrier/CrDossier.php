<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 07 Jul 2022 09:38:18 +0000.
 */

namespace App\Models\Courrier;

use Illuminate\Database\Eloquent\Model as Eloquent;
use App\Models\Relations\HasManySyncable;

/**
 * Class CrDossier
 *
 * @property int $id
 * @property string $libelle
 * @property string $objet
 * @property int $structure_id
 * @property int $responsable_id
 * @property int $inscription_id
 * @property \Carbon\Carbon $date_cloture
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 *
 * @property \App\Models\Inscription $inscription
 * @property \App\Models\Structure $structure
 * @property \Illuminate\Database\Eloquent\Collection $cr_courrier_dossiers
 *
 * @package App\Models
 */
class CrDossier extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes;
	protected $table = 'cr_dossier';

	protected $casts = [
		'structure_id' => 'int',
		'responsable_id' => 'int',
		'inscription_id' => 'int'
	];

	protected $dates = [
		'date_cloture'
	];

	protected $fillable = [
		'libelle',
		'objet',
		'structure_id',
		'responsable_id',
		'inscription_id',
		'date_cloture'
	];

    //Make it available in the json response
	protected $appends = ['nb_courrier_entrants','nb_courrier_sortants','nb_courrier_internes'];

    public function getNbCourrierEntrantsAttribute()
    {
        return $this->cr_courrier_entrants()->whereHas('cr_provenance', function($query) {
            $query->where('cr_provenance.externe',1);
        })->count();
    }

    public function getNbCourrierSortantsAttribute()
    {
        return $this->cr_courrier_sortants()->count();
    }

    public function getNbCourrierInternesAttribute()
    {
        return $this->cr_courrier_entrants()->whereHas('cr_provenance', function($query) {
            $query->where('cr_provenance.externe',0);
        })->count();
    }

	public function inscription()
	{
		return $this->belongsTo(\App\Models\Inscription::class, 'responsable_id');
	}

    public function responsable_inscription()
	{
		return $this->belongsTo(\App\Models\Inscription::class, 'responsable_id');
	}

	public function structure()
	{
		return $this->belongsTo(\App\Models\Structure::class);
	}

	public function cr_courrier_dossiers()
	{
		return $this->hasMany(\App\Models\Courrier\CrCourrierDossier::class, 'dossier_id');
	}

    public function cr_courriers()
	{
		return $this->hasMany(\App\Models\Dash\CrCourrier::class, 'dossier_id');
	}

    public function cr_courrier_entrants()
    {
        return $this->hasManyThrough(\App\Models\Courrier\CrCourrierEntrant::class, \App\Models\Courrier\CrCourrier::class, 'dossier_id', 'courrier_id');
    }

    public function cr_courrier_sortants()
    {
        return $this->hasManyThrough(\App\Models\Courrier\CrCourrierSortant::class, \App\Models\Courrier\CrCourrier::class, 'dossier_id', 'courrier_id');
    }

    public function cr_reaffected_inscriptions()
    {
        return $this->belongsToMany(\App\Models\Inscription::class, 'cr_reaffectation', 'courrier_id', 'suivi_par');
    }

	/**
     * Overrides the default Eloquent hasMany relationship to return a HasManySyncable.
     *
     * {@inheritDoc}
     * @return \App\Model\Relations\HasManySyncable
     */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $instance = $this->newRelatedInstance($related);

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $localKey = $localKey ?: $this->getKeyName();

        return new HasManySyncable(
            $instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey
        );
    }
}
