<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\SoftDeletes;

class Thirdparty extends Model
{
    use HasFactory;
    use WithExtensions;
    use SoftDeletes;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_customer' => 'boolean',
        'is_supplier' => 'boolean',
    ];

    protected $fillable = [
        'legal_form',
        'contact',
        'vat',
        'is_customer',
        'is_supplier',
        'address',
        'town',
        'province',
        'zip',
        'country_id',
        'phone',
        'mobile',
        'email',
        'invoice_email',
        'observations',
    ];

    /**
     * Get permissions
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

        $oQuery = static::select('thirdparties.*')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('thirdparties.id', $model_id);
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

        $oQuery->when(isset($filters['vat']) && !empty($filters['vat']), function($query) use ($filters) {
            return $query->where('thirdparties.vat', $filters['vat']);
        })
        ->when(isset($filters['legal_form']) && !empty($filters['legal_form']), function($query) use ($filters) {
            return $query->where('thirdparties.legal_form', 'like', '%'.$filters['legal_form'].'%');
        })
        ->when(isset($filters['email']) && !empty($filters['email']), function($query) use ($filters) {
            return $query->where('thirdparties.legal_form', 'email', '%'.$filters['email'].'%');
        })
        ->when((isset($filters['is_customer']) && !empty($filters['is_customer'])) || (isset($filters['is_supplier']) && !empty($filters['is_supplier'])), function($query) use ($filters) {
            return $query->where(function($query) use ($filters) {
                $query->when(isset($filters['is_customer']), function($query) use ($filters) {
                    $query->orWhere('thirdparties.is_customer', $filters['is_customer']);
                })
                ->when(isset($filters['is_supplier']), function($query) use ($filters) {
                    $query->orWhere('thirdparties.is_supplier', $filters['is_supplier']);
                });
            });
        })
        ->when(isset($filters['search']) && !empty($filters['search']), function($query) use ($filters) {
            return $query->where(function ($query) use ($filters){
                $query->where('thirdparties.legal_form', 'like', '%'.$filters['search'].'%')
                ->orWhere('thirdparties.email', 'like', '%'.$filters['search'].'%')
                ->orWhere('thirdparties.invoice_email', 'like', '%'.$filters['search'].'%')
                ->orWhere('thirdparties.vat', 'like', '%'.$filters['search'].'%');
            });
        });

        return $oQuery;
    }

    public function save(array $options = array(), $do_log = true)
    {
        parent::save($options, $do_log);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
