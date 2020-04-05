<?php
/*
Plugin Name: Yatterukun
Plugin URI: https://www.andows.jp/yatterukun-plugin
Description: Wait for POST request and automatically replace the image/video file to the new one.
Version: 1.0.0
Author: Katsuya Ando
Author URI: https://www.andows.jp
Text Domain: yatterukun
Domain Path: /languages
License: GPLv2 or later
*/

require 'Yatterukun.php';

$wp_yatterukun = new Yatterukun();

if (function_exists( 'register_uninstall_hook' ))
	register_uninstall_hook( __FILE__, 'uninstall_yatterukun' );

/**
 * Uninstall
 */
function uninstall_yatterukun() {
	
	//delete_option ( self::WP_SETTINGS_KEY );
	
	$wp_upload_url = site_url( '/uploads/yatterukun/', 'https' );
	global $wpdb;
	$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid LIKE '%%%s%%';", 'yatterukun' ));
	$force_delete = true;
	foreach ( $attachment as $id ) {
		wp_delete_attachment( $id, $force_delete );
	}
	$dst_dir = ABSPATH .'wp-content/uploads/yatterukun';
	delete_files($dst_dir);
}
/* 
 * php delete function that deals with directories recursively
 */
function delete_files($target) {
    if(is_dir($target)){
        $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned
        foreach( $files as $file ){
            delete_files( $file );      
        }
        rmdir( $target );
    } elseif(is_file($target)) {
        unlink( $target );  
    }
}


