<?php

return [
    // Role => allowed route name patterns (wildcards allowed)
    // Use '*' to allow everything for a role
    'roles' => [
        'superadmin' => ['*'],
        'admin' => [
            // admin full area except sensitive system settings if you want to limit later
            'admin.*',
        ],
        'user' => [
            // default non-admin user: only see dashboard, transactions manage, sales, stock reports, stock-batches, stock-card
            'admin.dashboard',
            'admin.transactions.manage',
            'admin.sales',
            'admin.stock-reports',
            'admin.stock-batches.index',
            'stock-card.*',
            // allow profile/settings pages
            'settings.*',
        ],
    ],
];
