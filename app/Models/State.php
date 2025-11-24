<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;
use App\Traits\WithExtensions;

class State extends Model
{
    use HasFactory;
    use HasTranslations;
    use WithExtensions;

    public $translatable = ['description'];

    /**
     * Get state
     *
     * @param int $iModels_id
     * @param int $records_in_page
     * @param array $aSort (attribute => 'asc'/'desc')
     * @param array $aFilters
     * @return mixed Collection
     *
     */
    public static function emtGet(
        int $iModels_id=0,
        int $records_in_page = 0,
        array $aSort = [],
        array $aFilters = []
    ) {

        $oQuery = static::select('states.*')
        ->when($iModels_id>0, function($query) use ($iModels_id) {
            return $query->where('states.id', $iModels_id);
        })
        ->when(isset($aFilters['states_ids']) && !empty($aFilters['states_ids']), function($query) use ($aFilters) {
            return $query->whereIn('states.id', $aFilters['states_ids']);
        })
        ;

        foreach ($aSort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //$oQuery->dd();
        return static::getModelData($oQuery, $iModels_id, $records_in_page);
    }

}
