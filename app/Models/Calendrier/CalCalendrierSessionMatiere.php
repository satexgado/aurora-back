<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 25 Nov 2020 10:48:44 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class CalCalendrierSessionMatiere
 * 
 * @property int $id_calendrier
 * @property int $calendrier
 * @property int $session_matiere
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $inscription
 * 
 * @property \App\Models\VisiSessionMatiere $visi_session_matiere
 * @property \App\Models\CalCalendrier $cal_calendrier
 * @property \App\Models\CptInscription $cpt_inscription
 *
 * @package App\Models
 */
class CalCalendrierSessionMatiere extends Eloquent
{
	protected $table = 'cal_calendrier_session_matiere';
	protected $primaryKey = 'id_calendrier';

	protected $casts = [
		'calendrier' => 'int',
		'session_matiere' => 'int',
		'inscription' => 'int'
	];

	protected $fillable = [
		'calendrier',
		'session_matiere',
		'inscription'
	];

	public function visi_session_matiere()
	{
		return $this->belongsTo(\App\Models\VisiSessionMatiere::class, 'session_matiere');
	}

	public function cal_calendrier()
	{
		return $this->belongsTo(\App\Models\CalCalendrier::class, 'calendrier');
	}

	public function cpt_inscription()
	{
		return $this->belongsTo(\App\Models\CptInscription::class, 'inscription');
	}
}
