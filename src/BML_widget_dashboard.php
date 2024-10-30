<?php

namespace Bubuku\Plugins\MediaLibrary;

defined( 'ABSPATH') || die( 'Hello, Pepiño!' );

class BML_widget_dashboard {
    public function __construct() {
        add_action('wp_dashboard_setup', array($this, 'add_dashboard_widget'));
    }

    public function add_dashboard_widget() {
        wp_add_dashboard_widget(
            'bml_widget_dashboard_summary', // Widget slug
            esc_html__( 'Media Library Summary', 'bubuku-media-library' ), // Title
            array($this, 'render_dashboard_widget') // display function
        );
    }

    public function render_dashboard_widget() {

        $bml_reports = new BML_reports();
        $img_summary = $bml_reports->get_img_summary();

        if ( ! empty( $img_summary ) ) {
            $img_sizes = $img_summary['img_sizes'];
            $alt_images = $img_summary['img_alt_empty'];
        } else {
            $img_sizes = array(
                'good' => '-',
                'medium' => '-',
                'bad' => '-'
            );
            $alt_images = '-';
        }

        ob_start();
		?>
        
            <div class="bml-dashboard-widget">
                <h3><?php esc_html_e('Number of images according to their optimal size', 'bubuku-media-library'); ?></h3>
                <div style="display:grid;gap: 14px;grid-template-columns: 1fr 1fr 1fr;align-items: center;">
                    <p style="border-radius:4px;padding:16px 20px;text-align:center;background:#76C3C5;margin-top:0;">
                        <strong style="display:block;font-size:26px;"><?php echo $img_sizes['good'];?></strong>
                        <small><?php esc_html_e( 'Good size', 'bubuku-media-library'); ?></small>
                    </p>
                    <p style="border-radius:4px;padding:16px 20px;text-align:center;background:#EDCC88;margin-top:0;">
                        <strong style="display:block;font-size:26px;"><?php echo $img_sizes['medium'];?></strong>
                        <small><?php esc_html_e( 'Medium size', 'bubuku-media-library'); ?></small>
                    </p>
                    <p style="border-radius:4px;padding:16px 20px;text-align:center;background:#F09878;margin-top:0;">
                        <strong style="display:block;font-size:26px;"><?php echo $img_sizes['bad'];?></strong>
                        <small><?php esc_html_e( 'Bad size', 'bubuku-media-library'); ?></small>
                    </p>
                </div>
                
                <h3 style="margin-top:10px;"><?php esc_html_e('Number of images without alternative text (ALT)', 'bubuku-media-library'); ?></h3>
                <div style="display:grid;gap:14px;grid-template-columns: 1fr 2fr;">
                    <p style="border-radius:4px;padding:20px;background:#ededed;margin-top:0;">
                        <?php esc_html_e( 'Images without ALT attribute and not accessible', 'bubuku-media-library'); ?>
                    </p>
                    <p style="border-radius:4px;padding:20px;background:#ededed;margin-top:0;display: flex;align-items: center;">
                        <strong style="font-size:26px;"><?php echo $alt_images;?></strong>
                    </p>
                </div>
            </div>
        
        <?php
        echo ob_get_clean();

    }
}