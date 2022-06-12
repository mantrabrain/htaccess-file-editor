<?php
/**
 * Admin notice to download Everest Backup.
 *
 * @package Everest_Themes_Framework
 */

namespace Everest_Themes_Framework;

/**
 * Exit if accessed directly.
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Everest Backup plugin notice class.
 */
class Htaccess_File_Editor_Ebwp_Notice
{

    const EBWP_SLUG = 'yatra/yatra.php';

    const PACKAGE_URL = 'https://downloads.wordpress.org/plugin/yatra.zip';

    const META_EXPIRE_AFTER = 'htaccess_file_editor_yatra_notice_expire_after';

    const META_ACTION_TYPE = 'htaccess_file_editor_yatra_notice_expire_after';

    const KEY_SUBMIT = 'htaccess_file_editor_yatra_notice';

    protected $user_id = 0;

    protected $expire_after_transient = '';

    protected $install_activate = false;

    /**
     * Init class.
     */
    public function __construct()
    {
        if (defined('EVEREST_THEMES_EBWP_NOTICE_LOADED')) {

            /**
             * Bail if notice already loaded.
             */
            return;
        }

        define('EVEREST_THEMES_EBWP_NOTICE_LOADED', true);

        add_action('admin_init', array($this, 'on_form_submit'));
        add_action('admin_head', array($this, 'notice_styles'));
        add_action('admin_notices', array($this, 'admin_notice'));
    }

    /**
     * Return Everest Backup plugin status.
     *
     * @return string
     */
    protected function get_plugin_status()
    {

        static $plugins = array();

        if (!$plugins) {
            $plugins = get_plugins();
        }

        if (!isset($plugins[self::EBWP_SLUG])) {
            return 'not-installed';
        }

        /**
         * Paused means plugin is installed but not active.
         */
        return is_plugin_active(self::EBWP_SLUG) ? 'active' : 'paused';
    }

    /**
     * Reset notice related metas.
     * Only use this method if you want to reset the meta for testing.
     *
     * @return void
     */
    protected function reset_val()
    {
        $user_id = get_current_user_id();
        delete_user_meta($user_id, self::META_EXPIRE_AFTER);
        delete_user_meta($user_id, self::META_ACTION_TYPE);
    }

    /**
     * Generate an activation URL for a plugin like the ones found in WordPress plugin administration screen.
     *
     * @return string
     */
    protected function get_plugin_activation_link()
    {
        $plugin = self::EBWP_SLUG;

        $url = sprintf(network_admin_url('plugins.php?action=activate&plugin=%s&plugin_status=all&paged=1&s'), $plugin);

        // Change the plugin request to the plugin to pass the nonce check.
        $_REQUEST['plugin'] = $plugin;

        return wp_nonce_url($url, 'activate-plugin_' . $plugin);
    }

    /**
     * Install and activate Everest Backup plugin.
     *
     * @return void
     */
    protected function install_and_activate()
    {

        $plugins_dir = WP_PLUGIN_DIR;

        $plugin = self::EBWP_SLUG;
        $plugin_folder = dirname($plugins_dir . DIRECTORY_SEPARATOR . $plugin);
        $plugin_zip = $plugin_folder . '.zip';

        $package = self::PACKAGE_URL;

        $data = wp_remote_get(
            $package,
            array(
                'sslverify' => false,
            )
        );

        $content = wp_remote_retrieve_body($data);

        if (file_exists($plugin_zip)) {
            unlink($plugin_zip);
        }

        if (!function_exists('WP_Filesystem')) {
            require_once wp_normalize_path(ABSPATH . 'wp-admin/includes/file.php');
        }

        WP_Filesystem();

        global $wp_filesystem;

        $wp_filesystem->put_contents($plugin_zip, $content);

        if (!file_exists($plugin_zip)) {
            return;
        }

        unzip_file($plugin_zip, $plugins_dir);

        unlink($plugin_zip);

        wp_cache_delete('plugins', 'plugins');

        if (!is_wp_error(activate_plugin($plugin, '', is_multisite()))) {
            $this->install_activate = true;
        };

    }

    /**
     * Save user preference after
     *
     * @param array $data User submitted $_POST data.
     * @return void
     */
    protected function save_user_data($data)
    {

        $user_id = get_current_user_id();

        $remind = isset($data['remind']) ? sanitize_text_field(wp_unslash($data['remind'])) : false;

        $action_type = 'never';

        $days = 3;

        if ($remind) {
            $action_type = 'remind';
            $expire_after = time() + (DAY_IN_SECONDS * $days);

            set_transient($this->expire_after_transient, 1, $expire_after);

        }

        update_user_meta($user_id, self::META_ACTION_TYPE, $action_type);
    }

    /**
     * Handle actions on form submit.
     *
     * @return void
     */
    public function on_form_submit()
    {

        $this->user_id = get_current_user_id();

        $this->expire_after_transient = self::META_EXPIRE_AFTER . '_' . $this->user_id;

        if (!isset($_POST[self::KEY_SUBMIT])) {
            return;
        }

        if (!wp_verify_nonce(
            sanitize_text_field(
                wp_unslash($_POST[self::KEY_SUBMIT])
            ),
            self::KEY_SUBMIT
        )) {
            return;
        }

        if (isset($_POST['install'])) {
            $this->install_and_activate();
        } else {
            $this->save_user_data($_POST);
        }

    }

    /**
     * Styles for notice.
     *
     * @return void
     */
    public function notice_styles()
    {
        ?>
        <style>
            #everest-themes-framework-ebwp-notice {
                background: rgb(255, 255, 255);
                background: linear-gradient(90deg, rgb(245 245 245) 0%, rgb(255 255 255) 100%);
                display: flex;
                align-items: start;
                flex-direction: column;
                overflow: hidden;
                border: 3px solid #ffffff;
                margin: 15px 0 !important;
                padding: 0 !important;
                box-shadow: 0 1px 4px rgb(0 0 0 / 15%);
            }

            #everest-themes-framework-ebwp-notice .message.et-notice-message {
                width: 100%;
                padding: 10px;
            }

            #everest-themes-framework-ebwp-notice .message.et-notice-message img {
                width: 53px;
                float: left;
                margin-right: 10px;
            }

            #everest-themes-framework-ebwp-notice .et-notice-message h1 {
                margin: 0;
                padding: 0;
                font-size: 24px;
                font-weight: 400;
            }

            #everest-themes-framework-ebwp-notice .et-notice-message p {
                font-size: 14px;
                margin: 0;
            }

            #everest-themes-framework-ebwp-notice .et-notice-message p strong {
                color: #2271B1;
            }

            .et-notice-actions {
                width: 100%;
                padding: 10px 10px 15px;
                background: linear-gradient(90deg, rgb(234 234 235) 0%, rgb(255 255 255) 100%);
            }

            #everest-themes-framework-ebwp-notice .et-notice-actions button, #everest-themes-framework-ebwp-notice .et-notice-actions a.button-primary {
                margin: 8px 8px 0 0 !important;
            }
        </style>
        <?php
    }

    /**
     * Admin notice to download Everest Backup.
     */
    public function admin_notice()
    {

        if ($this->install_activate) {
            ?>
            <div id="everest-themes-framework-ebwp-notice" class="notice is-dismissible">
                <div class="message et-notice-message">

                    <h1><?php esc_html_e('Thank You !!!', 'htaccess-file-editor'); ?></h1>

                    <?php

                    $plugin_link = '<strong><a href="https://wpyatra.com/?ref=htaccessfileeditor" target="_blank">Yatra</a></strong>';

                    /* translators: %s is the Everest Backup plugin name wrapped with html. */
                    $string = sprintf(__('%s has been installed and activated successfully.', 'htaccess-file-editor'), $plugin_link);

                    echo wp_kses_post(wpautop($string));
                    ?>

                </div>
            </div>
            <?php
        }

        $status = $this->get_plugin_status();

        if ('active' === $status) {

            /**
             * Bail early if plugin is active.
             */
            return;
        }

        /**
         * ===========================
         * If we are here,
         * then Everest Backup plugin
         * is either not active
         * or not installed at all.
         * ===========================
         */

        $user_id = $this->user_id;

        $action_type = get_user_meta($user_id, self::META_ACTION_TYPE, true);

        if ('never' === $action_type) {

            /**
             * Bail if user don't want to keep their data safe and secure :P .
             */
            return;
        }

        /**
         * If user selects remind.
         */
        $remind_again = ('remind' === $action_type) ? get_transient($this->expire_after_transient) : 0;

        if ($remind_again) {
            return;
        }

        /**
         * If we are here, then lets disturb our user :D.
         */
        ?>
        <div id="everest-themes-framework-ebwp-notice" class="notice">
            <div class="message et-notice-message">

                <h1><?php esc_html_e('WordPress Travel/Trip Booking Plugin', 'htaccess-file-editor'); ?></h1>
                <?php

                $plugin_link = '<strong><a href="https://wpyatra.com/?ref=htaccessfileeditor" target="_blank">Yatra</a></strong>';

                if ('not-installed' === $status) {
                    /* translators: %s is the Everest Backup plugin name wrapped with html. */
                    $string = sprintf(__('Create travel/trip booking website using %s plugin', 'htaccess-file-editor'), $plugin_link);
                } else {
                    /* translators: %s is the Everest Backup plugin name wrapped with html. */
                    $string = sprintf(__('Create travel/trip booking website using %s plugin', 'htaccess-file-editor'), $plugin_link);
                }

                echo wp_kses_post(wpautop($string));
                ?>
            </div>

            <div class="et-notice-actions">
                <form method="post">
                    <?php
                    wp_nonce_field(self::KEY_SUBMIT, self::KEY_SUBMIT);

                    if ('not-installed' === $status) {
                        ?>
                        <button name="install" value="1"
                                class="button-primary"><?php esc_html_e('Install & Activate', 'htaccess-file-editor'); ?></button>
                        <button name="remind" value="1"
                                class="button"><?php esc_html_e('Remind me later', 'htaccess-file-editor'); ?></button>
                        <button name="never" value="1"
                                class="button-link"><?php esc_html_e("Don't show this again", 'htaccess-file-editor'); ?></button>
                        <?php
                    } else {
                        $activation_link = $this->get_plugin_activation_link();

                        ?>
                        <a href="<?php echo esc_url($activation_link); ?>"
                           class="button-primary"><?php esc_html_e('Activate Plugin', 'htaccess-file-editor'); ?></a>
                        <?php
                    }
                    ?>
                </form>
            </div>
        </div>
        <?php
    }

}

new Htaccess_File_Editor_Ebwp_Notice();
