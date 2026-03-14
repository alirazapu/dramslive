<?php
return array
(
    'default' => array
    (
        'type'       => 'MySQLi',
        'connection' => array(
              'hostname'   => 'localhost',           
            'username'   => 'root',           
           'password'   => '',
            'persistent' => FALSE,
            'database'   => 'aiesplus',
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
                'hostname'   => '192.168.0.151',
                'username'   => 'junaid',
                'password'   => 'JUNaid@18182025',
                'persistent' => FALSE,
                'database'   => 'subscriber_db',
            ),
        'table_prefix' => '',
        'charset'      => 'utf8',
    ),
);