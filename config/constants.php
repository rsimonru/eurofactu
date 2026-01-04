<?php

return [
	'pagination' => [
		'DEFAULT_PAGE_RECORDS' => 25,
		'LIMIT_PAGE_RECORDS' => 1000
	],
	'levels' => [
		"basic" => 1,
		"collaborator" => 2,
		"employee" => 3,
		"responsable" => 4,
		"administrator" => 5,
		"superadmin" => 6,
	],
    'states' => [
        'paid' => 1,
        'pending' => 2,
        'open' => 3,
        'closed' => 4,
        'cancelled' => 5,
        'active' => 6,
        'inactive' => 7,
        'approved' => 8,
        'sent' => 9,
    ],
    'date_type' => [
		'created_at' => 'F. alta',
		'customer_date' => 'F. cliente',
		'expiration_date' => 'F. vencimiento',
		'delivery_date' => 'F. entrega',
		'last_login' => 'Últ. conexión',
		'date' => 'F. documento',
	],
];
