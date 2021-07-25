<?php
/**
 * Htaccess_File_Editor setup
 *
 * @package Htaccess_File_Editor
 * @since   1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Main Htaccess_File_Editor Class.
 *
 * @class Htaccess_File_Editor
 */
final class Htaccess_File_Editor
{

    /**
     * Htaccess_File_Editor version.
     *
     * @var string
     */
    public $version = HTACCESS_FILE_EDITOR_VERSION;

    /**
     * The single instance of the class.
     *
     * @var Htaccess_File_Editor
     * @since 1.0.0
     */
    protected static $_instance = null;


    /**
     * Main Htaccess_File_Editor Instance.
     *
     * Ensures only one instance of Htaccess_File_Editor is loaded or can be loaded.
     *
     * @return Htaccess_File_Editor - Main instance.
     * @since 1.0.0
     * @static
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, __('Cloning is forbidden.', 'yatra'), '1.0.0');
    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, __('Unserializing instances of this class is forbidden.', 'yatra'), '1.0.0');
    }

    /**
     * Auto-load in-accessible properties on demand.
     *
     * @param mixed $key Key name.
     * @return mixed
     */
    public function __get($key)
    {
        if (in_array($key, array(''), true)) {
            return $this->$key();
        }
    }

    /**
     * Htaccess_File_Editor Constructor.
     */
    public function __construct()
    {

        $this->define_constants();
        $this->includes();
        $this->init_hooks();


        do_action('htaccess_file_editor_loaded');
    }

    /**
     * Hook into actions and filters.
     *
     * @since 1.0.0
     */
    private function init_hooks()
    {
        add_action('init', array($this, 'init'), 0);


    }

    /**
     * Define Htaccess_File_Editor Constants.
     */
    private function define_constants()
    {

        $this->define('HTACCESS_FILE_EDITOR_ABSPATH', dirname(HTACCESS_FILE_EDITOR_FILE) . '/');
        $this->define('HTACCESS_FILE_EDITOR_BASENAME', plugin_basename(HTACCESS_FILE_EDITOR_FILE));
    }

    /**
     * Define constant if not already set.
     *
     * @param string $name Constant name.
     * @param string|bool $value Constant value.
     */
    private function define($name, $value)
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    /**
     * What type of request is this?
     *
     * @param string $type admin, ajax, cron or frontend.
     * @return bool
     */
    private function is_request($type)
    {
        switch ($type) {
            case 'admin':
                return is_admin();
            case 'ajax':
                return defined('DOING_AJAX');
            case 'cron':
                return defined('DOING_CRON');
            case 'frontend':
                return (!is_admin() || defined('DOING_AJAX')) && !defined('DOING_CRON') && !defined('REST_REQUEST');
        }
    }

    /**
     * Include required core files used in admin and on the frontend.
     */
    public function includes()
    {

        if (!$this->is_request('admin')) {
            return;
        }
        include_once dirname(HTACCESS_FILE_EDITOR_FILE) . '/includes/admin/class-mantrabrain-admin-notices.php';
        include_once dirname(HTACCESS_FILE_EDITOR_FILE) . '/includes/admin/dashboard/class-mantrabrain-admin-dashboard.php';

        $this->init_this();

    }

    public function init_this()
    {
        if (!defined('WP_CONTENT_URL')) {
            if (!defined('WP_SITEURL')) {
                define('WP_SITEURL', get_option('url') . '/');
            }
            define('WP_CONTENT_URL', WP_SITEURL . 'wp-content');
        }
        if (!defined('WP_PLUGIN_URL')) {
            define('WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins');
        }

        $WPHE_root = str_replace('\\', '/', dirname(__FILE__)) . '/';
        $WPHE_lib = $WPHE_root . 'lib/';
        $WPHE_dirname = str_replace('\\', '/', dirname(plugin_basename(__FILE__)));


        /***** Načtení překladu ***********************************************/
        $WPHE_Locale = get_locale();
        if (!empty($WPHE_Locale)) {
            $WPHE_moFile = dirname(__FILE__) . '/languages/' . $WPHE_Locale . '.mo';
            if (@file_exists($WPHE_moFile) && is_readable($WPHE_moFile)) {
                load_textdomain('wphe', $WPHE_moFile);
            }
            unset($WPHE_moFile);
        }
        unset($WPHE_Locale);


        /***** Načtení souborů pluginu ****************************************/
        if (file_exists($WPHE_lib . 'lib.wp-files.php')) {
            require $WPHE_lib . 'lib.wp-files.php';
        } else {
            wp_die(__('Fatal error: Plugin <strong>Htaccess File Editor</strong> is corrupted', 'wphe'));
        }

        if (file_exists($WPHE_lib . 'lib.functions.php')) {
            require $WPHE_lib . 'lib.functions.php';
        } else {
            wp_die(__('Fatal error: Plugin <strong>Htaccess File Editor</strong> is corrupted', 'wphe'));
        }

        if (file_exists($WPHE_lib . 'lib.ad.php')) {
            require $WPHE_lib . 'lib.ad.php';
        } else {
            wp_die(__('Fatal error: Plugin <strong>Htaccess File Editor</strong> is corrupted', 'wphe'));
        }


        if (file_exists($WPHE_lib . 'lib.pages.php')) {
            require $WPHE_lib . 'lib.pages.php';
        } else {
            wp_die(__('Fatal error: Plugin <strong>Htaccess File Editor</strong> is corrupted', 'wphe'));
        }


        /***** Vytvoření menu v administraci a spuštění pluginu ***************/
        if (function_exists('add_action')) {
            add_action('admin_menu', 'WPHE_admin_menu');
        } else {
            unset($WPHE_root);
            unset($WPHE_lib);
            unset($WPHE_plugin);
            unset($WPHE_dirname);
            unset(HTACCESS_FILE_EDITOR_PLUGIN_URI);

            return;
        }
    }

    /**
     * Init Htaccess_File_Editor when WordPress Initialises.
     */
    public function init()
    {
        // Before init action.
        do_action('before_htaccess_file_editor_init');

        // Set up localisation.
        $this->load_plugin_textdomain();

        // Init action.
        do_action('htaccess_file_editor_init');
    }

    /**
     * Load Localisation files.
     *
     * Note: the first-loaded translation file overrides any following ones if the same translation is present.
     *
     * Locales found in:
     *      - WP_LANG_DIR/yatra/yatra-LOCALE.mo
     *      - WP_LANG_DIR/plugins/yatra-LOCALE.mo
     */
    public function load_plugin_textdomain()
    {
        $locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
        $locale = apply_filters('plugin_locale', $locale, 'yatra');
        unload_textdomain('htaccess-file-editor');
        load_textdomain('htaccess-file-editor', WP_LANG_DIR . '/htaccess-file-editor/htaccess-file-editor-' . $locale . '.mo');
        load_plugin_textdomain('htaccess-file-editor', false, plugin_basename(dirname(HTACCESS_FILE_EDITOR_FILE)) . '/i18n/languages');
    }

    /**
     * Ensure theme and server variable compatibility and setup image sizes.
     */
    public function setup_environment()
    {

        $this->define('HTACCESS_FILE_EDITOR_TEMPLATE_PATH', $this->template_path());

    }

    /**
     * Get the plugin url.
     *
     * @return string
     */
    public function plugin_url()
    {
        return untrailingslashit(plugins_url('/', HTACCESS_FILE_EDITOR_FILE));
    }

    /**
     * Get the plugin path.
     *
     * @return string
     */
    public function plugin_path()
    {
        return untrailingslashit(plugin_dir_path(HTACCESS_FILE_EDITOR_FILE));
    }

    /**
     * Get the template path.
     *
     * @return string
     */
    public function template_path()
    {
        return apply_filters('htaccess_file_editor_template_path', 'htaccess-file-editor/');
    }

    /**
     * Get the template path.
     *
     * @return string
     */
    public function plugin_template_path()
    {
        return apply_filters('htaccess_file_editor_plugin_template_path', $this->plugin_path() . '/templates/');
    }

    /**
     * Get Ajax URL.
     *
     * @return string
     */
    public function ajax_url()
    {
        return admin_url('admin-ajax.php', 'relative');
    }


}
