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
        _doing_it_wrong(__FUNCTION__, __('Cloning is forbidden.', 'htaccess-file-editor'), '1.0.0');
    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, __('Unserializing instances of this class is forbidden.', 'htaccess-file-editor'), '1.0.0');
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
        //register_activation_hook(HTACCESS_FILE_EDITOR_FILE, array('Htaccess_File_Editor_Install', 'install'));

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

        if (!is_admin()) {
            return;
        }


        include_once HTACCESS_FILE_EDITOR_ABSPATH . '/includes/admin/dashboard/class-mantrabrain-admin-dashboard.php';
        include_once HTACCESS_FILE_EDITOR_ABSPATH . '/includes/class-htaccess-file-editor-actions.php';
        include_once HTACCESS_FILE_EDITOR_ABSPATH . '/includes/functions.php';
        include_once HTACCESS_FILE_EDITOR_ABSPATH . '/includes/class-htaccess-file-editor-hooks.php';


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
     *      - WP_LANG_DIR/htaccess-file-editor/htaccess-file-editor-LOCALE.mo
     *      - WP_LANG_DIR/plugins/htaccess-file-editor-LOCALE.mo
     */
    public function load_plugin_textdomain()
    {
        $locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
        $locale = apply_filters('plugin_locale', $locale, 'htaccess-file-editor');
        unload_textdomain('htaccess-file-editor');
        load_textdomain('htaccess-file-editor', WP_LANG_DIR . '/htaccess-file-editor/htaccess-file-editor-' . $locale . '.mo');
        load_plugin_textdomain('htaccess-file-editor', false, plugin_basename(dirname(HTACCESS_FILE_EDITOR_FILE)) . '/i18n/languages');
    }


}
