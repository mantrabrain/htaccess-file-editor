<?php

class Htaccess_File_Editor_Actions
{
    public function __construct()
    {
        add_action('htaccess_file_editor_restore_backup', array($this, 'restore_backup'));
        add_action('htaccess_file_editor_create_backup', array($this, 'create_backup'));
        add_action('htaccess_file_editor_delete_backup', array($this, 'delete_backup'));
        add_action('htaccess_file_editor_backup_form', array($this, 'backup_form'));
    }

    public function restore_backup()
    {
        $htaccess_file_editor_restore_result = htaccess_file_editor_restore_backup();

        if ($htaccess_file_editor_restore_result === true) {

            include_once HTACCESS_FILE_EDITOR_ABSPATH . '/templates/notices/restore-success.php';

        } else {

            include_once HTACCESS_FILE_EDITOR_ABSPATH . '/templates/notices/restore-failed.php';

        }
    }

    public function create_backup()
    {
        $create_backup_status = htaccess_file_editor_create_backup();

        if ($create_backup_status) {

            include_once HTACCESS_FILE_EDITOR_ABSPATH . '/templates/notices/backup-success.php';

        } else {
            include_once HTACCESS_FILE_EDITOR_ABSPATH . '/templates/notices/backup-failed.php';

        }

    }

    public function delete_backup()
    {
        $delete_status = htaccess_file_editor_delete_backup();

        if ($delete_status) {

            include_once HTACCESS_FILE_EDITOR_ABSPATH . '/templates/notices/delete-success.php';

        } else {
            include_once HTACCESS_FILE_EDITOR_ABSPATH . '/templates/notices/delete-failed.php';

        }

    }

    public function backup_form()
    {
        include_once HTACCESS_FILE_EDITOR_ABSPATH . '/templates/backup-form.php';

    }
}

new Htaccess_File_Editor_Actions();