<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 08 Nov 2020 23:16:33 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class CalCalendrierParticipant
 * 
 * @property int $id_calendrier
 * @property int $calendrier
 * @property int $participant
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $inscription
 * 
 * @property \App\Models\CptInscription $cpt_inscription
 * @property \App\Models\CalCalendrier $cal_calendrier
 *
 * @package App\Models
 */
class CalCalendrierParticipant extends Eloquent
{
	protected $table = 'cal_calendrier_participant';
	protected $primaryKey = 'id_calendrier';

	protected $casts = [
		'calendrier' => 'int',
		'participant' => 'int',
		'inscription_id' => 'int'
	];

	protected $fillable = [
		'calendrier',
		'participant',
		'inscription_id'
	];

	public function cpt_inscription()
	{
		return $this->belongsTo(\App\Models\Inscription::class, 'inscription_id');
	}

	public function cal_calendrier()
	{
		return $this->belongsTo(\App\Models\CalCalendrier::class, 'calendrier');
	}
}
