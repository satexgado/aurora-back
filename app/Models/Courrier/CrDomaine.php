<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 28 Dec 2021 16:27:03 +0000.
 */

namespace App\Models\Courrier;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class CrDomaine
 *
 * @property int $id
 * @property string $libelle
 * @property int $inscription
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 *
 * @property \Illuminate\Database\Eloquent\Collection $cr_courriers
 *
 * @package App\Models
 */
class CrDomaine extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes;
	protected $table = 'cr_domaine';

	protected $casts = [
		'inscription_id' => 'int',
		'priorite' => 'int'
	];

	protected $fillable = [
		'libelle',
		'description',
        'priorite',
		'inscription_id'
	];

	public function inscription()
	{
		return $this->belongsTo(\App\Models\Inscription::class, 'inscription_id');
	}

	public function cr_courriers()
	{
		return $this->hasMany(\App\Models\Courrier\CrCourrier::class, 'domaine_id');
	}

}
