<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 28 Dec 2021 16:27:03 +0000.
 */

namespace App\Models\Ged;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class GedDossierAdministratif
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
class GedDossierAdministratif extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes;
	protected $table = 'ged_dossier_admistratif';

	protected $casts = [
		'inscription_id' => 'int',
		'structure_id' => 'int',
	];

	protected $fillable = [
		'libelle',
		'description',
		'inscription_id',
		'structure_id',
	];

	public function inscription()
	{
		return $this->belongsTo(\App\Models\Inscription::class, 'inscription_id');
	}

	public function structure()
	{
		return $this->belongsTo(\App\Models\Structure::class);
	}
}
