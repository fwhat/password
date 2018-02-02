<?php
return [
    'components' => [
        'db' => [
            'class' => 'pass\db\sqlite\Connection',
            'dbDir' => '',
        ],
    ],
    'params' => [
        'private_key' => '', //openssl.cnf path
        'public_key' => '' //openssl.cnf path
    ]
];