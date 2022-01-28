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

    function scripts($hooks)
    {
        if ($hooks != 'toplevel_page_htaccess-file-editor' && $hooks != 'toplevel_page_htaccess-file-editor_backup') {
            return;
        }
        wp_enqueue_style('htaccess-file-editor-style', HTACCESS_FILE_EDITOR_PLUGIN_URI . '/assets/css/admin.css', array('wp-codemirror'), HTACCESS_FILE_EDITOR_VERSION);

        wp_enqueue_script('htaccess-file-editor-script', HTACCESS_FILE_EDITOR_PLUGIN_URI . '/assets/js/htaccess-file-editor.js', array('jquery', 'wp-theme-plugin-editor'), HTACCESS_FILE_EDITOR_VERSION);

        $settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'text/css'));

        wp_localize_script('htaccess-file-editor-script', 'htaccess_file_editor_settings', $settings);
    }

    public function backup_page()
    {
        include_once HTACCESS_FILE_EDITOR_ABSPATH . '/templates/backup-page.php';

    }

    public function dashboard()
    {
        include_once HTACCESS_FILE_EDITOR_ABSPATH . '/templates/dashboard.php';

    }
}

new Htaccess_File_Editor_Hooks();