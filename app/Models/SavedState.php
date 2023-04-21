<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 28 Dec 2021 16:27:03 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class AffectationStructure
 *
 * @property int $id
 * @property int $user
 * @property int $structure
 * @property int $fonction
 * @property int $droit_acces
 * @property int $inscription
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 *
 * @property \App\Models\DroitAcce $droit_acce
 *
 * @package App\Models
 */
class SavedState extends Eloquent
{
	// use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'saved_state';

	protected $casts = [
		'inscription_id' => 'int'
	];

	protected $fillable = [
		'libelle',
		'module',
		'state',
		'inscription_id'
	];

    public function inscription()
	{
		return $this->belongsTo(\App\Models\Inscription::class, 'inscription_id');
	}

}
