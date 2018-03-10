<?php
return [
    'components' => [
        //db configure (required)
        'db' => [
            'class' => 'pass\db\%dbClass%\Connection',
            'dbDir' => DB_FILE_DIR,
        ],
        //generate default config (optional)
        'generate' => [
            'class' => 'pass\PasswordGenerate',
            'length' => 12,
            'level' => 3,
        ],
    ],
    'params' => [
    ]
];