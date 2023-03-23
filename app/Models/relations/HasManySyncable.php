<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Dash\CrCourrier;

/**
 * @link https://github.com/laravel/framework/blob/5.4/src/Illuminate/Database/Eloquent/Relations/HasMany.php
 */
class HasManySyncable extends HasMany
{
    public function sync($data, $deleting = true)
    {
        $changes = [
            'created' => [], 'deleted' => [], 'updated' => [],
        ];

        $relatedKeyName = $this->related->getKeyName();

        // First we need to attach any of the associated models that are not currently
        // in the child entity table. We'll spin through the given IDs, checking to see
        // if they exist in the array of current ones, and if not we will insert.
        $current = $this->newQuery()->pluck(
            $relatedKeyName
        )->all();
    
        // Separate the submitted data into "update" and "new"
        $updateRows = [];
        $newRows = [];
        foreach ($data as $key=>$value) {
            // We determine "updateable" rows as those whose $relatedKeyName (usually 'id') is set, not empty, and
            // match a related row in the database.
            if (isset($value) && !empty($value) && in_array($key, $current)) {
                $id = $key;
                $updateRows[$id] = $value;
            } else {
                $newRows[] = $key;
            }
        }

        // Next, we'll determine the rows in the database that aren't in the "update" list.
        // These rows will be scheduled for deletion.  Again, we determine based on the relatedKeyName (typically 'id').
        $updateIds = array_keys($updateRows);
        $deleteIds = [];
        foreach ($current as $currentId) {
            if (!in_array($currentId, $updateIds)) {
                $deleteIds[] = $currentId;
            }
        }

        // Delete any non-matching rows
        if ($deleting && count($deleteIds) > 0) {
            foreach ($deleteIds as $deleteId) {
                $found = CrCourrier::findOrFail($deleteId);
                if($found) {
                 $found->dossier_id = null;
                 $found->save();
                 $changes['created'] = $found;
                }
            }  
        }

        $changes['deleted'] = $this->castKeys($deleteIds);

        // Update the updatable rows
        // foreach ($updateRows as $id => $row) {
        //    $found = $this->getRelated()->find($id);
        //    if($found) {
        //     $found->update(
        //         ['dossier_id'=> $this->getParent()->getAttribute($this->localKey)]
        //      )
        //      ->update($row);
        //    }   
        // }
        
        $changes['updated'] = $this->castKeys($updateIds);

        // Insert the new rows
        $newIds = [];
        foreach ($newRows as $row) {
           $found = CrCourrier::findOrFail($row);
           if($found) {
            $found->dossier_id = $this->getParent()->getAttribute($this->localKey);
            $found->save();
           }
        }

        // $changes['created'] = $this->castKeys($newRows); 
        $changes['created'] =  $this->getBaseQuery()->get();

        return $changes;
    }


    /**
     * Cast the given keys to integers if they are numeric and string otherwise.
     *
     * @param  array  $keys
     * @return array
     */
    protected function castKeys(array $keys)
    {
        return (array) array_map(function ($v) {
            return $this->castKey($v);
        }, $keys);
    }
    
    /**
     * Cast the given key to an integer if it is numeric.
     *
     * @param  mixed  $key
     * @return mixed
     */
    protected function castKey($key)
    {
        return is_numeric($key) ? (int) $key : (string) $key;
    }
}