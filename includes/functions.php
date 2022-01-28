<?php
if (!defined('ABSPATH')) die('You do not have sufficient permissions to access this file.');


function htaccess_file_editor_create_backup()
{
    $WPHE_backup_path = ABSPATH . 'wp-content/htaccess.backup';
    $WPHE_orig_path = ABSPATH . '.htaccess';
    @clearstatcache();

    htaccess_file_editor_create_secure_wpcontent();

    if (file_exists($WPHE_backup_path)) {
        htaccess_file_editor_delete_backup();

        if (file_exists(ABSPATH . '.htaccess')) {
            $htaccess_content_orig = @file_get_contents($WPHE_orig_path, false, NULL);
            $htaccess_content_orig = trim($htaccess_content_orig);
            $htaccess_content_orig = str_replace('\\\\', '\\', $htaccess_content_orig);
            $htaccess_content_orig = str_replace('\"', '"', $htaccess_content_orig);
            @chmod($WPHE_backup_path, 0666);
            $WPHE_success = @file_put_contents($WPHE_backup_path, $htaccess_content_orig, LOCK_EX);
            if ($WPHE_success === false) {
                unset($WPHE_backup_path);
                unset($WPHE_orig_path);
                unset($htaccess_content_orig);
                unset($WPHE_success);
                return false;
            } else {
                unset($WPHE_backup_path);
                unset($WPHE_orig_path);
                unset($htaccess_content_orig);
                unset($WPHE_success);
                return true;
            }
            @chmod($WPHE_backup_path, 0644);
        } else {
            unset($WPHE_backup_path);
            unset($WPHE_orig_path);
            return false;
        }
    } else {
        if (file_exists(ABSPATH . '.htaccess')) {
            $htaccess_content_orig = @file_get_contents($WPHE_orig_path, false, NULL);
            $htaccess_content_orig = trim($htaccess_content_orig);
            $htaccess_content_orig = str_replace('\\\\', '\\', $htaccess_content_orig);
            $htaccess_content_orig = str_replace('\"', '"', $htaccess_content_orig);
            @chmod($WPHE_backup_path, 0666);
            $WPHE_success = @file_put_contents($WPHE_backup_path, $htaccess_content_orig, LOCK_EX);
            if ($WPHE_success === false) {
                unset($WPHE_backup_path);
                unset($WPHE_orig_path);
                unset($htaccess_content_orig);
                unset($WPHE_success);
                return false;
            } else {
                unset($WPHE_backup_path);
                unset($WPHE_orig_path);
                unset($htaccess_content_orig);
                unset($WPHE_success);
                return true;
            }
            @chmod($WPHE_backup_path, 0644);
        } else {
            unset($WPHE_backup_path);
            unset($WPHE_orig_path);
            return false;
        }
    }
}


function htaccess_file_editor_create_secure_wpcontent()
{
    $htaccess_file_editor_secure_path = ABSPATH . 'wp-content/.htaccess';
    $htaccess_file_editor_secure_text = '
# Htaccess File Editor - Secure backups
<files htaccess.backup>
order allow,deny
deny from all
</files>
';

    if (is_readable(ABSPATH . 'wp-content/.htaccess')) {
        $htaccess_file_editor_secure_content = @file_get_contents(ABSPATH . 'wp-content/.htaccess');

        if ($htaccess_file_editor_secure_content !== false) {
            if (strpos($htaccess_file_editor_secure_content, 'Secure backups') === false) {
                unset($htaccess_file_editor_secure_content);
                $htaccess_file_editor_create_sec = @file_put_contents(ABSPATH . 'wp-content/.htaccess', $htaccess_file_editor_secure_text, FILE_APPEND | LOCK_EX);
                if ($htaccess_file_editor_create_sec !== false) {
                    unset($htaccess_file_editor_secure_text);
                    unset($htaccess_file_editor_create_sec);
                    return true;
                } else {
                    unset($htaccess_file_editor_secure_text);
                    unset($htaccess_file_editor_create_sec);
                    return false;
                }
            } else {
                unset($htaccess_file_editor_secure_content);
                return true;
            }
        } else {
            unset($htaccess_file_editor_secure_content);
            return false;
        }
    } else {
        if (file_exists(ABSPATH . 'wp-content/.htaccess')) {
            return false;
        } else {
            $htaccess_file_editor_create_sec = @file_put_contents(ABSPATH . 'wp-content/.htaccess', $htaccess_file_editor_secure_text, LOCK_EX);
            if ($htaccess_file_editor_create_sec !== false) {
                return true;
            } else {
                return false;
            }
        }
    }
}


function htaccess_file_editor_restore_backup()
{
    $htaccess_file_editor_backup_path = ABSPATH . 'wp-content/htaccess.backup';
    $WPHE_orig_path = ABSPATH . '.htaccess';
    @clearstatcache();

    if (!file_exists($htaccess_file_editor_backup_path)) {
        unset($htaccess_file_editor_backup_path);
        unset($WPHE_orig_path);
        return false;
    } else {
        if (file_exists($WPHE_orig_path)) {
            if (is_writable($WPHE_orig_path)) {
                @unlink($WPHE_orig_path);
            } else {
                @chmod($WPHE_orig_path, 0666);
                @unlink($WPHE_orig_path);
            }
        }
        $htaccess_file_editor_htaccess_content_backup = @file_get_contents($htaccess_file_editor_backup_path, false, NULL);
        if (htaccess_file_editor_write_new_htaccess($htaccess_file_editor_htaccess_content_backup) === false) {
            unset($htaccess_file_editor_success);
            unset($WPHE_orig_path);
            unset($htaccess_file_editor_backup_path);
            return $htaccess_file_editor_htaccess_content_backup;
        } else {
            htaccess_file_editor_delete_backup();
            unset($htaccess_file_editor_success);
            unset($htaccess_file_editor_htaccess_content_backup);
            unset($WPHE_orig_path);
            unset($htaccess_file_editor_backup_path);
            return true;
        }
    }
}


function htaccess_file_editor_delete_backup()
{
    $htaccess_file_editor_backup_path = ABSPATH . 'wp-content/htaccess.backup';
    @clearstatcache();

    if (file_exists($htaccess_file_editor_backup_path)) {
        if (is_writable($htaccess_file_editor_backup_path)) {
            @unlink($htaccess_file_editor_backup_path);
        } else {
            @chmod($htaccess_file_editor_backup_path, 0666);
            @unlink($htaccess_file_editor_backup_path);
        }

        @clearstatcache();

        if (file_exists($htaccess_file_editor_backup_path)) {
            unset($htaccess_file_editor_backup_path);
            return false;
        } else {
            unset($htaccess_file_editor_backup_path);
            return true;
        }
    } else {
        unset($htaccess_file_editor_backup_path);
        return true;
    }
}


function htaccess_file_editor_write_new_htaccess($WPHE_new_content)
{
    $WPHE_orig_path = ABSPATH . '.htaccess';
    @clearstatcache();

    if (file_exists($WPHE_orig_path)) {
        if (is_writable($WPHE_orig_path)) {
            @unlink($WPHE_orig_path);
        } else {
            @chmod($WPHE_orig_path, 0666);
            @unlink($WPHE_orig_path);
        }
    }
    $WPHE_new_content = trim($WPHE_new_content);
    $WPHE_new_content = str_replace('\\\\', '\\', $WPHE_new_content);
    $WPHE_new_content = str_replace('\"', '"', $WPHE_new_content);
    $WPHE_write_success = @file_put_contents($WPHE_orig_path, $WPHE_new_content, LOCK_EX);
    @clearstatcache();
    if (!file_exists($WPHE_orig_path) && $WPHE_write_success === false) {
        unset($WPHE_orig_path);
        unset($WPHE_new_content);
        unset($WPHE_write_success);
        return false;
    } else {
        unset($WPHE_orig_path);
        unset($WPHE_new_content);
        unset($WPHE_write_success);
        return true;
    }
}


function htaccess_file_editor_debug($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}
