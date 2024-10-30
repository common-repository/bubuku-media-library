<?php

namespace Bubuku\Plugins\MediaLibrary;

defined( 'ABSPATH') || die( 'Hello, Pepiño!' );

class BML_restapi {
    
    private $_namespace;
    private $_db;
    private $_view;

    public function __construct() {
		$this->init();
	}

	private function init() {
		$this->_namespace = BUBUKU_BML_PLUGIN_ENDPOINTS_URL;
        $this->_db = new BML_db();
        $this->_view = new BML_view();
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

    /**
    * register_routes
    * 
    * We define the routes that our REST API will have.
    *
    * @return void
    */
    public function register_routes() {
        register_rest_route( $this->_namespace, 'calculate-file-size', array(
            'methods'  => 'POST',
            'callback' => array ($this, 'calculate_file_size'),
            'args'     => array(
                'media_id' => array(
                    'required' => true,
                    'validate_callback' => function($param, $request, $key) {
                        return is_numeric( $param );
                    }
                ),
            ),
            'permission_callback' => '__return_true'
        ));

        register_rest_route( $this->_namespace, 'get-notification-settings', array(
            'methods'  => 'POST',
            'callback' => array ($this, 'get_notification_settings'),
            'args'     => array(
                '_wpnonce' => array(
                    'required' => true,
                    'validate_callback' => function($param, $request, $key) {
                        return is_string( $param );
                    }
                )
            ),
            'permission_callback' => '__return_true'
        ));

        register_rest_route( $this->_namespace, 'set-notification-settings', array(
            'methods'  => 'POST',
            'callback' => array ($this, 'set_notification_settings'),
            'args'     => array(
                'emails' => array(
                    'required' => true,
                    'validate_callback' => function($param, $request, $key) {
                        return is_string( $param );
                    }
                ),
                '_wpnonce' => array(
                    'required' => true,
                    'validate_callback' => function($param, $request, $key) {
                        return is_string( $param );
                    }
                )
            ),
            'permission_callback' => '__return_true'
        ));

        register_rest_route( $this->_namespace, 'set-reports-disable', array(
            'methods'  => 'POST',
            'callback' => array ($this, 'set_reports_disable'),
            'args'     => array(
                'disable_reports' => array(
                    'required' => true,
                    'validate_callback' => function($param, $request, $key) {
                        return is_numeric($param) && ($param === 0 || $param === 1 || $param === 2);
                    }
                ),
                '_wpnonce' => array(
                    'required' => true,
                    'validate_callback' => function($param, $request, $key) {
                        return is_string( $param );
                    }
                )
            ),
            'permission_callback' => '__return_true'
        ));
    }

    /**
    * calculate_file_size
    * 
    * @param WP_REST_Request $request Full data about the request.
    *
    * @return void 
    */
    public function calculate_file_size($request) {
        $media_id = $request['media_id'];
        $nonce = $request['_wpnonce'];

        if ( BUBUKU_BML_PLUGIN_NONCE !== $nonce && empty($media_id)) {
            wp_send_json_error( esc_html__('empty Media ID', 'bubuku-media-library') );
            die();
        }

        $data = $this->_db->set_file_size($media_id);

        if ( $data ) {
            $filesize = $this->_view->show_file_size($media_id);
            wp_send_json_success(array('filesize' => $filesize));
            die();
        }
            
        return $data;
    }

    /**
     * get_notification_settings
     * 
     * @param WP_REST_Request $request Full data about the request.
     * 
     * @return void
     */
    public function get_notification_settings($request) {
        $_wpnonce = $request['_wpnonce'];

        if ( BUBUKU_BML_PLUGIN_NONCE !== $_wpnonce ) {
            wp_send_json_error( esc_html__('Hello, Pepiño!', 'bubuku-media-library') );
            die();
        }

        // Get the list of weekly email recipients.
		$notification_settings = get_option( 'bbkmedialibrary_notification_settings', array() );
        if ( empty( $notification_settings) ) {
            wp_send_json_error( esc_html__('empty emails', 'bubuku-media-library') );
            die();

        } else {
            $emails = implode( ', ', $notification_settings['emails'] );
            $frequency = $notification_settings['frequency'];
            $disabled = $notification_settings['disabled'];
            wp_send_json_success( compact( 'emails', 'frequency', 'disabled' ) );
            die();
        }

        wp_send_json_success( array('emails' => $notification_settings ) );

        return false;

    }

    /**
     * set_notification_settings
     * 
     * @param WP_REST_Request $request Full data about the request.
     * 
     * @return void
     */
    public function set_notification_settings($request) {
        $emails = $request['emails'];
        $_wpnonce = $request['_wpnonce'];

        if ( BUBUKU_BML_PLUGIN_NONCE !== $nonce && empty($emails)) {
            wp_send_json_error( esc_html__('empty emails', 'bubuku-media-library') );
            die();
        }

        // Update the option.
        $notification_settings = get_option( 'bbkmedialibrary_notification_settings', array() );
        $notification_settings['emails'] = explode( ',', $emails );
        update_option( 'bbkmedialibrary_notification_settings', $notification_settings );

        wp_send_json_success( array('emails' => $emails ) );
        die();

        return false;


    }

    /**
     * set_reports_disable
     * 
     * @param WP_REST_Request $request Full data about the request.
     * 
     * @return void
     */
    public function set_reports_disable($request) {
        $_wpnonce = $request['_wpnonce'];
        $disable_reports = $request['disable_reports'];

        if ( BUBUKU_BML_PLUGIN_NONCE !== $_wpnonce ) {
            wp_send_json_error( esc_html__('Hello, Pepiño!', 'bubuku-media-library') );
            die();
        }


        // Get the option.
        $notification_settings = get_option( 'bbkmedialibrary_notification_settings', array() );

        // Unschedule and remove scheduled wp-cron jobs.
        $frequency = '';
        wp_unschedule_event( wp_next_scheduled( 'bbkmedialibrary_report_event' ), 'bbkmedialibrary_report_event' );
        wp_clear_scheduled_hook( 'bbkmedialibrary_report_event' );
        
        if ( 1 === $disable_reports ) {
            // Schedule the event every Monday.
            $frequency = 'next monday';
            wp_schedule_single_event( strtotime( $frequency ), 'bbkmedialibrary_report_event' );

        } else if ( 2 === $disable_reports ) {
            // Schedule the event every Monthly.
            $frequency = 'first day of next month';
            wp_schedule_single_event( strtotime( $frequency ), 'bbkmedialibrary_report_event' );
        }

        // Update the option.
        $notification_settings['frequency'] = $frequency;
        $notification_settings['disabled'] = $disable_reports;
        update_option( 'bbkmedialibrary_notification_settings', $notification_settings );

        wp_send_json_success( array('disabled' => $disable_reports ) );
        die();

        return $false;

    }
}