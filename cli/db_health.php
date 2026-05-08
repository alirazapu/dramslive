<?php
/**
 * cli/db_health.php
 *
 * Standalone CLI database connection test. Pings every connection
 * declared in application/config/database.php with a SELECT 1 and
 * prints a pass/fail line per connection.
 *
 * Usage from the project root:
 *
 *   php cli\db_health.php                   (test every connection, dev env)
 *   php cli\db_health.php production        (force production env)
 *   php cli\db_health.php dlms_sqlsrv       (test only the named connection)
 *   php cli\db_health.php dlms_sqlsrv prod  (combine: target + env)
 *
 * Exit code:
 *   0 on success (every connection that was tested passed)
 *   1 on any failure
 *   2 on script-level errors (config not found, etc.)
 *
 * Why standalone (no Kohana bootstrap):
 *   The whole point of the script is to verify low-level driver and
 *   network connectivity from the command line. Routing through
 *   Kohana's web request machinery would mask a real DB problem
 *   behind a different error if the bootstrap itself were broken.
 *   We stub just the bits of Kohana that database.php happens to
 *   read (Kohana::$environment + the DEVELOPMENT/PRODUCTION
 *   constants) and execute the config file as a plain PHP include.
 */

// ---- Stub the bits of Kohana that database.php touches ---------------
class Kohana
{
    const DEVELOPMENT = 'development';
    const PRODUCTION  = 'production';
    public static $environment = self::DEVELOPMENT;
}

// ---- CLI args --------------------------------------------------------
$argv = isset($argv) ? array_slice($argv, 1) : array();
$target_connection = null;
foreach ($argv as $arg) {
    $low = strtolower((string) $arg);
    if (in_array($low, array('production', 'prod', 'live'), true)) {
        Kohana::$environment = Kohana::PRODUCTION;
    } elseif (in_array($low, array('development', 'dev', 'local'), true)) {
        Kohana::$environment = Kohana::DEVELOPMENT;
    } elseif ($low === '--help' || $low === '-h' || $low === '/?') {
        fwrite(STDERR, "Usage: php cli/db_health.php [connection] [development|production]\n");
        exit(0);
    } else {
        $target_connection = (string) $arg;
    }
}

// ---- Locate + load the config ----------------------------------------
$config_path = realpath(__DIR__ . '/../application/config/database.php');
if (!$config_path || !is_file($config_path)) {
    fwrite(STDERR, "Cannot find application/config/database.php (looked under "
        . dirname(__DIR__) . DIRECTORY_SEPARATOR . "application/config)\n");
    exit(2);
}
$cfg = include $config_path;
if (!is_array($cfg)) {
    fwrite(STDERR, "database.php did not return an array.\n");
    exit(2);
}

// ---- Driver inventory ------------------------------------------------
echo "PHP " . PHP_VERSION . " (" . (PHP_ZTS ? 'ZTS' : 'NTS') . ", "
   . (PHP_INT_SIZE * 8) . "-bit) on " . (defined('PHP_OS_FAMILY') ? PHP_OS_FAMILY : PHP_OS) . "\n";
echo "environment = " . Kohana::$environment . "\n";
foreach (array('mysqli', 'pdo', 'pdo_mysql', 'sqlsrv', 'pdo_sqlsrv') as $ext) {
    echo sprintf("  ext %-12s : %s\n", $ext, extension_loaded($ext) ? 'loaded' : 'MISSING');
}
echo "\n";

// ---- Test loop -------------------------------------------------------
$dash = str_repeat('-', 100) . "\n";
echo $dash;
echo sprintf("%-18s %-10s %-32s %-7s %s\n",
    'connection', 'type', 'host/dsn', 'result', 'detail');
echo $dash;

$any_fail = false;
$tested   = 0;
foreach ($cfg as $name => $opts) {
    if ($target_connection !== null && $name !== $target_connection) continue;
    $tested++;

    $type = isset($opts['type']) ? $opts['type'] : '?';
    $conn = isset($opts['connection']) ? $opts['connection'] : array();

    if ($type === 'PDO') {
        $host_dsn = isset($conn['dsn']) ? $conn['dsn'] : '';
    } else {
        $host_dsn = (isset($conn['hostname']) ? $conn['hostname'] : '?')
                  . '/' . (isset($conn['database']) ? $conn['database'] : '?');
    }

    $status = 'ok';
    $detail = '';
    $t0     = microtime(true);

    try {
        if ($type === 'PDO') {
            if (!extension_loaded('pdo')) {
                throw new RuntimeException('PHP extension pdo not loaded');
            }
            if (strpos((string) $host_dsn, 'sqlsrv:') === 0 && !extension_loaded('pdo_sqlsrv')) {
                throw new RuntimeException('PHP extension pdo_sqlsrv not loaded');
            }
            if (strpos((string) $host_dsn, 'mysql:') === 0 && !extension_loaded('pdo_mysql')) {
                throw new RuntimeException('PHP extension pdo_mysql not loaded');
            }
            // ATTR_TIMEOUT is intentionally NOT passed to the PDO
            // constructor here — Microsoft's pdo_sqlsrv driver
            // rejects it with "SQLSTATE[IMSSP]: An unsupported
            // attribute was designated on the PDO object", which
            // makes the script falsely report a perfectly healthy
            // connection as failed. For SQL Server, login timeout
            // is set via the DSN parameter "LoginTimeout=N".
            $pdo = new PDO(
                (string) $host_dsn,
                isset($conn['username']) ? (string) $conn['username'] : '',
                isset($conn['password']) ? (string) $conn['password'] : '',
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                )
            );
            $row    = $pdo->query('SELECT 1 AS one')->fetch(PDO::FETCH_ASSOC);
            $ms     = (int) round((microtime(true) - $t0) * 1000);
            $detail = $ms . ' ms';
            if (!isset($row['one'])) {
                $status = 'fail';
                $detail = 'connected but SELECT 1 returned nothing';
            }
        } elseif ($type === 'MySQLi' || $type === 'MySQL') {
            if (!extension_loaded('mysqli')) {
                throw new RuntimeException('PHP extension mysqli not loaded');
            }
            $mysqli = @new mysqli(
                isset($conn['hostname']) ? (string) $conn['hostname'] : 'localhost',
                isset($conn['username']) ? (string) $conn['username'] : '',
                isset($conn['password']) ? (string) $conn['password'] : '',
                isset($conn['database']) ? (string) $conn['database'] : ''
            );
            if ($mysqli->connect_errno) {
                throw new RuntimeException('connect: ' . $mysqli->connect_error);
            }
            $res = $mysqli->query('SELECT 1 AS one');
            if (!$res) {
                $err = $mysqli->error;
                $mysqli->close();
                throw new RuntimeException('query: ' . $err);
            }
            $row    = $res->fetch_assoc();
            $ms     = (int) round((microtime(true) - $t0) * 1000);
            $detail = $ms . ' ms';
            $mysqli->close();
            if (!isset($row['one'])) {
                $status = 'fail';
                $detail = 'connected but SELECT 1 returned nothing';
            }
        } else {
            $status = 'skip';
            $detail = 'unsupported connection type "' . $type . '"';
        }
    } catch (Throwable $e) {
        $status = 'fail';
        $msg = $e->getMessage();
        // Friendly hints for common failures.
        if (stripos($msg, 'could not find driver') !== false) {
            $msg .= ' [install pdo_sqlsrv extension for SQL Server]';
        } elseif (stripos($msg, 'unknown database') !== false) {
            $msg .= ' [database name in config does not exist on the server]';
        } elseif (stripos($msg, "couldn't connect") !== false
                || stripos($msg, 'no route') !== false
                || stripos($msg, 'host unreachable') !== false
                || stripos($msg, 'tcp provider') !== false) {
            $msg .= ' [host unreachable — firewall / network / DB down]';
        } elseif (stripos($msg, 'login failed') !== false || stripos($msg, 'access denied') !== false) {
            $msg .= ' [bad username / password]';
        }
        $detail = substr($msg, 0, 220);
    }

    if ($status === 'fail') $any_fail = true;
    echo sprintf("%-18s %-10s %-32s %-7s %s\n",
        $name, $type, substr((string) $host_dsn, 0, 30), $status, $detail);
}

echo "\n";
if ($target_connection !== null && $tested === 0) {
    fwrite(STDERR, "No connection named '$target_connection' in database.php.\n");
    exit(2);
}
if ($any_fail) {
    echo "RESULT: one or more connections FAILED.\n";
    exit(1);
}
echo "RESULT: all connections healthy.\n";
exit(0);
