<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesNotesProduct extends Model
{
    use HasFactory;
    use WithExtensions;
    use SoftDeletes;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'units' => 'float',
        'confirmed_units' => 'float',
    ];

    protected $fillable = [
        'sales_note_id',
        'sales_order_product_id',
        'description',
        'units',
        'confirmed_units',
    ];

    /**
     * Get note products
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

        $oQuery = static::select('sales_notes_products.*')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('sales_notes_products.id', $model_id);
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
            return $query->whereIn('sales_notes_products.id', $filters['product_ids']);
        })
        ->when(isset($filters['sales_note_id']) && !empty($filters['sales_note_id']), function($query) use ($filters) {
            return $query->where('sales_notes_products.sales_note_id', $filters['sales_note_id']);
        });

        return $oQuery;
    }

    /**
     * Relationships
     */
    public function note()
    {
        return $this->belongsTo(SalesNote::class, 'sales_note_id');
    }

    public function sales_orders_product()
    {
        return $this->belongsTo(SalesOrdersProduct::class, 'sales_order_product_id');
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
