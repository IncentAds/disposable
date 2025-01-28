<?php

use CristianPeter\LaravelDisposableContactGuard\Fetcher\email\DefaultEmailFetcher;
use CristianPeter\LaravelDisposableContactGuard\Fetcher\phone\DefaultPhoneFetcher;

return [

    'email' => [
        'sources' => [
            'https://cdn.jsdelivr.net/gh/disposable/disposable-email-domains@master/domains.json',
        ],
        'fetcher' => DefaultEmailFetcher::class,
        'storage' => storage_path('framework/disposable_domains.json'),

        'whitelist' => [],
        'blacklist' => [],

        'include_subdomains' => false,
        'cache' => [
            'enabled' => true,
            'store' => 'default',
            'key' => 'disposable_email:domains',
        ],
    ],
    'phone' => [
        'sources' => [
            'https://raw.githubusercontent.com/tagmood/Laravel-Disposable-Phone/refs/heads/master/number-list.json',
        ],
        'fetcher' => DefaultPhoneFetcher::class,
        'storage' => storage_path('framework/disposable_phone_numbers.json'),
        'whitelist' => [],
        'blacklist' => [],
        'cache' => [
            'enabled' => true,
            'store' => 'default',
            'key' => 'disposable_phone:numbers',
        ],
    ],
    'integrations' => [
        'numcheckr' =>  [
            'url' => env('NUMCHECKR_URL', 'https://numcheckr.com/api/check-number'),
            'api_key' => env('NUMCHECKR_API_KEY', ''),
        ]
    ]
];
