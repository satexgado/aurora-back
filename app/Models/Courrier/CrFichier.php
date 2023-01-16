<?php

/**
 * Created by Reliese Model.
 * Date: Fri, 07 Jan 2022 03:13:45 +0000.
 */

namespace App\Models\Courrier;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class CrFichier
 * 
 * @property int $id
 * @property int $fichier_id
 * @property int $courrier_id
 * @property int $inscription_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * 
 * @property \App\Models\Courrier\CrCourrier $cr_courrier
 * @property \App\Models\Fichier $fichier
 * @property \App\Models\Inscription $inscription
 *
 * @package App\Models
 */
class CrFichier extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes;
	protected $table = 'cr_fichier';

	protected $casts = [
		'fichier_id' => 'int',
		'courrier_id' => 'int',
		'inscription_id' => 'int'
	];

	protected $fillable = [
		'fichier_id',
		'courrier_id',
		'inscription_id'
	];

	public function cr_courrier()
	{
		return $this->belongsTo(\App\Models\Courrier\CrCourrier::class, 'courrier_id');
	}

	public function fichier()
	{
		return $this->belongsTo(\App\Models\Fichier::class);
	}

	public function inscription()
	{
		return $this->belongsTo(\App\Models\Inscription::class);
	}
}
