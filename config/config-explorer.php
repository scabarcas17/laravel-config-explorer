<?php

declare(strict_types=1);

return [
    // null = auto-detect (debug mode in non-production); true/false to force.
    'enabled' => env('CONFIG_EXPLORER_ENABLED'),

    'route' => [
        'prefix'     => 'config-explorer',
        'middleware' => ['web'],
        'name'       => 'config-explorer.show',
    ],

    // Dot-path patterns; matching values render as [REDACTED]. * is a wildcard.
    'redact_patterns' => [
        '*password*',
        '*secret*',
        '*token*',
        '*api_key*',
        '*api-key*',
        '*private_key*',
        'app.key',
        'services.*.secret',
        'database.connections.*.password',
        'mail.mailers.*.password',
    ],
];
