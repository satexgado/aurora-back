<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 28 Dec 2021 16:27:03 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class TypeStructure
 * 
 * @property int $id
 * @property string $libelle
 * @property string $description
 * @property int $inscription
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * 
 * @property \Illuminate\Database\Eloquent\Collection $structures
 *
 * @package App\Models
 */
class TypeStructure extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes;

	protected $casts = [
		'inscription' => 'int'
	];

	protected $fillable = [
		'libelle',
		'description',
		'inscription'
	];

	public function inscription()
	{
		return $this->belongsTo(\App\Models\Inscription::class, 'inscription');
	}

	public function structures()
	{
		return $this->hasMany(\App\Models\Structure::class, 'type');
	}
}
