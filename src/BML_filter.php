<?php

namespace Bubuku\Plugins\MediaLibrary;

defined( 'ABSPATH') || die( 'Hello, Pepiño!' );

class BML_filter {

    public function __construct() {
		$this->init();
	}

	private function init() {
        add_action('restrict_manage_posts', [ $this, 'media_dropdown' ] );
        // Filter query
		add_action( 'pre_get_posts', [ $this, 'filter_results' ] );
        add_action( 'load-upload.php', [ $this, 'notice' ] );
    }

    /**
     * media_dropdown
     *
     * @return void
     */
    public function media_dropdown(){

        $screen = '';
		if ( is_admin() && function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
		}
		
		if ('upload' === $screen->base ) {
			
            /**
             * ALT
             */
			$selected = '';
			if ( isset( $_GET['bk_filter_alt'] ) ) {
                // $filter_alt = filter_input(INPUT_GET, 'bk_filter_alt', FILTER_SANITIZE_STRING );
				$selected = sanitize_text_field( $_GET['bk_filter_alt'] ); 
			}
	
			echo '<select name="bk_filter_alt"><option value="All">'. esc_html__('All alt text', 'bubuku-media-library' ) .'</option>';
			$aItems = array(
                        array(
                            'id' => '1',
                            'label' => esc_html__('Empty alt text', 'bubuku-media-library' )
                        ), 
                        array(
                            'id' => '2',
                            'label' => esc_html__('full alt text', 'bubuku-media-library' )
                        )
            );
				
			foreach ( $aItems as $item ) {
                $is_selected = ($selected === $item['id']) ? 'selected' : '';
				echo '<option value="'. esc_html($item['id']) .'" '. esc_html($is_selected) .' >'. esc_html($item['label']) .'</option>';
			}
			
            echo '</select>';

            /**
             * FILE SIZE
             */
			$selected = '';
			if ( isset( $_GET['bk_filter_file_size'] ) ) {
				$selected = sanitize_text_field( $_GET['bk_filter_file_size'] );
			}
	
			echo '<select name="bk_filter_file_size"><option value="All">'. esc_html__('All file size', 'bubuku-media-library' ) .'</option>';
			$aItems = array(
                        array(
                            'id' => '1',
                            'label' => esc_html__('Good file size', 'bubuku-media-library' )
                        ), 
                        array(
                            'id' => '2',
                            'label' => esc_html__('Medium file size', 'bubuku-media-library' )
                        ), 
                        array(
                            'id' => '3',
                            'label' => esc_html__('High file size', 'bubuku-media-library' )
                        )
            );
				
			foreach ( $aItems as $item ) {
                $is_selected = ($selected === $item['id']) ? 'selected' : '';
				echo '<option value="'. esc_html( $item['id'] ) .'" '. esc_html( $is_selected ) .' >'. esc_html( $item['label'] ) .'</option>';
			}
			
            echo '</select>';
		}

		return false;
    }

    /**
	 * Add new parameter to search query
	 * 
	 * @param [type] $query
	 * @return void
	 */
	public function filter_results($query){

        global $pagenow;
        
		 
		if ( is_admin() && 'upload.php' === $pagenow ) {

            $meta_query = array();

            /**
             * ALT
             */
			if ( isset( $_GET['bk_filter_alt'] ) && 'All' !== $_GET['bk_filter_alt']  ) {
				$bk_filter_alt = sanitize_text_field( $_GET['bk_filter_alt'] );

                switch ($bk_filter_alt) {
                    case 1:
                        $compare = 'NOT EXISTS';
                        break;
                    case 2:
                        $compare = '!=';
                        break;
                    default:
                        $compare = '';
                        break;
                }

                array_push( 
                    $meta_query, 
                    array(
                        'key' => '_wp_attachment_image_alt',
                        'value' => '',
                        'compare' => $compare,
                    )
                );
			}

            /**
             * FILE SIZE
             */
			if ( isset( $_GET['bk_filter_file_size'] ) && 'All' !== $_GET['bk_filter_file_size']  ) {
				$bk_filter_file_size = sanitize_text_field( $_GET['bk_filter_file_size'] );

                switch ($bk_filter_file_size) {
                    case 1:
                        // good -> <= 100k
                        $compare = array(
                            'key' => '_bkml_attachment_file_size',
                            'value' => 100000,
                            'type'     => 'numeric',
                            'compare' => '<=',
                        );
                        break;
                    case 2:
                        // medium -> 100.001k - 499.999K
                        $compare = array(
                            'key' => '_bkml_attachment_file_size',
                            'value' => array(100001, 499999),
                            'type'     => 'numeric',
                            'compare' => 'between',
                        );
                        break;
                    case 3:
                        // High -> >= 500
                        $compare = array(
                            'key' => '_bkml_attachment_file_size',
                            'value' => 500000,
                            'type'     => 'numeric',
                            'compare' => '>=',
                        );
                        break;
                    default:
                        $compare = array();
                        break;
                }
	
				array_push( 
                    $meta_query, 
                    $compare
                );
			}

            $query->set('meta_query', $meta_query);
		}
	}

    public function notice() {
        global $wp_query;
        //$count = $custom_posts->found_posts;
        // media_list_table_query
        // WP_Media_List_Table::no_items()
        // has_items
        // total_items
        // get_pagenum
        // $wp_list_table->_pagination_args;
        // $wp_list_table->_pagination_args['total_items'];

        if(!class_exists('WP_List_Table')){
            require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
        }        

        // echo '-+-';
        // var_dump($wp_query);

        $wp_list_table = _get_list_table('WP_Media_List_Table');
        //$data = $wp_list_table->display();
        //usort( $data, array( &$this, 'sort_data' ) );
        //$totalItems = count($data);
        // echo '-+-'. $data;
        //var_dump($data);

        //$WP_Media_List_Table = new WP_Media_List_Table();
        //echo '-*-'. $WP_Media_List_Table->has_items();

        //exit();
    }
}