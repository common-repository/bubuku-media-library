<?php

namespace Bubuku\Plugins\MediaLibrary;

defined( 'ABSPATH') || die( 'Hello, Pepiño!' );

class BML_reports {
    
    private $_emails;
    private $_frequency;
    private $_disabled;

    /**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize plugin
	 */
	public function init() {

        $notification_settings = get_option( 'bbkmedialibrary_notification_settings', array() );
        $this->_emails = isset($notification_settings['emails']) ? $notification_settings['emails'] : '';
        $this->_frequency = isset($notification_settings['frequency']) ? $notification_settings['frequency'] : '';
        $this->_disabled = isset($notification_settings['disabled']) ? $notification_settings['disabled'] : '';

        add_action( 'bbkmedialibrary_report_event', array($this, 'send_email_report') );

        // test email
        // $this->send_email_report();

    }

    public function send_email_report() {

        // Clear hook, just in case
        wp_clear_scheduled_hook('bbkmedialibrary_report_event');
        // Schedule next event
        wp_schedule_single_event( strtotime( $this->_frequency ), 'bbkmedialibrary_report_event' );
        
        $to = $this->_emails;

        $email_subject = sprintf(
            esc_html__( 'Analysis of the sizes and accessibility of images in: %s', 'bubuku-media-library' ),
            get_bloginfo( 'name' )
        );

        $email_content = $this->_get_report_html( $email_subject );
        
        $email_headers = array();
        $email_headers[] = 'From: BBKMediaLibrary <' . get_bloginfo( 'admin_email' ) . '>';
        $email_headers[] = 'Content-Type: text/html; charset=UTF-8';

        // Send email.
        wp_mail( $to, $email_subject, $email_content, $email_headers );
    }

    public function get_img_summary() {
        
        $img_sizes = $this->_calculate_img_sizes();
        $img_alt_empty = $this->_calculate_img_alt_empty();

        return compact('img_sizes', 'img_alt_empty');
    }

    private function _calculate_img_sizes(){
        global $wpdb;

        $count_1 = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key = %s AND CAST(meta_value AS SIGNED) <= %d",
            '_bkml_attachment_file_size',
            100000
        ));

        $count_2 = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key = %s AND CAST(meta_value AS SIGNED) BETWEEN %d AND %d",
            '_bkml_attachment_file_size',
            100001,
            499999
        ));

        $count_3 = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key = %s AND CAST(meta_value AS SIGNED) > %d",
            '_bkml_attachment_file_size',
            500000
        ));        
        
        return array(
            'good' => number_format_i18n($count_1),
            'medium' => number_format_i18n($count_2),
            'bad' => number_format_i18n($count_3),
        );


    }

    private function _calculate_img_alt_empty(){
        global $wpdb;

        $count = $wpdb->get_var("
            SELECT COUNT(*) 
            FROM {$wpdb->posts}
            WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%' AND 
            ID NOT IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attachment_image_alt');
        ");

        return number_format_i18n($count);
    }

    private function _get_report_html( $title ) {

        $site_name = get_bloginfo( 'name' );
        $site_url = get_bloginfo( 'url' );

        $img_sizes = $this->_calculate_img_sizes();
        $img_alt_empty = $this->_calculate_img_alt_empty();
            
        ob_start();
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
			<!--[if !mso]><!-->
			<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
			<!--<![endif]-->
			<meta name="color-scheme" content="light">
			<meta name="supported-color-schemes" content="light">
			<title><?php echo $title; ?></title>
			
		</head>
		<body style="margin: 0;padding: 0;min-width: 100%;-moz-box-sizing: border-box;-webkit-box-sizing: border-box;box-sizing: border-box;background: #f1f1f1;text-align: left;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #444444;font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;font-weight: normal;mso-line-height-rule: exactly;line-height: 140%;font-size: 14px;width: 100% !important;-webkit-font-smoothing: antialiased !important;-moz-osx-font-smoothing: grayscale !important;">
            
        <table border="0" align="center" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;border-spacing: 0;padding: 0;vertical-align: top;text-align: left;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;-moz-box-sizing: border-box;-webkit-box-sizing: border-box;box-sizing: border-box;background: #fefefe;color: #444444;font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;font-weight: normal;mso-line-height-rule: exactly;line-height: 140%;font-size: 16px;width: 100%;max-width:600px;-webkit-font-smoothing: antialiased !important;-moz-osx-font-smoothing: grayscale !important;margin: 0 auto;">

                <tr>
                    <td>
                        <img src="<?php echo esc_url( BUBUKU_BML_PLUGIN_URL .'/assets/img/report_hero.png'); ?>" width="600" alt="Bubuku Media Library - Report" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;width: 600px;max-width: 100%;clear: both;display: inline-block !important;height: auto !important;">
                    </td>
                </tr>

                <tr>
                    <td style="padding: 40px 20px;">
                        <p><strong><?php esc_html_e( 'Hello', 'bubuku-media-library'); ?>,</strong></p>
                        <p>
                            <?php 
                                echo wp_kses(
                                    sprintf(__( 'This is <em>Bubuku Media Library</em> report for the <a href="%s" target="_blank" style="color: #444444;">%s</a>.', 'bubuku-media-library' ), $site_url, $site_name ),
                                    [
                                        'em' => [], // Allow <em> tag for emphasized text
                                        'a' => [ // Allow <a> tag for links
                                            'href' => true,
                                            'target' => true,
                                            'style' => true,
                                            'color' => true,
                                        ],
                                    ]
                                );
                            ?>
                        </p>
                        <p>
                            <?php 
                                echo wp_kses( 
                                    sprintf( 
                                        __( 'The report was generated on <strong>%s</strong> at <strong>%s</strong>.', 'bubuku-media-library' ), date_i18n( get_option( 'date_format' ) ), date_i18n( get_option( 'time_format' ) ) 
                                    ),
                                    [
                                        'strong' => [], // Allow <strong> tag for emphasized text
                                    ]
                                );
                            ?>
                        </p>

                        <p>&nbsp;</p>
                        <h2 style="font-size:18px;">
                            <?php esc_html_e( 'Number of images according to their optimal size', 'bubuku-media-library'); ?>
                        </h2>
                        <table width="100%">
                            <tr>
                                <td width="32%" bgcolor="#76C3C5" style="border-radius:4px;padding:20px;text-align:center;">
                                    <p style="margin-bottom:6px;">
                                        <strong style="font-size:36px;"><?php echo $img_sizes['good'];?></strong>
                                    </p>
                                    <p style="margin-top:0;"><small><?php esc_html_e( 'Good size', 'bubuku-media-library'); ?></small></p>
                                </td>
                                <td width="1%">&nbsp;</td>
                                <td width="32%" bgcolor="#EDCC88" style="border-radius:4px;padding:20px;text-align:center;">
                                    <p style="margin-bottom:6px;">
                                        <strong style="font-size:36px;"><?php echo $img_sizes['medium'];?></strong>
                                    </p>
                                    <p style="margin-top:0;"><small><?php esc_html_e( 'Medium size', 'bubuku-media-library'); ?></small></p>
                                </td>
                                <td width="1%">&nbsp;</td>
                                <td width="32%" bgcolor="#F09878" style="border-radius:4px;padding:20px;text-align:center;">
                                    <p style="margin-bottom:6px;"><strong style="font-size:36px;"><?php echo $img_sizes['bad'];?></strong></p>
                                    <p style="margin-top:0;"><small><?php esc_html_e( 'Bad size', 'bubuku-media-library'); ?></small></p>
                                </td>
                            </tr>
                        </table>
                        
                        <p>&nbsp;</p>
                        <h2 style="font-size:18px;">
                            <?php esc_html_e( 'Number of images without alternative text (ALT)', 'bubuku-media-library'); ?>
                        </h2>
                        <table>
                            <tr>
                                <td width="32%" bgcolor="#ededed" style="border-radius:4px;padding:20px;">
                                    <p>
                                        <?php esc_html_e( 'Images without ALT attribute and not accessible', 'bubuku-media-library'); ?>
                                    </p>
                                </td>
                                <td width="1%">&nbsp;</td>
                                <td width="67%" bgcolor="#ededed" style="border-radius:4px;padding:20px;">
                                    <p>
                                        <strong style="font-size:36px;"><?php echo $img_alt_empty; ?></strong>
                                    </p>
                                </td>
                            </tr>
                        </table>
                        
                        <p>&nbsp;</p>
                        <p style="border-top: 1px solid #cacaca;">
                            <br>
                            <small>
                                <?php 
                                    echo wp_kses( 
                                        sprintf( 
                                            __( 
                                                'You are receiving this email because you have installed the <em>Bubuku Media Library</em> plugin in <em>%s</em>. If you no longer wish to receive these emails, you can go to the plugin settings and disable the report.', 'bubuku-media-library' 
                                            ), $site_name 
                                        ),
                                        [
                                            'em' => [], // Allow <em> tag for emphasized text
                                        ]
                                    );
                                ?>
                            </small>
                        </p>


                    </td>
                </tr>

            </table>
		</body>
		</html>
		<?php
		return ob_get_clean();
    }
}