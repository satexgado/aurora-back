<?php

/**
 * Created by Reliese Model.
 * Date: Fri, 07 Jan 2022 03:13:45 +0000.
 */

namespace App\Models\Courrier;

use App\Models\Structure\Inscription;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Auth;

/**
 * Class CrReaffectation
 *
 * @property int $id
 * @property string $libelle
 * @property int $courrier_id
 * @property int $structure_id
 * @property int $suivi_par
 * @property int $inscription_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 *
 * @property \App\Models\Courrier\CrCourrier $cr_courrier
 * @property \App\Models\Inscription $inscription
 * @property \App\Models\Structure $structure
 *
 * @package App\Models
 */
class CrReaffectation extends Eloquent
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    protected $table = 'cr_reaffectation';

    protected $casts = [
        'courrier_id' => 'int',
        'structure_id' => 'int',
        'suivi_par' => 'int',
        'inscription_id' => 'int',
        'confirmation' => 'int',
        'annulation' => 'int',
    ];

    protected $fillable = [
        'libelle',
        'courrier_id',
        'structure_id',
        'confirmation',
        'annulation',
        'suivi_par',
        'inscription_id'
    ];

    protected $with = [
        'inscription',
        'suivi_par_inscription',
        'structure',
        'cr_courrier'
    ];
    
     //Make it available in the json response
	protected $appends = ['is_user', 'link'];
	
	public function getIsUserAttribute()
	{
		if(Auth::check() && $this->attributes['suivi_par'] == Auth::id())
		{
			return true;
		}
		return false;
	}
	
    public function getLinkAttribute()
    {
        $link ="";
        if($this->cr_courrier->cr_courrier_sortants()->count()) {
            $link = '/courrier/sortant/'.$this->cr_courrier->cr_courrier_sortants()->first()->id;
        }
        else if(
            $this->cr_courrier->cr_courrier_entrants()->whereHas('cr_provenance', function($query) {
                $query->where('cr_provenance.externe',1);
            })->count()
        ) {
            $link = '/courrier/entrant/'.$this->cr_courrier->cr_courrier_entrants()->first()->id;
        }  else  {
            $link = '/courrier/interne/'.$this->cr_courrier->cr_courrier_entrants()->first()->id;
        }
        return $link;
    }

    public function cr_courrier()
    {
        return $this->belongsTo(\App\Models\Courrier\CrCourrier::class, 'courrier_id');
    }

    public function inscription()
    {
        return $this->belongsTo(\App\Models\Inscription::class, 'inscription_id');
    }

    public function structure()
    {
        return $this->belongsTo(\App\Models\Structure::class);
    }

    public function suivi_par_inscription()
    {
        return $this->belongsTo(Inscription::class, 'suivi_par');
    }
}