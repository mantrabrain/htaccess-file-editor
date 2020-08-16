<?php
/*
Plugin Name: Htaccess File Editor
Plugin URI: https://mantrabrain.com/
Description: Simple editor htaccess file without using FTP client.
Version: 1.0.5
Text Domain: wphe
Domain Path: /lang/
Author: Mantrabrain
Author URI: https://mantrabrain.com
Requires at least: 3.0.0
Tested up to: 5.6
License: GPLv2 or later
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You do not have sufficient permissions to access this file.' );
}

// Define HTACCESS_FILE_EDITOR_FILE.
if (!defined('HTACCESS_FILE_EDITOR_FILE')) {
    define('HTACCESS_FILE_EDITOR_FILE', __FILE__);
}
if ( ! is_admin() ) {
	return;
} else {
    include_once dirname(HTACCESS_FILE_EDITOR_FILE) . '/includes/admin/class-mantrabrain-admin-notices.php';
    include_once dirname(HTACCESS_FILE_EDITOR_FILE) . '/includes/admin/dashboard/class-mantrabrain-admin-dashboard.php';

    $WPHE_version = '1.0.4';

	if ( ! defined( 'WP_CONTENT_URL' ) ) {
		if ( ! defined( 'WP_SITEURL' ) ) {
			define( 'WP_SITEURL', get_option( 'url' ) . '/' );
		}
		define( 'WP_CONTENT_URL', WP_SITEURL . 'wp-content' );
	}
	if ( ! defined( 'WP_PLUGIN_URL' ) ) {
		define( 'WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins' );
	}

	$WPHE_root    = str_replace( '\\', '/', dirname( __FILE__ ) ) . '/';
	$WPHE_lib     = $WPHE_root . 'lib/';
	$WPHE_dirname = str_replace( '\\', '/', dirname( plugin_basename( __FILE__ ) ) );
	$WPHE_dirurl  = WP_PLUGIN_URL . '/' . $WPHE_dirname . '/';


	/***** Načtení překladu ***********************************************/
	$WPHE_Locale = get_locale();
	if ( ! empty( $WPHE_Locale ) ) {
		$WPHE_moFile = dirname( __FILE__ ) . '/lang/' . $WPHE_Locale . '.mo';
		if ( @file_exists( $WPHE_moFile ) && is_readable( $WPHE_moFile ) ) {
			load_textdomain( 'wphe', $WPHE_moFile );
		}
		unset( $WPHE_moFile );
	}
	unset( $WPHE_Locale );


	/***** Načtení souborů pluginu ****************************************/
	if ( file_exists( $WPHE_lib . 'lib.wp-files.php' ) ) {
		require $WPHE_lib . 'lib.wp-files.php';
	} else {
		wp_die( __( 'Fatal error: Plugin <strong>Htaccess File Editor</strong> is corrupted', 'wphe' ) );
	}

	if ( file_exists( $WPHE_lib . 'lib.functions.php' ) ) {
		require $WPHE_lib . 'lib.functions.php';
	} else {
		wp_die( __( 'Fatal error: Plugin <strong>Htaccess File Editor</strong> is corrupted', 'wphe' ) );
	}

	if ( file_exists( $WPHE_lib . 'lib.ad.php' ) ) {
		require $WPHE_lib . 'lib.ad.php';
	} else {
		wp_die( __( 'Fatal error: Plugin <strong>Htaccess File Editor</strong> is corrupted', 'wphe' ) );
	}


	if ( file_exists( $WPHE_lib . 'lib.pages.php' ) ) {
		require $WPHE_lib . 'lib.pages.php';
	} else {
		wp_die( __( 'Fatal error: Plugin <strong>Htaccess File Editor</strong> is corrupted', 'wphe' ) );
	}


	/***** Vytvoření menu v administraci a spuštění pluginu ***************/
	if ( function_exists( 'add_action' ) ) {
		add_action( 'admin_menu', 'WPHE_admin_menu' );
	} else {
		unset( $WPHE_root );
		unset( $WPHE_lib );
		unset( $WPHE_plugin );
		unset( $WPHE_dirname );
		unset( $WPHE_dirurl );

		return;
	}
}
