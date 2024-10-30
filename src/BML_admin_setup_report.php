<?php

namespace Bubuku\Plugins\MediaLibrary;

defined( 'ABSPATH') || die( 'Hello, Pepiño!' );

class BML_admin_setup_report {

    public function __construct() {
		$this->init();
	}

	private function init() {
        add_action( 'admin_menu', [ $this, 'create_admin_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'load_scripts_css'] );
    }

    public function create_admin_menu() {
        $capability = 'manage_options';
        $slug = 'bubuku-media-library-options';

        add_submenu_page(
            'options-general.php',
            esc_html__( 'Bubuku Media Libary Setup', 'bubuku-media-library' ),
            esc_html__( 'BBK Media Library', 'bubuku-media-library' ),
            $capability,
            $slug,
            [ $this, 'menu_page_template' ]
        );
    }

    public function menu_page_template() {
        echo '<div class="wrap"><div id="bbk-media-library-app"></div></div>';
    }

    public function load_scripts_css( $hook ) {
        // We only show the stylesheets and scripts on the plugin options page
        if ( 'settings_page_bubuku-media-library-options' !== $hook) {
            return;
        }

        // If we don't have notification settings, we create it
        $this->create_notification_settings();

        // Load the stylesheets and scripts in the plugin options page
        wp_enqueue_style( 
            'bbk-admin-setup-report', 
            BUBUKU_BML_PLUGIN_URL . '/assets/js-build/admin-setup-report.css?r=' . BUBUKU_BML_PLUGIN_VERSION 
        );
        
        wp_enqueue_script( 
            'bbk-admin-setup-report', 
            BUBUKU_BML_PLUGIN_URL . '/assets/js-build/admin-setup-report.js?r=' . BUBUKU_BML_PLUGIN_VERSION, 
            [ 'jquery', 'wp-element' ],
            [ 'jquery', 'wp-element' ], 
            wp_rand(), 
            true
        );

        $const_script = wp_json_encode( 
                                    array( 
                                        'api_url' => home_url( '/wp-json/' . BUBUKU_BML_PLUGIN_ENDPOINTS_URL ),
                                        '_wpnonce' => BUBUKU_BML_PLUGIN_NONCE
                                    )
        );
        wp_add_inline_script( 'bbk-admin-setup-report', 'const BbkMediaLibrary = '. $const_script, 'before' );
    }

    public function create_notification_settings() {
        
        // Setting the notification email option for Weekly emails.
		if ( ! get_option( 'bbkmedialibrary_notification_settings' ) ) {
			
			$args = array (
				'emails' 	=> array( get_bloginfo( 'admin_email' ) ),
				'frequency' => '',
				'disabled' 	=> 0,
			);
			add_option( 'bbkmedialibrary_notification_settings', $args, '', 'no' );
		}

    }
}