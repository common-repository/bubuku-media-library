<?php
/**
 * Plugin Name: Bubuku Media Library
 * Description: Plugin created by Bubuku for media library management and optimization
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Version:     1.1.1
 * Author:      Bubuku
 * Author URI:  https://www.bubuku.com/
 * Text Domain: bubuku-media-library
 * Domain Path: /languages
 * License:     EUPL v1.2
 * License URI: https://www.eupl.eu/1.2/en/
 *
 * @package     WordPress
 * @author      Bubuku
 * @copyright   2022 Bubuku
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 *
 * Prefix:      bbk
 */

// Detects if the plugin has been entered directly.
defined( 'ABSPATH') || die( 'Hello, Pepiño!' );

define( 'BUBUKU_BML_PLUGIN_BASE', untrailingslashit( plugin_basename( __FILE__ ) ) );

/**
 * Bootstrap the plugin.
 */
require_once 'vendor/autoload.php';

use Bubuku\Plugins\MediaLibrary\BML_plugin;

if ( class_exists( 'Bubuku\Plugins\MediaLibrary\BML_plugin' ) ) {
	$the_plugin = new BML_plugin();
}

register_activation_hook( __FILE__, [ $the_plugin, 'activate' ] );
register_deactivation_hook( __FILE__, [ $the_plugin, 'deactivate' ] );