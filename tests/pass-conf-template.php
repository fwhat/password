<?php
return [
    'components' => [
        'db' => [
            'class' => 'pass\db\sqlite\Connection',
            'dbDir' => '',
        ],
        'secret' => [
            'private_key_path' => '%private_key_path%',//private_key path
            'public_key_path' => '%public_key_path%' //public_key path
        ],
    ],
    'params' => [
    ]
];