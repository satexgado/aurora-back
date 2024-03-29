<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 28 Dec 2021 16:27:03 +0000.
 */

namespace App\Models\Ged;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class GedFavori
 *
 * @property int $id
 * @property int $element
 * @property int $inscription
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 *
 * @property \App\Models\GedElement $ged_element
 *
 * @package App\Models
 */
class GedModele extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes;
	protected $table = 'ged_modele';

	protected $casts = [
		'inscription_id' => 'int',
		'structure_id' => 'int',
		'active' => 'bool'
	];

	protected $fillable = [
		'libelle',
		'description',
		'image',
		'allowed_type',
		'inscription_id',
		'structure_id',
		'active'
	];

	public function inscription()
	{
		return $this->belongsTo(\App\Models\Inscription::class, 'inscription_id');
	}

	public function ged_modele_form_fields()
	{
		return $this->hasMany(\App\Models\Ged\GedModeleFormField::class, 'modele_id');
	}

	public function structure()
	{
		return $this->belongsTo(\App\Models\Structure::class);
	}
}
