<?php

return [
    'verifactu' => [
        'production' => env('VERIFACTU_PRODUCTION', false),
        'vendorName' => env('VERIFACTU_VENDOR_NAME', 'Euromática Servicios Informáticos, S.L.'),
        'vendorVat' => env('VERIFACTU_VENDOR_VAT', 'B84574227'),
        'systemName' => env('VERIFACTU_SYSTEM_NAME', 'Eurofactu'),
        'systemId' => env('VERIFACTU_SYSTEM_ID', 'EF-001'),
        'version' => env('VERIFACTU_VERSION', '0.0.1'),
        'installationNumber' => env('VERIFACTU_INSTALLATION_NUMBER', '001'),
        'onlySupportsVerifactu' => env('VERIFACTU_ONLY_SUPPORTS_VERIFACTU', true),
        'supportsMultipleTaxpayers' => env('VERIFACTU_SUPPORTS_MULTIPLE_TAXPAYERS', true),
        'hasMultipleTaxpayers' => env('VERIFACTU_HAS_MULTIPLE_TAXPAYERS', true),
        'certificate_path' => env('VERIFACTU_CERTIFICATE_PATH', ''),
        'certificate_key_path' => env('VERIFACTU_CERTIFICATE_KEY_PATH', ''),
        'certificate_password' => env('VERIFACTU_CERTIFICATE_PASSWORD', 'pass'),
        'qr_base_url' => env('VERIFACTU_QR_BASE_URL', 'https://prewww2.aeat.es/wlpl/TIKE-CONT/ValidarQR'),
    ],
];
