<?php

/**
 * Created by Reliese Model.
 * Date: Fri, 20 Nov 2020 11:25:28 +0000.
 */

namespace App\Models\Calendrier;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class CalGroupeCalendrier
 * 
 * @property int $id_type_calendrier
 * @property string $libelle
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $inscription
 * 
 * @property \App\Models\CptInscription $cpt_inscription
 * @property \Illuminate\Database\Eloquent\Collection $cal_calendriers
 *
 * @package App\Models
 */
class CalGroupeCalendrier extends Eloquent
{
	protected $table = 'cal_groupe_calendrier';
	protected $primaryKey = 'id_type_calendrier';

	protected $casts = [
		'inscription_id' => 'int'
	];

	protected $fillable = [
		'libelle',
		'inscription_id'
	];

	public function cpt_inscription()
	{
		return $this->belongsTo(\App\Models\Inscription::class, 'inscription_id');
	}

	public function cal_calendriers()
	{
		return $this->hasMany(\App\Models\Calendrier\CalCalendrier::class, 'groupe');
	}
}
