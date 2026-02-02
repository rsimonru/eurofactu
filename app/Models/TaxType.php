<?php

namespace App\Models;

use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaxType extends Model
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
    public static function emtGet(
        int $model_id=0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = []
    ) {

        $oQuery = static::select('tax_types.*')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('tax_types.id', $model_id);
        })
        ->when(isset($filters['tax_type_ids']) && !empty($filters['tax_type_ids']), function($query) use ($filters) {
            return $query->whereInto('tax_types.id', $filters['tax_type_ids']);
        })
        ->when(isset($filters['tax_ids']) && !empty($filters['tax_ids']), function($query) use ($filters) {
            return $query->whereInto('tax_types.tax_id', $filters['tax_ids']);
        })
        ->when(isset($filters['value']) && $filters['value'] !== null, function($query) use ($filters) {
            return $query->where('tax_types.value', $filters['value']);
        })
        ;

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //dd($oQuery->toSql());
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }
}
