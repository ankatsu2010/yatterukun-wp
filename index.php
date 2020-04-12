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
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require 'Yatterukun.php';

$wp_yatterukun = new Yatterukun();

if (function_exists( 'register_uninstall_hook' ))
	register_uninstall_hook( __FILE__, 'yatterukun_uninstall' );

/**
 * Uninstall
 */
function yatterukun_uninstall() {
	
	$wp_upload_url = site_url( '/uploads/yatterukun/' );
	global $wpdb;
	$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid LIKE '%%%s%%';", 'yatterukun' ));
	$force_delete = true;
	foreach ( $attachment as $id ) {
		wp_delete_attachment( $id, $force_delete );
	}
	
	if ( ! wp_upload_dir() ['error'] ) {
		$dst_dir = wp_upload_dir() ['basedir'] .'/yatterukun';
		yatterukun_delete_files($dst_dir);
	}
}

/* 
 * 
 */
function yatterukun_delete_files($target) {
    if(is_dir($target)){
        $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned
        foreach( $files as $file ){
            yatterukun_delete_files( $file );      
        }
        rmdir( $target );
    } elseif(is_file($target)) {
        unlink( $target );  
    }
}

/* 
 * 
 */
function delete_yatterukun_page() {
    $yatterukun_slug = Yatterukun::getOption('page_slug');
    if ( $page = get_page_by_path( $yatterukun_slug ) ) {
    	$page_id = $page->ID;
    	wp_delete_post( $page_id, true ); 
    }
}


