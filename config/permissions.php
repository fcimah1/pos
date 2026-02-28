<?php

return [
    // Define roles and their allowed permissions
    'roles' => [
        'admin' => [
            '*', // full access
        ],

        'cashier' => [
            'view_orders',
            'create_orders',
            'print_receipt',
        ],

        'storekeeper' => [
            'view_products',
            'manage_stock',
        ],
    ],
];
