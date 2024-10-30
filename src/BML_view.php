<?php

namespace Bubuku\Plugins\MediaLibrary;

defined( 'ABSPATH') || die( 'Hello, Pepiño!' );

class BML_view {

    public function __construct() {
		$this->init();
	}

	private function init() {

    }

    /**
     * show_file_size
     *
     * @param string $file_size
     * @return string
     */
    public function show_file_size($media_id) {
        global $wp_query;

        $file_size = get_post_meta( $media_id, '_bkml_attachment_file_size', true );
        $is_calculate_filesize = true;
        
        if ( empty($file_size) ) {
            $is_calculate_filesize = false;
            $file_size = filesize( get_attached_file( $media_id ) );
        }
        
        if ( !empty( $file_size ) ) {
            if ( 100001 > $file_size) {
                $file_size_style = 'bk_good';
            } else if ( 500001 > $file_size) {
                $file_size_style = 'bk_medium';
            } else {
                $file_size_style = 'bk_high';
            }

            $file_size = size_format($file_size, 2);
            
        }

        if ( $is_calculate_filesize ) {
            return '<p class="bk_file_size_item">
                        <span class="bk_file_size '. esc_html( $file_size_style ) .'">'. esc_html( $file_size ) .'</span>
                        <span class="bk_btn bk_update_size js-bkml-calculate-size" data-id="'. $media_id .'">
                            <span class="bk_txt">
                                <span class="dashicons dashicons-update"></span>
                                '. esc_html__('Update size', 'bubuku-media-library') .'
                            </span>
                            <span class="bk_loader"></span>
                        </span>
                    </p>';
        } else {
            
            $orderby = ( isset($wp_query->query['orderby']) ) ? $wp_query->query['orderby'] : '';
            $file_size_out = '';
            if ( 'bk_file_size' !== $orderby ) {
                $file_size_out = '<span class="bk_file_size '. esc_html( $file_size_style ) .'">'. esc_html( $file_size ) .'</span>'; 
            }

            return '<p class="bk_file_size_item"> 
                        '.  $file_size_out .'
                        <small>'. esc_html__('In order to sort or filter by size, you need to calculate', 'bubuku-media-library') .'</small>  
                        <button type="button" class="bk_btn js-bkml-calculate-size" data-id="'. $media_id .'">
                            <span class="bk_txt button-primary">'. esc_html__('Calculate', 'bubuku-media-library') .'</span>
                            <span class="bk_loader"></span>
                        </button>
                    </p>';
        }

        return esc_html__('error', 'bubuku-media-library');
    }

    /**
     * show_file_alt
     *
     * @param string $media_id
     * @return string
     */
    public function show_file_alt($media_id) {

        $alt_text = get_post_meta($media_id , '_wp_attachment_image_alt', true);
        
        if ( !empty( $alt_text ) ) {
            $alt_style = 'bk_good';
            $icon_style = 'dashicons-yes';
        } else {
            $alt_style = 'bk_high';
            $icon_style = 'dashicons-no-alt';
        }

        return '<div class="bk_info">
                    <p class="bk_alt '. esc_html( $alt_style ) .'">
                        <span class="dashicons '. esc_html( $icon_style ) .'"></span>
                        '. esc_html__('Image alt text', 'bubuku-media-library') .'
                    </p>
                </div>';
    }
}