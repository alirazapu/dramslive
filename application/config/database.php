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
    'ctd_kpk' => array
    (
        'type'       => 'MySQLi',
        'connection' => array(
            'hostname'   => '192.168.5.204',
            'username'   => 'azmat',
            'password'   => 'azmat@123',
            'persistent' => FALSE,
            'database'   => 'ctd_kpk',
        ),
        'table_prefix' => '',
        'charset'      => 'utf8',
    ),
    'dlms_sqlsrv' => array
    (
        'type'       => 'PDO',
        'connection' => array(
            'dsn'        => 'sqlsrv:Server=192.168.0.152,1433;Database=DLMS_FamzSolutions',
            'username'   => 'junaid_sql',
            'password'   => 'JUNaid@123',
            'persistent' => FALSE,
        ),
        'table_prefix' => '',
        'charset'      => 'utf8',
    ),
    'ecp' => array
    (
        'type'       => 'MySQLi',
        'connection' => array(
            'hostname'   => '192.168.0.156',
            'username'   => 'ecp_new_user',
            'password'   => 'ctd@123#',
            'persistent' => FALSE,
            'database'   => 'ecp',
        ),
        'table_prefix' => '',
        'charset'      => 'utf8',
    ),
    'govt_emp_data' => array
    (
        'type'       => 'MySQLi',
        'connection' => array(
            'hostname'   => '192.168.0.151',
            'username'   => 'brainbotuser',
            'password'   => 'BBuser@2025',
            'persistent' => FALSE,
            'database'   => 'govt_emp_data',
        ),
        'table_prefix' => '',
        'charset'      => 'utf8',
    ),
);
