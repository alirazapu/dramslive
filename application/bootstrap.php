<?php defined('SYSPATH') or die('No direct script access.');

// Prevent multiple inclusions (this was the root cause of broken PROJECT_ROOT)
if (defined('BOOTSTRAP_LOADED')) {
    return;
}
define('BOOTSTRAP_LOADED', true);

// -----------------------------------------------------------------------------
// Environment setup
// -----------------------------------------------------------------------------

require SYSPATH . 'classes/Kohana/Core' . EXT;

if (is_file(APPPATH . 'classes/Kohana' . EXT)) {
    require APPPATH . 'classes/Kohana' . EXT;
} else {
    require SYSPATH . 'classes/Kohana' . EXT;
}

date_default_timezone_set('Asia/Karachi');
setlocale(LC_ALL, 'en_US.utf-8');

spl_autoload_register(array('Kohana', 'auto_load'));
ini_set('unserialize_callback_func', 'spl_autoload_call');
mb_substitute_character('none');

// -----------------------------------------------------------------------------
// Configuration & Initialization
// -----------------------------------------------------------------------------

I18n::lang('en-us');

if (isset($_SERVER['SERVER_PROTOCOL'])) {
    HTTP::$protocol = $_SERVER['SERVER_PROTOCOL'];
}

// Environment from server variable (if set)
if (isset($_SERVER['KOHANA_ENV'])) {
    $env_constant = 'Kohana::' . strtoupper($_SERVER['KOHANA_ENV']);
    if (defined($env_constant)) {
        Kohana::$environment = constant($env_constant);
    }
}

// Windows detection
define('IS_WINDOWS', strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
define('DS', DIRECTORY_SEPARATOR);

// More reliable project root detection
$doc_root   = rtrim(str_replace(['/', '\\'], DS, $_SERVER['DOCUMENT_ROOT']), DS);
$script_dir = rtrim(str_replace(['/', '\\'], DS, dirname($_SERVER['SCRIPT_FILENAME'])), DS);

// Try to find the project root intelligently
$possible_root = realpath($script_dir . DS . '..'); // most common: index.php in public/
if ($possible_root === false || !is_dir($possible_root)) {
    $possible_root = realpath($doc_root . DS . 'drams');
}
if ($possible_root === false || !is_dir($possible_root)) {
    // Last resort fallback
    $possible_root = $doc_root . DS . 'drams';
}

$project_root = rtrim(str_replace(['/', '\\'], DS, $possible_root), DS) . DS;

define('DOCUMENT_ROOT', $doc_root . DS);
define('PROJECT_ROOT',  $project_root);

// Directory constants
define('UFONE_FILES',   PROJECT_ROOT .  'drams' . DS . 'dramsfiles' . DS . 'ufone_tem_files' . DS);
define('UPLOADS_DIR',   PROJECT_ROOT .  'drams' . DS . 'uploads' . DS);
define('TEMPLATES_DIR', PROJECT_ROOT .  'drams' . DS . 'application' .DS. 'views' . DS. 'templates' . DS);
define('EXPORTS_DIR',   PROJECT_ROOT .  'drams' . DS . 'dramsfiles' . DS. 'exports' . DS);
define('TEMP_DIR',      PROJECT_ROOT  . 'drams' . DS . 'dramsfiles' . DS .'temp' . DS);
// User-side "Requested Attachment" upload destination (CDR / Subscriber /
// Location / CNIC-SIMs forms). Mirrors the per-row attachment column on
// admin_request that admin_request_sent_form already supports.
define('REQUESTED_ATTACHMENTS', PROJECT_ROOT . 'drams' . DS . 'dramsfiles' . DS . 'requested_attachment' . DS);

// SCOM-specific attachments (Copy of FIR + Cover Letter). For SCOM
// (company mnc=8) the analyst must supply both files on the request
// form; they are stored here and later attached to the outgoing email
// when the cron sender picks the SCOM row up.
define('SCOM_ATTACHMENTS', PROJECT_ROOT . 'drams' . DS . 'dramsfiles' . DS . 'scom_attachment' . DS);

define('FAMILYTREE_TERMP_IMAGES',   UPLOADS_DIR . 'familytree_temp_images' . DS);
define('TRAVELHISTORY_TERMP_IMAGES', UPLOADS_DIR . 'travelhistory_temp_images' . DS);
define('VERISYS_TERMP_IMAGES',      UPLOADS_DIR . 'verisys_temp_images' . DS);

// Development logging (optional – comment out in production)
if (Kohana::$environment === Kohana::DEVELOPMENT) {
    error_log(sprintf(
        "Paths:\n  DOCUMENT_ROOT = %s\n  PROJECT_ROOT  = %s\n  UFONE_FILES   = %s",
        DOCUMENT_ROOT, PROJECT_ROOT, UFONE_FILES
    ));
}

// -----------------------------------------------------------------------------
// Kohana Initialization
// -----------------------------------------------------------------------------

$is_https = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
);
$scheme = $is_https ? 'https://' : 'http://';

$base_url = $scheme . 'ctd.drams.com'; // default
$env_var = getenv('KOHANA_ENV');

if ($env_var) {
    switch (strtoupper($env_var)) {
        case 'DEVELOPMENT':
            $base_url = $scheme . 'dev.ctd.drams.com';
            Kohana::$environment = Kohana::DEVELOPMENT;
            break;
        case 'STAGING':
            $base_url = $scheme . 'stage.ctd.drams.com';
            Kohana::$environment = Kohana::STAGING;
            break;
        case 'TESTING':
            $base_url = $scheme . 'test.ctd.drams.com';
            Kohana::$environment = Kohana::TESTING;
            break;
        case 'PRODUCTION':
        default:
            Kohana::$environment = Kohana::PRODUCTION;
            break;
    }
} else {
    Kohana::$environment = Kohana::PRODUCTION;
}

/**
 * Session hardening. Must run before anything calls Session::instance(),
 * because these are read by session_start().
 *
 * [!!] save_path gets its own directory. The XAMPP default tmp directory is
 * shared with every other app on this box, and PHP's garbage collector
 * applies the *collecting* app's gc_maxlifetime to every file in the
 * directory - so a neighbouring app with a shorter lifetime would keep
 * expiring DRAMS logins early. An isolated path makes the timeout ours.
 *
 * Note gc_maxlifetime only makes a session file *eligible* for deletion; PHP
 * does not check it on read. The authoritative idle timeout is enforced in
 * Controller_Working::before(), which is what users actually experience.
 */
define('DRAMS_SESSION_LIFETIME', 3600); // 1 hour idle

// A subdirectory of the path PHP already uses: guaranteed writable by the
// web server user, correct on Windows and Linux alike, and outside the web
// root (session files carry the auth key, so they must never be servable).
// sys_get_temp_dir() is deliberately NOT used - under Apache it resolves to
// a different directory than it does on the CLI.
$default_save_path = ini_get('session.save_path');
$session_path = ($default_save_path !== FALSE AND $default_save_path !== '')
    ? rtrim($default_save_path, "\\/") . DIRECTORY_SEPARATOR . 'drams_sessions'
    : NULL;

if ($session_path !== NULL) {
    if (!is_dir($session_path)) {
        @mkdir($session_path, 0700, TRUE);
    }

    if (is_dir($session_path) AND is_writable($session_path)) {
        ini_set('session.save_path', $session_path);
    }
}

ini_set('session.gc_maxlifetime', DRAMS_SESSION_LIFETIME);
ini_set('session.gc_divisor', 100);        // actually sweep; the 1/1000 default rarely fires
ini_set('session.cookie_lifetime', 0);     // cookie dies when the browser closes
ini_set('session.use_strict_mode', 1);     // reject attacker-supplied session ids
// [!!] HttpOnly is NOT set here. Kohana's Session_Native calls
// session_set_cookie_params() from Cookie::$httponly, overwriting any ini
// value - so the real switch lives in application/config/cookie.php.
ini_set('session.use_only_cookies', 1);    // never accept a session id from the URL

// The env var above selects the *environment* (and therefore the database),
// but the base URL must follow the host the request actually arrived on -
// otherwise browsing dev.ctd.drams.com emits ctd.drams.com in every form
// action and redirect, bouncing the user onto a different host mid-flow
// (which breaks the login/OTP round-trip outright).
// Whitelisted so a forged Host header cannot rewrite our generated URLs.
$allowed_hosts = [
    'ctd.drams.com',
    'dev.ctd.drams.com',
    'stage.ctd.drams.com',
    'test.ctd.drams.com',
];

if (!empty($_SERVER['HTTP_HOST'])) {
    $request_host = strtolower($_SERVER['HTTP_HOST']);
    $host_only    = explode(':', $request_host)[0];

    if (in_array($host_only, $allowed_hosts, TRUE)) {
        $base_url = $scheme . $request_host;
    }
}

Kohana::init([
    'base_url'   => $base_url,
    'index_file' => false,
    'errors'     => true,
    'profile'    => false,
    'caching'    => false,
]);

// Logging & Config
Kohana::$log->attach(new Log_File(APPPATH . 'logs'));
Kohana::$config->attach(new Config_File);

// -----------------------------------------------------------------------------
// Framework-Level Error & Exception Logging (Production)
// -----------------------------------------------------------------------------
if (Kohana::$environment === Kohana::PRODUCTION) {
    // Custom error handler for production
    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        // Don't log suppressed errors (@-operator)
        if (!(error_reporting() & $errno)) {
            return false;
        }
        
        $error_types = [
            E_ERROR => 'error',
            E_WARNING => 'warning',
            E_PARSE => 'error',
            E_NOTICE => 'warning',
            E_CORE_ERROR => 'error',
            E_CORE_WARNING => 'warning',
            E_COMPILE_ERROR => 'error',
            E_COMPILE_WARNING => 'warning',
            E_USER_ERROR => 'error',
            E_USER_WARNING => 'warning',
            E_USER_NOTICE => 'info',
            E_STRICT => 'info',
            E_RECOVERABLE_ERROR => 'error',
            E_DEPRECATED => 'warning',
            E_USER_DEPRECATED => 'warning',
        ];
        
        $severity = isset($error_types[$errno]) ? $error_types[$errno] : 'error';
        
        // Log to Model_ErrorLog
        Model_ErrorLog::log(
            'php_error',
            $errstr,
            [
                'file' => $errfile,
                'line' => $errline,
                'error_code' => $errno
            ],
            null,
            'runtime_error',
            'framework',
            $severity
        );
        
        // Don't execute PHP internal error handler
        return true;
    });
    
    // Custom exception handler for production
    set_exception_handler(function($exception) {
        // Fallback to error_log first, in case Model_ErrorLog fails
        error_log("[" . date('c') . "] Uncaught Exception: " . $exception->getMessage());
        error_log("File: " . $exception->getFile() . " Line: " . $exception->getLine());
        
        // Try to log to database, but don't fail if it doesn't work
        try {
            Model_ErrorLog::log(
                'php_exception',
                $exception->getMessage(),
                [
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'exception_class' => get_class($exception)
                ],
                $exception->getTraceAsString(),
                'uncaught_exception',
                'framework',
                'error'
            );
        } catch (Exception $logException) {
            // If logging fails, just log to error_log
            error_log("[" . date('c') . "] Failed to log exception to database: " . $logException->getMessage());
        }
        
        // Show generic error page to user
        if (!headers_sent()) {
            header('HTTP/1.1 500 Internal Server Error');
            echo '<h1>An error occurred</h1><p>The error has been logged and will be reviewed.</p>';
        }
        exit(1);
    });
}

// Modules
Kohana::modules([
    'auth'       => MODPATH . 'auth',
    'database'   => MODPATH . 'database',
    'image'      => MODPATH . 'image',
    'orm'        => MODPATH . 'orm',
    'mysqli'     => MODPATH . 'mysqli',
    'phpexcel'   => MODPATH . 'phpexcel',
    'phpmailer'  => MODPATH . 'phpmailer',
]);

// Cookie settings
Cookie::$salt      = 'ctdkpkdrams';
Kohana_Cookie::$expiration = 86400; // 1 day (instead of 1 second – probably a typo?)

// [!!] application/config/cookie.php is NOT auto-applied - Kohana's Cookie
// class reads these statics only, which is why the statics above are set
// here. HttpOnly must therefore be set here too, and it must happen before
// anything starts the session: Session_Native feeds Cookie::$httponly into
// session_set_cookie_params(), overriding the php.ini value.
// Keeps JavaScript (and therefore any XSS) away from both the session
// cookie and the "authautologin" remember-me token.
Cookie::$httponly  = TRUE;
// Cookie::$secure must become TRUE once the site is served over HTTPS only.
Cookie::$secure    = FALSE;

// -----------------------------------------------------------------------------
// Routes
// -----------------------------------------------------------------------------

Route::set('template', 'template(/<action>)')
    ->defaults([
        'controller' => 'login',
    ]);

Route::set('default', '(<controller>(/<action>(/<id>)(/<id2>)(/<id3>)(/<id4>)(/<ctr>)))')
    ->defaults([
        'controller' => 'login',
        'action'     => 'index',
    ]);

// URL constants
define('BASE_URL',          $base_url);
define('UFONE_FILES_URL',   BASE_URL . 'dramsfiles/ufone_tem_files/');
define('UPLOADS_URL',       BASE_URL . 'uploads/');
define('TEMPLATES_URL',     BASE_URL . 'templates/');