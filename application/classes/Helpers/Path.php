<?php

abstract class  Helpers_Path {

    /**
     * Get absolute file path for uploads
     */
    public static function upload($filename = '') {
        return UFONE_FILES . $filename;
    }

    /**
     * Get absolute file path for templates
     */
    public static function template($filename = '') {
        return TEMPLATES_DIR . $filename;
    }

    /**
     * Get web URL for uploads
     */
    public static function upload_url($filename = '') {
        return URL::base() . 'dramsfiles/ufone_tem_files/' . $filename;
    }

    /**
     * Get web URL for templates
     */
    public static function template_url($filename = '') {
        return URL::base() . 'dramsfiles/ufone_tem_files/templates/' . $filename;
    }

    /**
     * Check if running on Windows
     */
    public static function is_windows() {
        return IS_WINDOWS;
    }
}