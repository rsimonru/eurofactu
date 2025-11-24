<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesInvoicesProduct extends Model
{
    use HasFactory;
    use WithExtensions;
    use SoftDeletes;

    /**
     * Get invoices
     *
     * @param int $iModels_id
     * @param int $records_in_page
     * @param array $aSort (attribute => 'asc'/'desc')
     * @param array $aFilters
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

        $oQuery = static::select('sales_invoices_products.*')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('sales_invoices.id', $model_id);
        });

        $oQuery = static::emtApplyFilters($oQuery, $filters);

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }
    public static function emtApplyFilters(
        $oQuery,
        ?array $filters = []
    ) {

        $oQuery->when(isset($filters['product_ids']) && !empty($filters['product_ids']), function($query) use ($filters) {
            return $query->whereIn('sales_invoices_products.id', $filters['product_ids']);
        })
        ->when(isset($filters['sinvoice_id']) && !empty($filters['sinvoice_id']), function($query) use ($filters) {
            return $query->where('sales_invoices_products.sinvoice_id', $filters['sinvoice_id']);
        });

        return $oQuery;
    }

    public function sales_invoice() {
        return $this->hasOne(SalesInvoice::class, 'id', 'sinvoice_id');
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
