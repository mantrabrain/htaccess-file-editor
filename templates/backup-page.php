<?php
if (!defined('ABSPATH')) {
    die('You do not have sufficient permissions to access this file.');
}
if (!current_user_can('activate_plugins')) {
    echo 'You do not have sufficient permissions to access this file.';
    return;
}

?>
    <div class="wrap">
        <h2 class="htaccess-file-editor-title"
            style="padding-left:50px"><?php __('Htaccess File Editor -', 'htaccess-file-editor'); ?><?php _e('Backup', 'htaccess-file-editor'); ?></h2>
        <?php
        //============================ Restore Backup ===================================
        if (!empty($_POST['submit']) && !empty($_POST['restore_backup']) && check_admin_referer('htaccess_file_editor_restoreb', 'htaccess_file_editor_restoreb')) {

            do_action('htaccess_file_editor_restore_backup');


            //============================== Create Backup ===================================
        } elseif (!empty($_POST['submit']) && !empty($_POST['create_backup']) && check_admin_referer('htaccess_file_editor_createb', 'htaccess_file_editor_createb')) {

            do_action('htaccess_file_editor_create_backup');


            //============================== Delete Backup ====================================
        } elseif (!empty($_POST['submit']) && !empty($_POST['delete_backup']) && check_admin_referer('htaccess_file_editor_deleteb', 'htaccess_file_editor_deleteb')) {

            do_action('htaccess_file_editor_delete_backup');

        } else {

            do_action('htaccess_file_editor_backup_form');


        }
        ?>

        <p style="clear:both;">&nbsp;</p>
    </div>
<?php
