<?php

namespace App\Traits;

use App\Classes\emtSign;
use Illuminate\Database\Eloquent\Builder;

trait WithExtensions
{

    public function getTokenAttribute() {
        return emtSign::sign($this->id);
    }

    public function scopeWhereInto(Builder $query, $field, $value)
    {
        if (is_array($value)) {
            return $query->whereIn($field, $value);
        } else {
            $value = explode(",", $value);
            return $query->whereIn($field, $value);
        }
    }
    public function scopeOrWhereInto(Builder $query, $field, $value)
    {
        if (is_array($value)) {
            return $query->orWhereIn($field, $value);
        } else {
            $value = explode(",", $value);
            return $query->orWhereIn($field, $value);
        }
    }
    public function scopeWhereNotInto(Builder $query, $field, $value)
    {
        if (is_array($value)) {
            return $query->whereNotIn($field, $value);
        } else {
            $value = explode(",", $value);
            return $query->whereNotIn($field, $value);
        }
    }

    public static function getModelData($oQuery, $iModel_id, $records_in_page = 0, $aWithDerived = [], $paginatorName = 'page', $keyBy = 'id') {

        if (!empty($aWithDerived)) {
            $oQuery->with($aWithDerived);
        }
        if ($iModel_id == 0) {
            //$records_in_page = ($records_in_page <= 0 || empty($records_in_page)) ? config('constants.pagination.DEFAULT_PAGE_RECORDS') : $records_in_page;
            $records_in_page = ($records_in_page == 0 ) ? config('constants.pagination.DEFAULT_PAGE_RECORDS') : $records_in_page;
            if ($records_in_page>0) {
                $oRecords = $oQuery->paginate($records_in_page, ['*'], $paginatorName);
                $oRecordsC = $oRecords->getCollection()->keyBy($keyBy);
                $oRecords->setCollection($oRecordsC);
            } else {
                $oRecords = $oQuery->get()->keyBy($keyBy);
            }
        } else {
            $oRecords = $oQuery->get()->first();
        }
        return $oRecords;
    }

}
