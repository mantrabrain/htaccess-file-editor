<?php
/**
 * Admin notice to download Everest Backup.
 *
 * @package   Htaccess_File_Editor
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Everest Backup plugin notice class.
 */
class  Htaccess_File_Editor_Ebwp_Notice {

	const EBWP_SLUG = 'everest-backup/everest-backup.php';

	const LOGO_URL = '//ps.w.org/everest-backup/assets/icon-128X128.gif';

	const PACKAGE_URL = 'https://downloads.wordpress.org/plugin/everest-backup.zip';

	const META_EXPIRE_AFTER = 'htaccess_file_editor_ebwp_notice_expire_after';

	const META_ACTION_TYPE = 'htaccess_file_editor_ebwp_notice_expire_after';

	const KEY_SUBMIT = 'htaccess_file_editor_ebwp_notice';

	protected $user_id                = 0;
	protected $expire_after_transient = '';
	protected $install_activate       = false;
	protected $user_consent           = true;

	/**
	 * Init class.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'on_form_submit' ) );
		add_action( 'htaccess_file_editor_after_ebwp_banner', array( $this, 'after_ebwp_banner' ) );

	}

	/**
	 * Return Everest Backup plugin status.
	 *
	 * @return string
	 */
	protected function get_plugin_status() {

		static $plugins = array();

		if ( ! $plugins ) {
			$plugins = get_plugins();
		}

		if ( ! isset( $plugins[ self::EBWP_SLUG ] ) ) {
			return 'not-installed';
		}

		/**
		 * Paused means plugin is installed but not active.
		 */
		return is_plugin_active( self::EBWP_SLUG ) ? 'active' : 'paused';
	}

	/**
	 * Reset notice related metas.
	 * Only use this method if you want to reset the meta for testing.
	 *
	 * @return void
	 */
	protected function reset_val() {
		$user_id = get_current_user_id();
		delete_user_meta( $user_id, self::META_EXPIRE_AFTER );
		delete_user_meta( $user_id, self::META_ACTION_TYPE );
	}

	/**
	 * Generate an activation URL for a plugin like the ones found in WordPress plugin administration screen.
	 *
	 * @return string
	 */
	protected function get_plugin_activation_link() {
		$plugin = self::EBWP_SLUG;

		$url = sprintf( network_admin_url( 'plugins.php?action=activate&plugin=%s&plugin_status=all&paged=1&s' ), $plugin );

		// Change the plugin request to the plugin to pass the nonce check.
		$_REQUEST['plugin'] = $plugin;

		return wp_nonce_url( $url, 'activate-plugin_' . $plugin );
	}

	/**
	 * Install and activate Everest Backup plugin.
	 *
	 * @return void
	 */
	protected function install_and_activate() {

		if ( ! isset( $_POST['user_consent'] ) ) {
			$this->user_consent = false;
			return;
		}

		$plugins_dir = WP_PLUGIN_DIR;

		$plugin        = self::EBWP_SLUG;
		$plugin_folder = dirname( $plugins_dir . DIRECTORY_SEPARATOR . $plugin );
		$plugin_zip    = wp_normalize_path( $plugin_folder . '.zip' );

		$package = self::PACKAGE_URL;

		$data = wp_remote_get(
			$package,
			array(
				'sslverify' => false,
			)
		);

		$content = wp_remote_retrieve_body( $data );

		if ( file_exists( $plugin_zip ) ) {
			unlink( $plugin_zip );
		}

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once wp_normalize_path( ABSPATH . 'wp-admin/includes/file.php' );
		}

		WP_Filesystem();

		global $wp_filesystem;

		$wp_filesystem->put_contents( $plugin_zip, $content );

		if ( ! file_exists( $plugin_zip ) ) {
			return;
		}

		unzip_file( $plugin_zip, $plugins_dir );

		unlink( $plugin_zip );

		wp_cache_flush();

		if ( ! is_wp_error( activate_plugin( $plugin, '', is_multisite() ) ) ) {
			$this->install_activate = true;
		};

		if ( wp_safe_redirect( admin_url( '/admin.php?page=htaccess-file-editor' ) ) ) {
			exit;
		}

	}

	/**
	 * Save user preference after
	 *
	 * @param array $data User submitted $_POST data.
	 * @return void
	 */
	protected function save_user_data( $data ) {

		$user_id = get_current_user_id();

		$remind = isset( $data['remind'] ) ? sanitize_text_field( wp_unslash( $data['remind'] ) ) : false;

		$action_type = 'never';

		$days = 3;

		if ( $remind ) {
			$action_type  = 'remind';
			$expire_after = time() + ( DAY_IN_SECONDS * $days );

			set_transient( $this->expire_after_transient, 1, $expire_after );

		}

		update_user_meta( $user_id, self::META_ACTION_TYPE, $action_type );
	}

	/**
	 * Handle actions on form submit.
	 *
	 * @return void
	 */
	public function on_form_submit() {
		$this->user_id = get_current_user_id();

		$this->expire_after_transient = self::META_EXPIRE_AFTER . '_' . $this->user_id;

		if ( ! isset( $_POST[ self::KEY_SUBMIT ] ) ) {
			return;
		}

		if ( ! wp_verify_nonce(
			sanitize_text_field(
				wp_unslash( $_POST[ self::KEY_SUBMIT ] )
			),
			self::KEY_SUBMIT
		) ) {
			return;
		}

		if ( isset( $_POST['install'] ) ) {
			$this->install_and_activate();
		} else {
			$this->save_user_data( $_POST );
		}

	}


	/**
	 * Admin notice to download Everest Backup.
	 */
	public function after_ebwp_banner() {

		$user_id = $this->user_id;

		$action_type = get_user_meta( $user_id, self::META_ACTION_TYPE, true );
		/**
		 * If user selects remind.
		 */
		$remind_again = ( 'remind' === $action_type ) ? get_transient( $this->expire_after_transient ) : 0;

		if ( $remind_again ) {
			return;
		}

		$status = $this->get_plugin_status();

		if ( 'active' === $status ) {
			return;
		}

		/**
		 * If we are here, then lets disturb our user :D.
		 */
		?>
			<aside class="htaccess-file-editor-sidebar">
				<div class="htaccess-banner">
				<img src="<?php echo esc_url( HTACCESS_FILE_EDITOR_PLUGIN_URI . '/assets/images/everest-backup-banner.png' ); ?>" />

					<form method="post" class="btn-group">
						<?php
							wp_nonce_field( self::KEY_SUBMIT, self::KEY_SUBMIT );
						if ( 'not-installed' === $status ) {
							?>

							<label class="user-consent" for="user-consent">
								<input type="checkbox" name="user_consent" id="user-consent">
								<span><?php esc_html_e( 'By clicking "Install & Activate" button, you agree to install and activate "Everest Backup" plugin in your website.', 'htaccess-file-editor' ); ?></span>
								<?php
								if ( ! $this->user_consent ) {
									?>
									<style>
										.user-consent {
											position: relative;
											padding: 20px 5px 0px;
										}
										label.user-consent:after {
											position: absolute;
											content: '<?php esc_html_e( '*This field is required!', 'htaccess-file-editor' ); ?>';
											background: red;
											top: -8px;
											left: 0;
											padding: 2px 5px;
											color: #ffffff;
										}
										label.user-consent:before {
											position: absolute;
											content: '';
											background: #ff0000;
											width: 7px;
											height: 7px;
											top: 11px;
											left: 8px;
											color: #ffffff;
											z-index: 1;
											transform: rotate(45deg);
										}
									</style>
									<?php
								}
								?>
							</label>

							<button name="install" value="1"  class="button button-install"><?php esc_html_e( 'Install & Activate', 'htaccess-file-editor' ); ?></button>
							<button name="remind" value="1"  class="button"><?php esc_html_e( 'Remind me later', 'htaccess-file-editor' ); ?></button>
							<?php
						} else {

							/**
							 * If we are here then plugin is installed but yet not activated.
							 */

							$activation_link = $this->get_plugin_activation_link();
							?>
							<a href="<?php echo esc_url( $activation_link ); ?>" class="button button-install"><?php esc_html_e( 'Activate Plugin', 'htaccess-file-editor' ); ?></a>
							<?php
						}
						?>
					</form>
				</div>
			</aside>
		<?php
	}

}

new Htaccess_File_Editor_Ebwp_Notice();
