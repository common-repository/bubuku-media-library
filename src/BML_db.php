<?php

namespace Bubuku\Plugins\MediaLibrary;

defined( 'ABSPATH') || die( 'Hello, Pepiño!' );

class BML_db {

    public function __construct() {
		$this->init();
	}

	private function init() {

    }

    /**
     * set_file_size
     *
     * @param string $media_id
     * @return string
     */
    public function set_file_size($media_id){
        // checks if the uploaded file is an image
        if ( wp_attachment_is_image( $media_id ) ) {
            $filesize = filesize( get_attached_file( $media_id ) );
            // creates new meta field with file size of an image
            update_post_meta( $media_id, '_bkml_attachment_file_size', $filesize );

            return $filesize;
        }

        return false;
    }

    /**
     * Remove all meta "_bkml_attachment_file_size" from Media
     *
     * @return void
     */
    public function remove_all_filesize_meta() {
        global $wpdb;
        $wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key = '_bkml_attachment_file_size'" );
        return true;
    }
}