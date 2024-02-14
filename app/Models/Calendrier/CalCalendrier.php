<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 27 Oct 2020 10:44:02 +0000.
 */

namespace App\Models\Calendrier;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class CalCalendrier
 *
 * @property int $id_calendrier
 * @property int $type
 * @property string $libelle
 * @property string $lieu
 * @property \Carbon\Carbon $date_debut
 * @property \Carbon\Carbon $date_fin
 * @property string $description
 * @property int $allday
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $inscription
 * @property string $affectable_type
 * @property int $affectable_id
 *
 * @property \App\Models\CptInscription $cpt_inscription
 * @property \App\Models\CalTypeCalendrier $cal_type_calendrier
 *
 * @package App\Models
 */
class CalCalendrier extends Eloquent
{
	protected $table = 'cal_calendrier';
	protected $primaryKey = 'id_calendrier';

	protected $casts = [
		'type' => 'int',
		'all_day' => 'int',
		'inscription_id' => 'int',
        'affectable_id' => 'int',
        'groupe' => 'int'
	];

	protected $dates = [
		'date_debut',
		'date_fin'
	];

	protected $fillable = [
        'duration',
        'type',
        'groupe',
        'rrule',
		'libelle',
		'lieu',
		'date_debut',
		'date_fin',
		'description',
		'all_day',
		'inscription_id',
		'affectable_type',
		'affectable_id'
	];

	public function cpt_inscription()
	{
		return $this->belongsTo(\App\Models\Inscription::class, 'inscription_id');
    }


    public static function related()
    {
        $feed = self::with('cal_type_calendrier')->get();
        //Evenement
        $feed->where('id_type_calendrier', 1)->load('participants');
        //Réunion
        // $feed->where('id_type_calendrier', 2)->load('relationA');
        //Cours
        $feed->where('id_type_calendrier', 3)->load(['participants','visi_matieres']);
        //Evaluation
        $feed->where('id_type_calendrier', 4)->load(['visi_session_matiere.visi_session', 'visi_session_matiere.visi_matiere', 'visi_type_evaluation']);
        //Inscription
        $feed->where('id_type_calendrier', 5)->load(['visi_niveau']);

        return $feed;
    }

    public function calendrier_recurring_rule()
	{
		return $this->belongsTo(\App\Models\Calendrier\CalCalendrierRecurringRule::class, 'rrule');
    }

    public function participants()
	{
		return $this->belongsToMany(\App\Models\Inscription::class, 'cal_calendrier_participant', 'calendrier', 'participant');
	}

    public function personnels()
	{
		return $this->belongsToMany(\App\Models\VisiPersonnel::class, 'cal_calendrier_personnel', 'calendrier', 'personnel');
	}

    public function visi_matieres()
	{
		return $this->belongsToMany(\App\Models\VisiMatiere::class, 'cal_calendrier_matiere', 'calendrier', 'matiere');
    }

    public function visi_programmes()
	{
		return $this->belongsToMany(\App\Models\VisiProgramme::class, 'cal_calendrier_niveau', 'calendrier', 'niveau');
    }

    public function visi_niveaus()
	{
		return $this->belongsToMany(\App\Models\VisiNiveau::class, 'cal_calendrier_niveau', 'calendrier', 'niveau');
    }

    public function visi_niveau()
	{
		return $this->hasOneThrough(\App\Models\VisiNiveau::class, \App\Models\CalCalendrierNiveau::class, 'calendrier', 'id_niveau', 'id_calendrier', 'niveau');
    }

    public function visi_type_evaluation()
	{
		return $this->hasOneThrough(\App\Models\VisiTypeEvaluation::class, \App\Models\CalCalendrierTypeEvaluation::class, 'calendrier', 'id_type_evaluation', 'id_calendrier', 'type_evaluation');
    }

    public function visi_session_matiere()
	{
		return $this->hasOneThrough(\App\Models\VisiSessionMatiere::class, \App\Models\CalCalendrierSessionMatiere::class, 'calendrier', 'id_affectation_session_matiere', 'id_calendrier', 'session_matiere');
    }
	public function cal_type_calendrier()
	{
		return $this->belongsTo(\App\Models\Calendrier\CalTypeCalendrier::class, 'type');
    }

    public function cal_groupe_calendrier()
	{
		return $this->belongsTo(\App\Models\Calendrier\CalGroupeCalendrier::class, 'groupe');
	}
}
