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
define('UFONE_FILES',   PROJECT_ROOT . 'dramsfiles' . DS . 'ufone_tem_files' . DS);
define('UPLOADS_DIR',   PROJECT_ROOT . 'uploads' . DS);
define('TEMPLATES_DIR', PROJECT_ROOT . 'templates' . DS);
define('EXPORTS_DIR',   PROJECT_ROOT . 'exports' . DS);
define('TEMP_DIR',      PROJECT_ROOT . 'temp' . DS);

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

Kohana::init([
    'base_url'   => $base_url,
    'index_file' => false,
    'errors'     => true,
    'profile'    => false,
    'caching'    => (Kohana::$environment === Kohana::PRODUCTION),
]);

// Logging & Config
Kohana::$log->attach(new Log_File(APPPATH . 'logs'));
Kohana::$config->attach(new Config_File);

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