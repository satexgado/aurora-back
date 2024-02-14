<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 10 Nov 2020 19:28:56 +0000.
 */

namespace App\Models\Calendrier;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class CalCalendrierRecurringRule
 * 
 * @property int $id_calendrier
 * @property string $freq
 * @property \Carbon\Carbon $dtstart
 * @property int $interval
 * @property string $wkst
 * @property int $count
 * @property \Carbon\Carbon $until
 * @property string $bysetpos
 * @property string $bymonth
 * @property string $bymonthday
 * @property string $byyearday
 * @property string $byweekno
 * @property string $byweekday
 * @property string $byhour
 * @property string $byminute
 * @property string $bysecond
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $inscription
 * 
 * @property \App\Models\CptInscription $cpt_inscription
 * @property \Illuminate\Database\Eloquent\Collection $cal_calendriers
 *
 * @package App\Models
 */
class CalCalendrierRecurringRule extends Eloquent
{
	protected $table = 'cal_calendrier_recurring_rule';
	protected $primaryKey = 'id_calendrier';

	protected $casts = [
		'interval' => 'int',
		'count' => 'int',
		'inscription_id' => 'int'
	];

	protected $dates = [
		'dtstart',
		'until'
	];

	protected $fillable = [
		'freq',
		'dtstart',
		'interval',
		'wkst',
		'count',
		'until',
		'bysetpos',
		'bymonth',
		'bymonthday',
		'byyearday',
		'byweekno',
		'byweekday',
		'byhour',
		'byminute',
		'bysecond',
		'inscription_id'
	];

	public function cpt_inscription()
	{
		return $this->belongsTo(\App\Models\CptInscription::class, 'inscription_id');
	}

	public function cal_calendriers()
	{
		return $this->hasMany(\App\Models\Calendrier\CalCalendrier::class, 'rrule');
	}
}
