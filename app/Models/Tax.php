<?php

namespace App\Models;

use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tax extends Model
{

    use HasFactory;
    use WithExtensions;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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

        $oQuery = static::select('taxes.*')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('taxes.id', $model_id);
        })
        ->when(isset($filters['taxes_ids']) && $filters['taxes_ids']>0, function($query) use ($filters) {
            return $query->whereIn('taxes.id', $filters['taxes_ids']);
        })
        ;

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //dd($oQuery->toSql());
        return static::getModelData($oQuery, $model_id, $records_in_page);
    }
}
