<?php

return [
    'default_category_id' => env('STRIPE_DEFAULT_CATEGORY_ID', 100),

    // Product mappings for test/production environments
    'product_mappings' => [
        'test' => [
            '1019' => 'prod_Rno9xm6I85ncG8', // MSR 2026 REGISTRATION
            '1007' => 'prod_SzVtWQDgCLGcpe', // MSR-2026 JERSEY GRAND
            '1008' => 'prod_RnoIlzHyOqRn2N', // MSR 2026 JERSEY TOR
            '1018' => 'prod_SzIVpsAoS1dWqu', // MSR-2026 RESERVATION
            '1006' => 'prod_SzVt24E1OwV0K8', // MSR 2026 DINNER
        ],
        'production' => [
            // Add production mappings when needed
        ],
    ],
];
