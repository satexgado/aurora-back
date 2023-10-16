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
 * @property \App\Models\GedWorkspace $ged_workspace
 *
 * @package App\Models
 */
class GedWorkspace extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes;
	protected $table = 'ged_workspace';

	protected $casts = [
		'inscription_id' => 'int',
		'structure_id' => 'int',
		'public' => 'bool'
	];

	protected $fillable = [
		'libelle',
		'description',
		'image',
		'inscription_id',
		'structure_id',
		'public'
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
