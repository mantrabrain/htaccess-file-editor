<?php

class Htaccess_File_Editor_Hooks
{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'scripts'), 10);


    }

    function admin_menu()
    {
        if (current_user_can('activate_plugins')) {

            add_menu_page('Htaccess File Editor', 'Htaccess', 'activate_plugins', 'htaccess-file-editor', array($this, 'dashboard'), HTACCESS_FILE_EDITOR_PLUGIN_URI . '/assets/images/icon.png');

            add_submenu_page('htaccess-file-editor', 'Backup', 'Backup', 'activate_plugins', 'htaccess-file-editor-backup', array($this, 'backup_page'));


        }

    }

    function scripts()
    {
        wp_enqueue_style('htaccess-file-editor-style', HTACCESS_FILE_EDITOR_PLUGIN_URI . '/assets/css/admin.css', array(), HTACCESS_FILE_EDITOR_VERSION);

    }

    public function backup_page()
    {
        include_once HTACCESS_FILE_EDITOR_ABSPATH . '/templates/backup.php';

    }

    public function dashboard()
    {
        include_once HTACCESS_FILE_EDITOR_ABSPATH . '/templates/dashboard.php';

    }
}

new Htaccess_File_Editor_Hooks();