<?php

namespace Bubuku\Plugins\MediaLibrary;

defined( 'ABSPATH') || die( 'Hello, Pepiño!' );

class BML_bulk_action {
    
    private $_db;

    public function __construct() {
		$this->init();
	}

	private function init() {
        
        $this->_db = new BML_db();

        add_filter( 'bulk_actions-upload', [ $this, 'add_action_dropdown' ] );
        add_action('handle_bulk_actions-upload', [ $this, 'handle_bulk_calculate_file_size' ], 10, 3 );
        add_action('admin_notices', [ $this, 'admin_notices_bulk' ] );
    }

    public function add_action_dropdown($bulk_actions){
        $bulk_actions['calculate-file-size'] = esc_html__('Calculate the size of the files', 'bubuku-media-library');
        return $bulk_actions;
    }
    
    function handle_bulk_calculate_file_size( $redirect_to, $action, $media_ids ) {
        
        if ( $action === 'calculate-file-size' ) {
            
            foreach ($media_ids as $media_id) {
                $this->_db->set_file_size($media_id);
            }

            $redirect_url = add_query_arg('changed-to-published', count($media_ids), $redirect_to);
        }

        return $redirect_url;
    }

    public function admin_notices_bulk() {
        if (!empty($_REQUEST['changed-to-published'])) {
            $num_changed = $_REQUEST['changed-to-published'];
            printf( '<div id="message" class="updated notice is-dismissable"><p>' . esc_html__( '%d media files calculated.', 'bubuku-media-library' ) . '</p></div>', $num_changed );
        }
    }
}