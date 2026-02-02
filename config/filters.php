<?php

// El primer array son los valores por defecto de los filtros.
// El segundo array de cada filtro es la definición que usa la clase para grabar el filtro y pintar las etiquetas
return [
    'sales_budgets' => [
        [
            'sort' => 'date',
            'order' => 'desc',
            'per_page' => config('constants.pagination.DEFAULT_PAGE_RECORDS'),
            'search' => '',
            'thirdparty_id' => '',
            'date' => [null, null, null],
            'state_id' => '',
            'vat' => '',
            'number' => '',
            'expired' => '',
        ],
        [
            'search' => ['text', null, 'filtro rápido'],
            'thirdparty_id' => ['array', 'Select:thirdparties', 'cliente'],
            'date' => ['date', ['date' => 'Fecha', 'valid_until' => 'Vencimiento', 'sent_date' => 'Enviado'], 'Fecha'],
            'state_id' => ['array', 'Select:states', 'estado'],
            'vat' => ['text', null, 'CIF'],
            'number' => ['text', null, 'nº documento'],
            'expired' => ['boolean', null, 'vencidas'],
        ],
    ],
    'sales_orders' => [
        [
            'sort' => 'customer_date',
            'order' => 'desc',
            'per_page' => config('constants.pagination.DEFAULT_PAGE_RECORDS'),
            'search' => '',
            'thirdparty_id' => '',
            'date' => [null, null, null],
            'state_id' => '',
            'vat' => '',
            'number' => '',
        ],
        [
            'search' => ['text', null, 'filtro rápido'],
            'thirdparty_id' => ['array', 'Select:thirdparties', 'cliente'],
            'date' => ['date', ['date' => 'Fecha', 'sent_date' => 'Enviado'], 'Fecha'],
            'state_id' => ['array', 'Select:states', 'estado'],
            'vat' => ['text', null, 'CIF'],
            'number' => ['text', null, 'nº documento'],
        ],
    ],
    'sales_notes' => [
        [
            'sort' => 'customer_date',
            'order' => 'desc',
            'per_page' => config('constants.pagination.DEFAULT_PAGE_RECORDS'),
            'search' => '',
            'thirdparty_id' => '',
            'date' => [null, null, null],
            'state_id' => '',
            'vat' => '',
            'number' => '',
        ],
        [
            'search' => ['text', null, 'filtro rápido'],
            'thirdparty_id' => ['array', 'Select:thirdparties', 'cliente'],
            'date' => ['date', ['date' => 'Fecha', 'sent_date' => 'Enviado'], 'Fecha'],
            'state_id' => ['array', 'Select:states', 'estado'],
            'vat' => ['text', null, 'CIF'],
            'number' => ['text', null, 'nº documento'],
        ],
    ],
    'sales_invoices' => [
        [
            'sort' => 'invoice_date',
            'order' => 'desc',
            'per_page' => config('constants.pagination.DEFAULT_PAGE_RECORDS'),
            'search' => '',
            'thirdparty_id' => '',
            'date' => [null, null, null],
            'state_id' => '',
            'vat' => '',
            'number' => '',
        ],
        [
            'search' => ['text', null, 'filtro rápido'],
            'thirdparty_id' => ['array', 'Select:thirdparties', 'cliente'],
            'date' => ['date', ['invoice_date' => 'Fecha factura', 'sent_date' => 'Fecha envio'], 'Fecha'],
            'state_id' => ['array', 'Select:states', 'estado'],
            'vat' => ['text', null, 'CIF'],
            'number' => ['text', null, 'nº documento'],
        ],
    ],
    'thirdparties' => [
        [
            'sort' => 'legal_form',
            'order' => 'desc',
            'per_page' => config('constants.pagination.DEFAULT_PAGE_RECORDS'),
            'search' => '',
            'is_customer' => false,
            'is_supplier' => false,
        ],
        [
            'search' => ['text', null, 'filtro rápido'],
            'is_customer' => ['boolean', null, 'cliente'],
            'is_supplier' => ['boolean', null, 'proveedor'],
        ],
    ],
    'products' => [
        [
            'sort' => 'description',
            'order' => 'asc',
            'per_page' => config('constants.pagination.DEFAULT_PAGE_RECORDS'),
            'search' => '',
        ],
        [
            'search' => ['text', null, 'filtro rápido'],
        ],
    ],
];
