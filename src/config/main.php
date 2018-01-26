<?php
return [
//    'components' => [
//        'db' => [
////            'class' => 'yii\db\Connection',
//            'dsn' => '127.0.0.1',
//            'username' => 'root',
//            'password' => '',
//            'charset' => 'utf8',
//        ],
//    ]
    'components' => [
        'db' => [
            'class' => 'pass\db\file\Connection',
            'dir' => '',
        ],
    ],
];