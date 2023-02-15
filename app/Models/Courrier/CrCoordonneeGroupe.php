<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 09 Feb 2023 12:34:53 +0000.
 */

namespace App\Models\Courrier;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class CrCoordonneeGroupe
 *
 * @property int $id
 * @property string $libelle
 * @property int $inscription_id
 * @property int $groupe_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 *
 * @property \App\Models\CrCoordonneeGroupe $cr_coordonnee_groupe
 * @property \App\Models\Inscription $inscription
 * @property \Illuminate\Database\Eloquent\Collection $cr_affectation_coordonnee_groupes
 * @property \Illuminate\Database\Eloquent\Collection $cr_coordonnee_groupes
 *
 * @package App\Models
 */
class CrCoordonneeGroupe extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes;
	protected $table = 'cr_coordonnee_groupe';

	protected $casts = [
		'inscription_id' => 'int',
		'groupe_id' => 'int'
	];

	protected $fillable = [
		'libelle',
		'inscription_id',
		'groupe_id'
	];

	public function cr_coordonnee_groupe()
	{
		return $this->belongsTo(\App\Models\Courrier\CrCoordonneeGroupe::class, 'groupe_id');
	}

	public function inscription()
	{
		return $this->belongsTo(\App\Models\Inscription::class);
	}

	public function cr_affectation_coordonnee_groupes()
	{
		return $this->hasMany(\App\Models\CrAffectationCoordonneeGroupe::class, 'groupe_id');
	}

	public function cr_coordonnee_groupes()
	{
		return $this->hasMany(\App\Models\Courrier\CrCoordonneeGroupe::class, 'groupe_id');
	}

    public function cr_coordonnees()
    {
        return $this->belongsToMany(\App\Models\Courrier\CrCoordonnee::class, 'cr_affectation_coordonnee_groupe', 'groupe_id', 'coordonnee_id');
    }

}
