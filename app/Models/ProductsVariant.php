<?php

namespace App\Models;

use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductsVariant extends Model
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

        $query = static::select('products_variants.*')
        ->when($model_id > 0, function ($query) use ($model_id) {
            return $query->where('products_variants.id', $model_id);
        })
        ;

        $query = static::applyFilters($query, $filters);

        foreach ($sort as $key => $value) {
            $query->orderBy($key, $value);
        }

        return static::getModelData($query, $model_id, $records_in_page, $with);
    }

    private static function applyFilters(
        $query,
        ?array $filters = []
    ) {

        $query->when(isset($filters['variants_ids']) && !empty($filters['variants_ids']), function($query) use ($filters) {
            $query->whereIn('products_variants.id', $filters['variants_ids']);
        })
        ->when(isset($filters['product_id']) && !empty($filters['product_id']), function($query) use ($filters) {
            $query->where('products_variants.product_id', $filters['product_id']);
        })
        ;

        return $query;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
