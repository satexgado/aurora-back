<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 09 Nov 2020 12:52:53 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class CalCalendrierMatiere
 * 
 * @property int $id_calendrier
 * @property int $calendrier
 * @property int $matiere
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $inscription
 * 
 * @property \App\Models\VisiMatiere $visi_matiere
 * @property \App\Models\CalCalendrier $cal_calendrier
 * @property \App\Models\CptInscription $cpt_inscription
 *
 * @package App\Models
 */
class CalCalendrierMatiere extends Eloquent
{
	protected $table = 'cal_calendrier_matiere';
	protected $primaryKey = 'id_calendrier';

	protected $casts = [
		'calendrier' => 'int',
		'matiere' => 'int',
		'inscription' => 'int'
	];

	protected $fillable = [
		'calendrier',
		'matiere',
		'inscription'
	];

	public function visi_matiere()
	{
		return $this->belongsTo(\App\Models\VisiMatiere::class, 'matiere');
	}

	public function cal_calendrier()
	{
		return $this->belongsTo(\App\Models\CalCalendrier::class, 'calendrier');
	}

	public function cpt_inscription()
	{
		return $this->belongsTo(\App\Models\Inscription::class, 'inscription');
	}
}
