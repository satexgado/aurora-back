<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 19 Nov 2020 15:48:24 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class CalCalendrierNiveau
 * 
 * @property int $id_calendrier
 * @property int $calendrier
 * @property int $niveau
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $inscription
 * 
 * @property \App\Models\VisiNiveau $visi_niveau
 * @property \App\Models\CalCalendrier $cal_calendrier
 * @property \App\Models\CptInscription $cpt_inscription
 *
 * @package App\Models
 */
class CalCalendrierNiveau extends Eloquent
{
	protected $table = 'cal_calendrier_niveau';
	protected $primaryKey = 'id_calendrier';

	protected $casts = [
		'calendrier' => 'int',
		'niveau' => 'int',
		'inscription' => 'int'
	];

	protected $fillable = [
		'calendrier',
		'niveau',
		'inscription'
	];

	public function visi_niveau()
	{
		return $this->belongsTo(\App\Models\VisiNiveau::class, 'niveau');
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
