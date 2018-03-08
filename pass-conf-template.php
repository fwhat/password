<?php
return [
    'components' => [
        'db' => [
            'class' => 'pass\db\%dbClass%\Connection',
            'dbDir' => DB_FILE_DIR,
        ],
    ],
    'params' => [
    ]
];