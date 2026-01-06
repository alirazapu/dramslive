<?php
return array
(
    'default' => array
    (
        'type'       => 'MySQLi',
        'connection' => array(
              'hostname'   => '192.168.1.100',           
            'username'   => 'ims',           
           'password'   => '123456789',
            'persistent' => FALSE,
            'database'   => 'aies',
        ),
        'table_prefix' => '',
        'charset'      => 'utf8',
    ),
    'remote' => array(
        'type'       => 'MySQL',
        'connection' => array(
            'hostname'   => '55.55.55.55',
            'username'   => 'remote_user',
            'password'   => 'mypassword',
            'persistent' => FALSE,
            'database'   => 'my_remote_db_name',
        ),
        'table_prefix' => '',
        'charset'      => 'utf8',
    ),
    'mobile' => array
    (
        'type'       => 'MySQLi',
                'connection' => array(
                //'hostname'   => 'p:12.10.10.6',
                'hostname'   => '50.0.0.8',
                'username'   => 'subscriber',
                'password'   => '7zwZwC*Ga&6w7c*#',
                'persistent' => FALSE,
                'database'   => 'dtsrc',
//                'hostname'   => 'p:50.0.0.6',
//                'hostname'   => 'p:12.10.10.6',
//                'username'   => 'wmsremote',
//                'password'   => 'Tora_Bora#312',
//                'persistent' => FALSE,
//                'database'   => 'mobile',
            ),
        'table_prefix' => '',
        'charset'      => 'utf8',
    ),
);