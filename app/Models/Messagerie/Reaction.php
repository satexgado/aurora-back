<?php

namespace App\Models\Messagerie;

use App\ApiRequest\ApiRequestConsumer;
use App\Models\Structure\Inscription;
use App\Models\Structure\Structure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Reaction extends Model
{

    use SoftDeletes, ApiRequestConsumer;
    protected $table = 'reactions';
    protected $primaryKey = 'id';
    protected $fillable = ['reaction', 'fichier', 'rebondissement', 'discussion', 'inscription'];
    protected $with = ['inscription', 'rebondissement'];
    protected $appends = ['structure'];


    public function rebondissement()
    {
        return $this->belongsTo(Reaction::class, 'rebondissement');
    }


    // public function fichier()
    // {
    //     return $this->belongsTo(Fichier::class, 'fichier');
    // }


    public function inscription()
    {
        return $this->belongsTo(Inscription::class, 'inscription')->select('id', 'prenom', 'nom', 'photo');
    }

    public function deletions()
    {
        return $this->hasMany(ReactionSupprime::class, 'reaction');
    }


    public function discussion()
    {
        return $this->belongsTo(Discussion::class, 'discussion');
    }

    public function getStructureAttribute()
    {
        return $this->structures()->get()->first();
    }

    public function reaction_lus()
    {
        return $this->hasMany(ReactionLu::class, 'reaction');
    }

    public function structures()
    {
        return $this->belongsToMany(Structure::class, ReactionStructure::class, 'reaction', 'structure')->without('type')->select(['structures.id', 'libelle', 'image', 'cigle']);
    }

    public function scopeWhereNotDeleted(Builder $query)
    {
        return $query->whereDoesntHave('deletions', function ($q) {
            $q->where('created_at', '<', 'reactions.created_at')->where('user', Auth::id());
        });
    }

    public function getFichierAttribute(){
		if($this->attributes['fichier']){
			$document_scanne = "http://127.0.0.1:8000/public/storage/".$this->attributes['fichier'];
			return $document_scanne;
		}
		return 0;
	}


    public function scopeWhereNotReaded($builder, $user_id)
    {
        return $builder->whereDoesntHave('reaction_lus', function ($q) use ($user_id) {
            $q->where('user', $user_id);
        });
    }

    public function marquerCommeLuParUser($user)
    {
        ReactionLu::create(['reaction' => $this->id, 'user' => Auth::id(), 'inscription' => $user]);
    }
}
