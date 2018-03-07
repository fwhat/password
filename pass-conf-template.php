<?php
return [
    'components' => [
        'db' => [
            'class' => 'pass\db\%dbClass%\Connection',
            'dbDir' => DB_FILE_DIR,
        ],
        'secret' => [
            'private_key_path' => '%private_key_path%',//private_key path
            'public_key_path' => '%public_key_path%' //public_key path
        ],
    ],
    'params' => [
    ]
];