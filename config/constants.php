<?php

return [
	'pagination' => [
		'DEFAULT_PAGE_RECORDS' => 150,
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
		'created_at' => 'Fecha alta',
		'customer_date' => 'Fecha cliente',
		'expiration_date' => 'Fecha vencimiento',
		'delivery_date' => 'Fecha entrega',
		'last_login' => 'Última conexión',
	],
];
