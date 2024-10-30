<?php

namespace Bubuku\Plugins\MediaLibrary;

defined( 'ABSPATH') || die( 'Hello, Pepiño!' );

class BML_assets {

    public function __construct() {
		$this->init();
	}

	private function init() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_styles' ] );
	}

    /**
	 * Enqueue Admin Styles
     * info: https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/
	 */
    public function enqueue_admin_styles($hook) {

        if ( 'upload.php' !== $hook) {
            return;
        }
        
        // Media library page
        wp_enqueue_style(
            'bk-media-library-css',
            BUBUKU_BML_PLUGIN_ASSETS_URL . '/css/admin.css',
            false,
            BUBUKU_BML_PLUGIN_VERSION
        );

        $js_dependencies = ['wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor', 'wp-compose']; // ['jquery'];
            
        wp_enqueue_script(
            'bk-media-library-js',
            BUBUKU_BML_PLUGIN_ASSETS_URL . '/js/common.js',
            $js_dependencies,
            true
        );

        $args = array(
            'nonce'         => BUBUKU_BML_PLUGIN_NONCE,
            'api_public'    => '/wp-json/'. BUBUKU_BML_PLUGIN_ENDPOINTS_URL,
        );
        
        wp_localize_script( 'bk-media-library-js', 'bbk_media_library', $args );
        
    }
}