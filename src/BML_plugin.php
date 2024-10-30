<?php

namespace Bubuku\Plugins\MediaLibrary;

defined( 'ABSPATH') || die( 'Hello, Pepiño!' );

class BML_plugin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	/**
	 * Initialize plugin
	 */
	public function init() {
		define( 'BUBUKU_BML_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __DIR__ ) ) );
		define( 'BUBUKU_BML_PLUGIN_URL', untrailingslashit( plugin_dir_url( __DIR__ ) ) );
		define( 'BUBUKU_BML_PLUGIN_ASSETS_PATH', BUBUKU_BML_PLUGIN_PATH . '/assets' );
		define( 'BUBUKU_BML_PLUGIN_ASSETS_URL', BUBUKU_BML_PLUGIN_URL . '/assets' );
		define( 'BUBUKU_BML_PLUGIN_ENDPOINTS_URL', 'bbk_medialibrary/v1' );
		define( 'BUBUKU_BML_PLUGIN_VERSION', '1.1.1' );
		define( 'BUBUKU_BML_PLUGIN_NONCE', wp_create_nonce('media-library/v1') );

		load_plugin_textdomain( 'bubuku-media-library', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

        new BML_assets();
		new BML_common();
		new BML_restapi();
		new BML_filter();
		new BML_bulk_action();
		new BML_reports();
		new BML_admin_setup_report();
		new BML_widget_dashboard();

		add_filter( 'plugin_action_links_' . BUBUKU_BML_PLUGIN_BASE ,  [ $this, 'add_settings_link' ] );

	}

    public function activate() {
		$admin_setup_report = new BML_admin_setup_report();
		$admin_setup_report->create_notification_settings();
    }

    public function deactivate() {

		// Remove Notification Settings
		delete_option( 'bbkmedialibrary_notification_settings' );

		// Unschedule and remove scheduled wp-cron jobs.
		wp_unschedule_event( wp_next_scheduled( 'bbkmedialibrary_report_event' ), 'bbkmedialibrary_report_event' );
		wp_clear_scheduled_hook( 'bbkmedialibrary_report_event' );
    }

	/**
	 * Add settings link to plugin page
	 * 
	 * @param array $links
	 * @return array
	 */
	
	public function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=bubuku-media-library-options">' . __( 'Settings', 'bubuku-media-library' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}

}
