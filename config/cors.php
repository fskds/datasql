<?php

return [
    'paths' => ['api/*', 'api-admin/*', 'api-user/*', 'oauth/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'],      // 也可改为你的具体前端域名
    'allowed_origins_patterns' => [
        'http://*.cnsesi.com',
        'https://*.cnsesi.com',
        'http://cnsesi.com',
        'https://cnsesi.com',
    ],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
