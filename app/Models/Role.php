<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;
use App\Traits\HasTranslations;
use App\Traits\WithExtensions;

class Role extends SpatieRole
{
    use HasFactory;
    use HasTranslations;
    use WithExtensions;

    public $translatable = ['description'];

    /**
     * Get records
     *
     * @param int model_id
     * @param int $records_in_page
     * @param array $sort (attribute => 'asc'/'desc')
     * @param array $filters
     * @return mixed Collection
     *
     */
    public static function dlGet(
        int $model_id=0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = []
    ) {

        $oQuery = static::select('roles.*')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('roles.id', $model_id);
        })
        ->where('roles.level', '<=', auth()->user()->level)
        ->when(isset($filters['level']) && $filters['level']>0, function($query) use ($filters) {
            return $query->where('roles.level', '<=', $filters['level']);
        });

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //dd($oQuery->toSql());
        return static::getModelData($oQuery, $model_id, $records_in_page);
    }
}
