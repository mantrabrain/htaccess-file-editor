<?php
if (!defined('ABSPATH')) die('You do not have sufficient permissions to access this file.');
if (!current_user_can('activate_plugins')) {
    echo __('You do not have permission to view this page', 'htaccess-file-editor');
}

$htaccess_file_editor_backup_path = WP_CONTENT_URL . '/htaccess.backup';
$htaccess_file_editor_origin_path = ABSPATH . '.htaccess';
?>
    <div class="wrap">
        <h2 class="wphe-title" style="padding-left:50px;">Htaccess File Editor</h2>
        <?php
        //============================ Uložení Htaccess souboru =======================================
        if (!empty($_POST['submit']) and !empty($_POST['save_htaccess']) and check_admin_referer('wphe_save', 'wphe_save')) {
            $WPHE_new_content = $_POST['ht_content'];
            htaccess_file_editor_delete_backup();
            if (htaccess_file_editor_create_backup()) {
                if (htaccess_file_editor_write_new_htaccess($WPHE_new_content)) {
                    echo '<div id="message" class="updated fade"><p><strong>' . __('File has been successfully changed', 'wphe') . '</strong></p></div>';
                    ?>
                    <p><?php _e('You have made changes to the htaccess file. The original file was automatically backed up (in <code>wp-content</code> folder)', 'wphe'); ?>
                        <br/>
                        <a href="<?php echo get_option('home'); ?>/"
                           target="_blank"><?php _e('Check the functionality of your site (the links to the articles or categories).', 'wphe'); ?></a>. <?php _e('If something is not working properly restore the original file from backup', 'wphe'); ?>
                    </p>
                    <div class="postbox" style="float: left; width: 95%; padding: 15px;">
                        <form method="post" action="<?php echo admin_url('admin.php?page=htaccess-file-editor') ?>">
                            <?php wp_nonce_field('wphe_delete', 'wphe_delete'); ?>
                            <input type="hidden" name="delete_backup" value="delete"/>
                            <p class="submit"><?php _e('If everything works properly, you can delete the backup file:', 'wphe'); ?>
                                <input type="submit" class="button button-primary" name="submit"
                                       value="<?php _e('Remove backup &raquo;', 'wphe'); ?>"/>&nbsp;<?php echo __('or', 'wphe'); ?>
                                &nbsp;<a
                                        href="<?php echo admin_url('admin.php?page=htaccess-file-editor') ?>"><?php _e('restore the original file from backup', 'wphe'); ?></a>
                            </p>
                        </form>
                    </div>
                    <?php
                } else {
                    echo '<div id="message" class="error fade"><p><strong>' . __('The file could not be saved!', 'wphe') . '</strong></p></div>';
                    echo '<div id="message" class="error fade"><p><strong>' . __('Due to server configuration can not change permissions on files or create new files', 'wphe') . '</strong></p></div>';
                }
            } else {
                echo '<div id="message" class="error fade"><p><strong>' . __('The file could not be saved!', 'wphe') . '</strong></p></div>';
                echo '<div id="message" class="error fade"><p><strong>' . __('Unable to create backup of the original file! <code>wp-content</code> folder is not writeable! Change the permissions this folder!', 'wphe') . '</strong></p></div>';
            }
            unset($WPHE_new_content);
            //============================ Vytvoření nového Htaccess souboru ================================
        } elseif (!empty($_POST['submit']) and !empty($_POST['create_htaccess']) and check_admin_referer('wphe_create', 'wphe_create')) {
            if (htaccess_file_editor_write_new_htaccess('# Created by Htaccess File Editor') === false) {
                echo '<div id="message" class="error fade"><p><strong>' . __('Htaccess file is not created.', 'wphe') . '</p></div>';
                echo '<div id="message" class="error fade"><p><strong>' . __('Due to server configuration can not change permissions on files or create new files', 'wphe') . '</strong></p></div>';
            } else {
                echo '<div id="message" class="updated fade"><p><strong>' . __('Htaccess file was successfully created.', 'wphe') . '</strong></p></div>';
                echo '<div id="message" class="updated fade"><p><strong><a href="' . admin_url('admin.php?page=htaccess-file-editor') . '">' . __('View new Htaccess file', 'wphe') . '</a></strong></p></div>';
            }
            //============================ Smazání zálohy =======================================
        } elseif (!empty($_POST['submit']) and !empty($_POST['delete_backup']) and check_admin_referer('wphe_delete', 'wphe_delete')) {
            if (htaccess_file_editor_delete_backup() === false) {
                echo '<div id="message" class="error fade"><p><strong>' . __('Backup file could not be removed! <code>wp-content</code> folder is not writeable! Change the permissions this folder!', 'wphe') . '</p></div>';
            } else {
                echo '<div id="message" class="updated fade"><p><strong>' . __('Backup file has been successfully removed.', 'wphe') . '</strong></p></div>';
            }
            //============================ Home ================================================
        } else {
            ?>
            <p><?php _e('Using this editor you can easily modify your htaccess file without having to use an FTP client.', 'wphe'); ?></p>
            <p class="wphe-red"><?php _e('<strong>WARNING:</strong> Any error in this file may cause malfunction of your site!', 'wphe'); ?>
                <br/>
                <?php _e('Edit htaccess file should therefore be performed only by experienced users!', 'wphe'); ?><br/>
            </p>
            <div class="postbox wphe-box">
                <h3 class="wphe-title"><?php _e('Information for editing htaccess file', 'wphe'); ?></h3>
                <p><?php _e('For more information on possible adjustments to this file, please visit', 'wphe'); ?> <a
                            href="http://httpd.apache.org/docs/current/howto/htaccess.html" target="_blank">Apache
                        Tutorial: .htaccess files</a> <?php _e('or', 'wphe'); ?> <a
                            href="http://net.tutsplus.com/tutorials/other/the-ultimate-guide-to-htaccess-files/"
                            target="_blank">The Ultimate Guide to .htaccess Files</a>. </p>
                <p><a href="http://www.google.com/#sclient=psy&q=htaccess+how+to"
                      target="_blank"><?php _e('use the Google search.', 'wphe'); ?></a></p>
            </div>
            <?php
            if (!file_exists($htaccess_file_editor_origin_path)) {
                echo '<div class="postbox wphe-box">';
                echo '<pre class="wphe-red">' . __('Htaccess file does not exists!', 'wphe') . '</pre>';
                echo '</div>';
                $success = false;
            } else {
                $success = true;
                if (!is_readable($htaccess_file_editor_origin_path)) {
                    echo '<div class="postbox wphe-box">';
                    echo '<pre class="wphe-red">' . __('Htaccess file cannot read!', 'wphe') . '</pre>';
                    echo '</div>';
                    $success = false;
                }
                if ($success == true) {
                    @chmod($htaccess_file_editor_origin_path, 0644);
                    $WPHE_htaccess_content = @file_get_contents($htaccess_file_editor_origin_path, false, NULL);
                    if ($WPHE_htaccess_content === false) {
                        echo '<div class="postbox wphe-box">';
                        echo '<pre class="wphe-red">' . __('Htaccess file cannot read!', 'wphe') . '</pre>';
                        echo '</div>';
                        $success = false;
                    } else {
                        $success = true;
                    }
                }
            }

            if ($success == true) {
                ?>
                <div class="postbox wphe-box">
                    <form method="post" action="<?php echo admin_url('admin.php?page=htaccess-file-editor') ?>">
                        <input type="hidden" name="save_htaccess" value="save"/>
                        <?php wp_nonce_field('wphe_save', 'wphe_save'); ?>
                        <h3 class="wphe-title"><?php _e('Content of the Htaccess file', 'wphe'); ?></h3>
                        <textarea name="ht_content" class="wphe-textarea"
                                  wrap="off"><?php echo $WPHE_htaccess_content; ?></textarea>
                        <p class="submit"><input type="submit" class="button button-primary" name="submit"
                                                 value="<?php _e('Save file &raquo;', 'wphe'); ?>"/></p>
                    </form>
                </div>
                <?php
                unset($WPHE_htaccess_content);
            } else {
                ?>
                <div class="postbox wphe-box" style="background: #E0FCE1;">
                    <form method="post" action="<?php echo admin_url('admin.php?page=htaccess-file-editor') ?>">
                        <input type="hidden" name="create_htaccess" value="create"/>
                        <?php wp_nonce_field('wphe_create', 'wphe_create'); ?>
                        <p class="submit"><?php _e('Create new <code>.htaccess</code> file?', 'wphe'); ?> <input
                                    type="submit" class="button button-primary" name="submit"
                                    value="<?php _e('Create &raquo;', 'wphe'); ?>"/></p>
                    </form>
                </div>
                <?php
            }
            unset($success);
        }
        ?>

        <p style="clear:both;">&nbsp;</p>
    </div>
<?php
unset($htaccess_file_editor_origin_path);
unset($htaccess_file_editor_backup_path);

