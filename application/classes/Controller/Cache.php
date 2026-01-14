<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Cache extends Controller {

    public function action_clear()
    {
        // Security check - only allow from localhost or with secret key
        $secret = $this->request->query('key');
        $allowed_ip = ['127.0.0.1', '::1'];

        if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ip) && $secret !== 'your_secret_key') {
            throw new HTTP_Exception_403('Access denied');
        }

        $cache_dir = APPPATH . 'cache';
        $deleted = 0;
        $errors = [];

        if (is_dir($cache_dir)) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($cache_dir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $file) {
                if ($file->isDir()) {
                    if (!rmdir($file->getRealPath())) {
                        $errors[] = $file->getRealPath();
                    }
                } else {
                    if (unlink($file->getRealPath())) {
                        $deleted++;
                    } else {
                        $errors[] = $file->getRealPath();
                    }
                }
            }
        }

        // Also clear internal Kohana cache if enabled
        if (class_exists('Cache')) {
            Cache::instance()->delete_all();
        }

        echo json_encode([
            'status' => 'success',
            'deleted' => $deleted,
            'errors' => $errors,
            'message' => 'Cache cleared successfully'
        ]);
    }

    // Command line version
    public function action_cli()
    {
        if (PHP_SAPI !== 'cli') {
            die('CLI only');
        }

        $this->action_clear();
    }
    public function action_adminsend()
    {
        $file_name = Helpers_Path::upload(69 . ".txt");

        echo "<pre style='background:#111;color:#0f0;padding:16px;font-family:consolas;'>";
        echo "Current PHP_OS          = " . PHP_OS . "\n";
        echo "IS_WINDOWS              = " . (IS_WINDOWS ? 'true' : 'false') . "\n";
        echo "DS                      = " . DS . "\n";
        echo "DOCUMENT_ROOT           = " . DOCUMENT_ROOT . "\n";
        echo "PROJECT_ROOT            = " . PROJECT_ROOT . "\n";
        echo "UFONE_FILES             = " . UFONE_FILES . "\n";
        echo "Helpers_Path::upload()  = " . $file_name . "\n";
        echo "dirname(file)           = " . dirname($file_name) . "\n";
        echo "Directory exists?       = " . (is_dir(dirname($file_name)) ? 'YES' : 'NO') . "\n";
        echo "</pre>";
        exit;
    }
}