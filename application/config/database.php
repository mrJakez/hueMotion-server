<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
    'default' => array(
        'type'       => 'PDOSQLite',
        'connection' => array(
            'dsn'        => 'sqlite://Users/dennisr/pwork/hueMotion-server/database.sqlite',
            'persistent' => FALSE,
        ),
        'table_prefix' => '',
        'charset'      => NULL,
        'caching'      => FALSE,
    ),
);
