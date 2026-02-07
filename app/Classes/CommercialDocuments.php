<?php

namespace App\Classes;

use App\Models\TaxType;

class CommercialDocuments
{

    public static function getProductsSummary($documents, $key_by = 'id') {
        $tax_types = TaxType::emtGet(records_in_page: -1, filters: ['tax_id' => $documents->first()->tax_id], with: ['tax'])
            ->keyBy('id');

        $summaries = [];
        foreach ($documents as $document) {
            $summary = $document->emtGetProductsSummary($tax_types);
            $summaries[$document->{$key_by}] = $summary;
        }
        return $summaries;
    }
}
