<?php
/*
 * Uninstall plugin
 */

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die( "Hello, Pepiño!" );
}

require_once 'vendor/autoload.php';
use Bubuku\Plugins\MediaLibrary\BML_db;


$plugin_db = new BML_db();
// Remove all meta "_bkml_attachment_file_size" from posts
$plugin_db->remove_all_filesize_meta();