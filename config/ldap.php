<?php

return [
    'default' => 'default',

    'connections' => [
        'default' => [
            'hosts' => ['10.251.1.31'],
            'username' => 'usr_ti360',
            'password' => '@AYV$*0VD0S\I*E3mh70VD0S\IXE@u!jXE@u!j',
            'port' => 389,
            'base_dn' => 'dc=cecremge,dc=cop',
        ],
    ],

    'logging' => env('LDAP_LOGGING', true),

    'cache' => [
        'enabled' => env('LDAP_CACHE', false),
        'driver' => env('CACHE_DRIVER', 'file'),
    ],
];
