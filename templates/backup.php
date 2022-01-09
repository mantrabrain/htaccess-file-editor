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
        <h2 class="wphe-title" style="padding-left:50px">Htaccess File Editor - <?php _e('Backup', 'wphe'); ?></h2>
        <?php
        //============================ Restore Backup ===================================
        if (!empty($_POST['submit']) && !empty($_POST['restore_backup']) && check_admin_referer('wphe_restoreb', 'wphe_restoreb')) {

            $wphe_restore_result = htaccess_file_editor_restore_backup();
            if ($wphe_restore_result === false) {
                echo '<div id="message" class="error fade"><p><strong>' . __('Unable to restore backup! Probably the wrong setting write permissions to the files.', 'wphe') . '</strong></p></div>';
                echo '<div class="postbox wphe-box">';
                echo '<p>' . __('The backup file is located in the <code>wp-content</code> folder.', 'wphe') . '</p>';
                echo '</div>';
            } elseif ($wphe_restore_result === true) {
                echo '<div id="message" class="updated fade"><p><strong>' . __('Backup was restored successfully', 'wphe') . '</strong></p></div>';
                echo '<div id="message" class="updated fade"><p><strong>' . __('Old backup file was deleted successfully', 'wphe') . '</strong></p></div>';
            } else {
                echo '<div id="message" class="error fade"><p><strong>' . __('Unable to restore backup!', 'wphe') . '</strong></p></div>';
                echo '<div class="postbox wphe-box" style="background: #FFEECE;">';
                echo '<p class="wphe-red">' . __('This is contents of the original file, put it into a file manually', 'wphe') . ':</p>';
                echo '<textarea class="wphe-textarea">' . $wphe_restore_result . '</textarea>';
                echo '</div>';
            }
            //============================== Create Backup ===================================
        } elseif (!empty($_POST['submit']) && !empty($_POST['create_backup']) && check_admin_referer('wphe_createb', 'wphe_createb')) {
            if (htaccess_file_editor_create_backup()) {
                echo '<div id="message" class="updated fade"><p><strong>' . __('Backup file was created successfully', 'wphe') . '</strong></p></div>';
                echo '<div class="postbox wphe-box">';
                echo '<p>' . __('The backup file is located in the <code>wp-content</code> folder.', 'wphe') . '</p>';
                echo '</div>';
            } else {
                echo '<div id="message" class="error fade"><p><strong>' . __('Unable to create backup! <code>wp-content</code> folder is not writeable! Change the permissions this folder manually!', 'wphe') . '</strong></p></div>';
                echo '<div id="message" class="error fade"><p><strong>' . __('Due to server configuration can not change permissions on files or create new files', 'wphe') . '</strong></p></div>';
            }
            //============================== Delete Backup ====================================
        } elseif (!empty($_POST['submit']) && !empty($_POST['delete_backup']) && check_admin_referer('wphe_deleteb', 'wphe_deleteb')) {
            if (htaccess_file_editor_delete_backup()) {
                echo '<div id="message" class="updated fade"><p><strong>' . __('Backup file was successfully removed', 'wphe') . '</strong></p></div>';
            } else {
                echo '<div id="message" class="error fade"><p><strong>' . __('Backup file could not be removed! Probably the wrong setting write permissions to the files.', 'wphe') . '</strong></p></div>';
                echo '<div id="message" class="error fade"><p><strong>' . __('Due to server configuration can not change permissions on files or create new files', 'wphe') . '</strong></p></div>';
            }
            //============================== Home ==============================================
        } else {
            if (file_exists(ABSPATH . 'wp-content/htaccess.backup')) {
                echo '<div class="postbox wphe-box" style="background: #FFEECE;">';
                ?>
                <form method="post"
                      action="<?php echo admin_url('admin.php?page=htaccess-file-editor-backup') ?>">
                    <?php wp_nonce_field('wphe_restoreb', 'wphe_restoreb'); ?>
                    <input type="hidden" name="restore_backup" value="restore"/>
                    <p class="submit"><?php _e('Do you want to restore the backup file?', 'wphe'); ?> <input
                                type="submit" class="button button-primary" name="submit"
                                value="<?php _e('Restore backup &raquo;', 'wphe'); ?>"/></p>
                </form>
                <?php
                echo '</div>';
                echo '<div class="postbox wphe-box" style="background: #FFEECE;">';
                ?>
                <form method="post" action="<?php echo admin_url('admin.php?page=htaccess-file-editor-backup') ?>">
                    <?php wp_nonce_field('wphe_deleteb', 'wphe_deleteb'); ?>
                    <input type="hidden" name="delete_backup" value="delete"/>
                    <p class="submit"><?php _e('Do you want to delete a backup file?', 'wphe'); ?> <input
                                type="submit" class="button button-primary" name="submit"
                                value="<?php _e('Remove backup &raquo;', 'wphe'); ?>"/></p>
                </form>
                <?php
                echo '</div>';
            } else {
                echo '<div class="postbox wphe-box">';
                echo '<pre class="wphe-red">' . __('Backup file not found...', 'wphe') . '</pre>';
                echo '</div>';

                echo '<div class="postbox wphe-box" style="background: #E0FCE1;">';
                ?>
                <form method="post" action="<?php echo admin_url('admin.php?page=htaccess-file-editor-backup') ?>">
                    <?php wp_nonce_field('wphe_createb', 'wphe_createb'); ?>
                    <input type="hidden" name="create_backup" value="create"/>
                    <p class="submit"><?php _e('Do you want to create a new backup file?', 'wphe'); ?> <input
                                type="submit" class="button button-primary" name="submit"
                                value="<?php _e('Create new &raquo;', 'wphe'); ?>"/></p>
                </form>
                <?php
                echo '</div>';
            }
        }
        ?>

        <p style="clear:both;">&nbsp;</p>
    </div>
<?php
