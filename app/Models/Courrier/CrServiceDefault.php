<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 23 Jan 2023 13:26:21 +0000.
 */

namespace App\Models\Courrier;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class CrServiceDefault
 *
 * @property int $id
 * @property int $inscription_id
 * @property int $structure_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 *
 * @property \App\Models\Inscription $inscription
 * @property \App\Models\Structure $structure
 * @property \Illuminate\Database\Eloquent\Collection $cr_affectation_service_default_personnes
 *
 * @package App\Models
 */
class CrServiceDefault extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes;
	protected $table = 'cr_service_default';

	protected $casts = [
		'inscription_id' => 'int',
		'structure_id' => 'int'
	];

	protected $fillable = [
		'inscription_id',
		'structure_id'
	];

    protected $with = [
        'structure',
        'personnes'
    ];

	public function inscription()
	{
		return $this->belongsTo(\App\Models\Inscription::class);
	}

	public function structure()
	{
		return $this->belongsTo(\App\Models\Structure::class);
	}

	public function cr_affectation_service_default_personnes()
	{
		return $this->hasMany(\App\Models\CrAffectationServiceDefaultPersonne::class, 'service_default');
	}

    public function personnes()
	{
		return $this->belongsToMany(\App\Models\Inscription::class, 'cr_affectation_service_default_personne', 'service_default', 'personne');
	}

}
