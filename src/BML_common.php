<?php

namespace Bubuku\Plugins\MediaLibrary;

defined( 'ABSPATH') || die( 'Hello, Pepiño!' );

class BML_common {

    private $_db;
    private $_view;

    public function __construct() {
		$this->init();
	}

	private function init() {
        $this->_db = new BML_db();
        $this->_view = new BML_view();

        // creates new columns in media library
        add_filter( 'manage_upload_columns',  [ $this, 'add_column_file_size'] );
         // Display the file size
        add_action( 'manage_media_custom_column',  [ $this, 'value_column_file_size'], 10, 2 );
        // Update image metadata on upload
        add_action( 'add_attachment', [ $this, 'update_image' ] );
        add_action( 'edit_attachment', [ $this, 'update_image' ] );
        add_action( 'save_post', [ $this, 'save_attachment' ] );
        // Adds sortable function for new columns
        add_filter( 'manage_upload_sortable_columns', [ $this, 'media_library_sortable' ] );
        // Sets sorting for new columns
        add_action( 'pre_get_posts', [ $this, 'media_library_sizes_query' ] );
	}

    
    public function add_column_file_size( $columns ) {
        $columns['bk_file_size'] = __('File Size', 'bubuku-media-library');
        return $columns;
    }

    public function value_column_file_size( $column_name, $media_id ) {
        
        if ( 'bk_file_size' != $column_name ) {
          return;
        }

        if ( wp_attachment_is_image( $media_id ) ) {
            
            echo $this->_view->show_file_size( $media_id );
            echo $this->_view->show_file_alt($media_id);

        } else {
            esc_html_e('This media is not supported.', 'bubuku-media-library');
        }
    
    }

    public function update_image( $media_id ) {
        $this->_db->set_file_size($media_id);
    }

    public function save_attachment($media_id) {
        
        if (wp_is_post_revision($media_id))
            return;
    
        $post_type = get_post_type($media_id);
        if ($post_type != 'attachment')
        return;
        
        $this->_db->set_file_size($media_id);
    }
    
    public function media_library_sortable( $columns ) {
        $columns['bk_file_size'] = 'bk_file_size';
        return $columns;
    }
    
    public function media_library_sizes_query( $query ) {
        if ( !is_admin() || !$query->is_main_query() ) {
            return;
        }
    
        $orderby = $query->get( 'orderby' );
    
        if ( 'bk_file_size' == $orderby ) {
            $query->set( 'orderby', 'meta_value_num' );
            $query->set( 'meta_key' , '_bkml_attachment_file_size' );
            $query->set( 'meta_type' , 'numeric' );
        }
    }

}