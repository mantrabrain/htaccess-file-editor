<?php
if ($htaccess_file_editor_restore_result === false) {

    echo '<div id="message" class="error fade"><p><strong>' . __('Unable to restore backup! Probably the wrong setting write permissions to the files.', 'htaccess-file-editor') . '</strong></p></div>';
    echo '<div class="postbox htaccess-file-editor-box">';
    echo '<p>' . __('The backup file is located in the <code>wp-content</code> folder.', 'htaccess-file-editor') . '</p>';
    echo '</div>';
} else {

    echo '<div id="message" class="error fade"><p><strong>' . __('Unable to restore backup!', 'htaccess-file-editor') . '</strong></p></div>';
    echo '<div class="postbox htaccess-file-editor-box" style="background: #FFEECE;">';
    echo '<p class="htaccess-file-editor-red">' . __('This is contents of the original file, put it into a file manually', 'htaccess-file-editor') . ':</p>';
    echo '<textarea id="htaccess-file-editor-textarea" class="htaccess-file-editor-textarea">' . $htaccess_file_editor_restore_result . '</textarea>';
    echo '</div>';
}