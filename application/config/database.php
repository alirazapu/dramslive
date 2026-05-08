<?php
// The 'default' connection is environment-aware. Dev (dev.ctd.drams.com)
// uses the live snapshot at aiesplusbk22032026 on 192.168.0.151 so testing
// happens against real-shape data without mutating prod, while production
// keeps using the local 'aiesplus' database. Kohana::$environment is set
// in application/bootstrap.php from the KOHANA_ENV variable.
$default_dev = array(
    'type'       => 'MySQLi',
    'connection' => array(
        'hostname'   => 'localhost',
        'username'   => 'root',
        'password'   => '',
        'persistent' => FALSE,
        'database'   => 'aiesdev',
    ),
    'table_prefix' => '',
    'charset'      => 'utf8',
);

$default_prod = array(
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
);

return array
(
    'default' => (Kohana::$environment === Kohana::DEVELOPMENT) ? $default_dev : $default_prod,
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
            // Encrypt=no + TrustServerCertificate=yes are needed because the
            // default ODBC Driver 18 connection turns encryption on and
            // rejects untrusted server certificates. The DLMS SQL Server
            // (192.168.0.152) does not present a CA-signed cert, so we
            // disable encryption rather than trust an unknown cert.
            // Verified working with this exact DSN via a standalone PDO
            // connect from the production host.
            'dsn'        => 'sqlsrv:Server=192.168.0.152,1433;Database=DLMS_FamzSolutions;Encrypt=no;TrustServerCertificate=yes',
            'username'   => 'junaid_sql',
            'password'   => 'JUNaid@123',
            'persistent' => FALSE,
        ),
        'table_prefix' => '',
        // charset MUST be empty (or unset) for SQL Server. Kohana's
        // Database_PDO::connect() calls set_charset() which runs
        //   $this->_connection->exec('SET NAMES \'utf8\'')
        // — that's MySQL-only syntax and SQL Server rejects it,
        // throwing inside the connection bootstrap. With charset=''
        // Kohana skips the call entirely. SQL Server's NVARCHAR is
        // already Unicode so no client-side charset declaration is
        // needed for this database anyway.
        'charset'      => '',
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
