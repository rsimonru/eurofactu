<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOrdersProduct extends Model
{
    use HasFactory;
    use WithExtensions;
    use SoftDeletes;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'units' => 'float',
        'base_unit' => 'float',
        'discountp' => 'float',
        'discounti' => 'float',
        'base_result' => 'float',
        'base_line' => 'float',
        'tax_unit' => 'float',
        'tax_line' => 'float',
        'es_unit' => 'float',
        'es_line' => 'float',
        'total_line' => 'float',
    ];

    protected $fillable = [
        'sales_order_id',
        'product_variant_id',
        'description',
        'units',
        'base_unit',
        'discount_type',
        'discountp',
        'discounti',
        'base_result',
        'base_line',
        'tax_type_id',
        'tax_type',
        'tax_unit',
        'tax_line',
        'es_type',
        'es_unit',
        'es_line',
        'total_line',
    ];

    /**
     * Get order products
     *
     * @param int $model_id
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

        $oQuery = static::select('sales_orders_products.*')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('sales_orders_products.id', $model_id);
        });

        $oQuery = static::applyFilters($oQuery, $filters);

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public static function applyFilters(
        $oQuery,
        ?array $filters = []
    ) {

        $oQuery->when(isset($filters['product_ids']) && !empty($filters['product_ids']), function($query) use ($filters) {
            return $query->whereIn('sales_orders_products.id', $filters['product_ids']);
        })
        ->when(isset($filters['sales_order_id']) && !empty($filters['sales_order_id']), function($query) use ($filters) {
            return $query->where('sales_orders_products.sales_order_id', $filters['sales_order_id']);
        });

        return $oQuery;
    }

    /**
     * Relationships
     */
    public function order()
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }

    public function product_variant()
    {
        return $this->belongsTo(ProductsVariant::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
