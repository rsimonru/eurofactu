<?php

namespace App\Models;

use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    use WithExtensions;

    /**
     * Get records.
     *
     * @param int $model_id
     * @param int $records_in_page
     * @param array $sort (attribute => 'asc'/'desc')
     * @param array $filters
     * @return mixed Colletion
     *
     */
    public static function emtGet(
        ?int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        ?array $filters = [],
        array $with = []
    ) {

        $query = static::select('products.*')
        ->when($model_id > 0, function ($query) use ($model_id) {
            return $query->where('products.id', $model_id);
        })
        ;

        $query = static::emtApplyFilters($query, $filters);

        foreach ($sort as $key => $value) {
            $query->orderBy($key, $value);
        }

        return static::getModelData($query, $model_id, $records_in_page, $with);
    }

    private static function emtApplyFilters(
        $query,
        ?array $filters = []
    ) {

        $query->when(isset($filters['product_ids']) && !empty($filters['product_ids']), function($query) use ($filters) {
            $query->whereIn('products.id', $filters['product_ids']);
        })
        ;

        return $query;
    }

}
