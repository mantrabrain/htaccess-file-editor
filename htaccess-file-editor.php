<?php
/*
Plugin Name: Htaccess File Editor
Plugin URI: https://mantrabrain.com/
Description: Simple editor htaccess file without using FTP client.
Version: 1.0.10
Text Domain: htaccess-file-editor
Author: Mantrabrain
Author URI: https://mantrabrain.com
Requires at least: 3.0.0
Tested up to: 5.9
License: GPLv2 or later
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
// Define HTACCESS_FILE_EDITOR_PLUGIN_FILE.
if (!defined('HTACCESS_FILE_EDITOR_FILE')) {
    define('HTACCESS_FILE_EDITOR_FILE', __FILE__);
}

// Define HTACCESS_FILE_EDITOR_VERSION.
if (!defined('HTACCESS_FILE_EDITOR_VERSION')) {
    define('HTACCESS_FILE_EDITOR_VERSION', '1.0.10');
}

// Define HTACCESS_FILE_EDITOR_PLUGIN_URI.
if (!defined('HTACCESS_FILE_EDITOR_PLUGIN_URI')) {
    define('HTACCESS_FILE_EDITOR_PLUGIN_URI', plugins_url('', HTACCESS_FILE_EDITOR_FILE));
}

// Define HTACCESS_FILE_EDITOR_PLUGIN_DIR.
if (!defined('HTACCESS_FILE_EDITOR_PLUGIN_DIR')) {
    define('HTACCESS_FILE_EDITOR_PLUGIN_DIR', plugin_dir_path(HTACCESS_FILE_EDITOR_FILE));
}


// Include the main Htaccess_File_Editor class.
if (!class_exists('Htaccess_File_Editor')) {
    include_once dirname(__FILE__) . '/includes/class-htaccess-file-editor.php';
}


/**
 * Main instance of Htaccess_File_Editor.
 *
 * Returns the main instance of Htaccess_File_Editor to prevent the need to use globals.
 *
 * @return Htaccess_File_Editor
 * @since  1.0.0
 */
function htaccess_file_editor()
{
    return Htaccess_File_Editor::instance();
}

// Global for backwards compatibility.
$GLOBALS['htaccess_file_editor_instance'] = htaccess_file_editor();
