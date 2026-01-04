<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesBudgetsProduct extends Model
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
        'sales_budget_id',
        'product_variant_id',
        'description',
        'units',
        'base_unit',
        'discount_type',
        'discountp',
        'discounti',
        'base_result',
        'base_line',
        'tax_type',
        'tax_unit',
        'tax_line',
        'es_type',
        'es_unit',
        'es_line',
        'total_line',
    ];

    /**
     * Get budget products
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

        $oQuery = static::select('sales_budgets_products.*')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('sales_budgets_products.id', $model_id);
        });

        $oQuery = static::emtApplyFilters($oQuery, $filters);

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }

    public static function emtApplyFilters(
        $oQuery,
        ?array $filters = []
    ) {

        $oQuery->when(isset($filters['product_ids']) && !empty($filters['product_ids']), function($query) use ($filters) {
            return $query->whereIn('sales_budgets_products.id', $filters['product_ids']);
        })
        ->when(isset($filters['sales_budget_id']) && !empty($filters['sales_budget_id']), function($query) use ($filters) {
            return $query->where('sales_budgets_products.sales_budget_id', $filters['sales_budget_id']);
        });

        return $oQuery;
    }

    /**
     * Relationships
     */
    public function sales_budget()
    {
        return $this->belongsTo(SalesBudget::class, 'sales_budget_id');
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
