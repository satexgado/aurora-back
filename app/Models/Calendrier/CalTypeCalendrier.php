<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 27 Oct 2020 10:43:22 +0000.
 */

namespace App\Models\Calendrier;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class CalTypeCalendrier
 *
 * @property int $id_type_calendrier
 * @property string $libelle_type
 * @property int $couleur
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $inscription
 *
 * @property \App\Models\CptInscription $cpt_inscription
 * @property \Illuminate\Database\Eloquent\Collection $cal_calendriers
 *
 * @package App\Models
 */
class CalTypeCalendrier extends Eloquent
{
	protected $table = 'cal_type_calendrier';
	protected $primaryKey = 'id_type_calendrier';

	protected $casts = [
		'inscription_id' => 'int'
	];

	protected $fillable = [
		'libelle_type',
		'couleur',
		'inscription_id'
	];

	public function cpt_inscription()
	{
		return $this->belongsTo(\App\Models\Inscription::class, 'inscription_id');
	}

	public function cal_calendriers()
	{
		return $this->hasMany(\App\Models\Calendrier\CalCalendrier::class, 'type');
	}
}
