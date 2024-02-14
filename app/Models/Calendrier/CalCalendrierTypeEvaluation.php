<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 25 Nov 2020 09:48:23 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class CalCalendrierTypeEvaluation
 * 
 * @property int $id_calendrier
 * @property int $calendrier
 * @property int $type_evaluation
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $inscription
 * 
 * @property \App\Models\VisiTypeEvaluation $visi_type_evaluation
 * @property \App\Models\CalCalendrier $cal_calendrier
 * @property \App\Models\CptInscription $cpt_inscription
 *
 * @package App\Models
 */
class CalCalendrierTypeEvaluation extends Eloquent
{
	protected $table = 'cal_calendrier_type_evaluation';
	protected $primaryKey = 'id_calendrier';

	protected $casts = [
		'calendrier' => 'int',
		'type_evaluation' => 'int',
		'inscription' => 'int'
	];

	protected $fillable = [
		'calendrier',
		'type_evaluation',
		'inscription'
	];

	public function visi_type_evaluation()
	{
		return $this->belongsTo(\App\Models\VisiTypeEvaluation::class, 'type_evaluation');
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
