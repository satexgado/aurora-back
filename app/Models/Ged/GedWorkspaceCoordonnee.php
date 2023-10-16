<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 28 Dec 2021 16:27:03 +0000.
 */

namespace App\Models\Ged;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class GedWorkspaceCoordonnee
 *
 * @property int $id
 * @property int $personne
 * @property int $element
 * @property int $inscription
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 *
 * @property \App\Models\GedElement $ged_element
 *
 * @package App\Models
 */
class GedWorkspaceCoordonnee extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletes;
	protected $table = 'ged_workspace_coordonnee';

	protected $casts = [
		'coordonnee_id' => 'int',
		'workspace_id' => 'int',
		'groupe_id' => 'int',
		'inscription_id' => 'int'
	];

	protected $fillable = [
		'coordonnee_id',
		'workspace_id',
		'inscription_id',
		'groupe_id'
	];

	public function ged_workspace()
	{
		return $this->belongsTo(\App\Models\Ged\GedWorkspace::class, 'workspace_id');
	}

	public function ged_workspace_groupe()
	{
		return $this->belongsTo(\App\Models\Ged\GedWorkspaceGroupe::class, 'groupe_id');
	}

    public function cr_coordonnee()
	{
		return $this->belongsTo(\App\Models\Courrier\CrCoordonnee::class, 'coordonnee_id');
	}

	public function inscription()
	{
		return $this->belongsTo(\App\Models\Inscription::class, 'inscription_id');
	}
}
