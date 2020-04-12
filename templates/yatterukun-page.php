<?php
/*
Template Name: Yatterukun
*/

if ( ! defined( 'ABSPATH' ) ) exit;

$page_slug = Yatterukun::getOption( 'page_slug' );
$user_name = Yatterukun::getOption( 'user_name' );
$upload_key = Yatterukun::getOption( 'upload_key' );
$data_name = Yatterukun::getOption( 'data_name' );
$max_size = Yatterukun::getOption( 'max_size' );
$file_types = Yatterukun::getOption( 'file_types' );

$_yatterukun_width = 2000;
$_yatterukun_height = 1200;

if ( is_page( $page_slug ) ) {
	if ( ! $_SERVER['REQUEST_METHOD'] == 'POST') {
		global $wp_query;
    	$wp_query->set_404();
		status_header( 404 );
		http_response_code(404);
    	nocache_headers();
    	require get_404_template();
    	exit;
	}
	else if ( isset($_POST['username']) && $user_name == $_POST['username'] 
		&& isset($_POST['uploadkey']) && $upload_key == $_POST['uploadkey'] ) {
		
		echo 'Welcome ' . $user_name . '!' . PHP_EOL;
		
		if ( ! empty( $_FILES[ $data_name ]['name'] ) && $_FILES[ $data_name ][ 'size' ] > 0 ) {
		
			if ( $_FILES[ $data_name ][ 'size' ] <= intval( $max_size ) * 1024 * 1024 ) {
				
				$allowed_file_types = array('image/jpg','image/jpeg','image/gif','image/png','video/mp4','video/quicktime','video/ogg','video/x-msvideo');
				$arr_file_type = wp_check_filetype(basename($_FILES[ $data_name ]['name']));
        		$uploaded_file_ext = strtolower( $arr_file_type['ext'] );
        		$uploaded_file_type = $arr_file_type['type'];
        		
				if ( is_array( $file_types ) && in_array($uploaded_file_ext, $file_types)
						&& in_array($uploaded_file_type, $allowed_file_types) ){
					
					if ( 'jpg' == $uploaded_file_ext ) {
						
						if ( $error = wp_upload_dir() ['error'] ) {
							
							echo 'Error:' . $error . PHP_EOL;
						}
						else{
							
							$wp_upload_url = wp_upload_dir() ['baseurl'] . '/yatterukun/';
							$image_url = $wp_upload_url . 'yatterukun.jpg';
							global $wpdb;
							$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ));
							if ( $attachment ) {
								$attach_id_old = $attachment[0];
							}
							else{
								$attach_id_old = 0;
							}
							echo 'image url is ' . $image_url . PHP_EOL;
							echo '$attachment is' . PHP_EOL;
							echo implode("|",$attachment) . PHP_EOL;
							echo 'old attachment id is ' . $attach_id_old . PHP_EOL;
							
							foreach ( glob ( wp_upload_dir() ['basedir'] .'/yatterukun/*.jpg' ) as $file ) {
								yatterukun_delete_files ( $file );
							}
							
							$dst_file_origin = wp_upload_dir() ['basedir'] .'/yatterukun/yatterukun_origin.jpg';
							$dst_file_new = wp_upload_dir() ['basedir'] .'/yatterukun/yatterukun.jpg';
							move_uploaded_file( $_FILES[ $data_name ]['tmp_name'], $dst_file_origin );
							list($width, $height) = getimagesize($dst_file_origin);
							
							if ( $width == $_yatterukun_width && $height == $_yatterukun_height ){
								
								rename( $dst_file_origin, $dst_file_new);
							}
							else {
								
								//resize
								$asp_origin = $width / $height;
								$asp_yatterukun = $_yatterukun_width / $_yatterukun_height;
								
								if ($asp_origin >= $asp_yatterukun){
								
									$scale = $_yatterukun_height / $height;
									$temp_w = floor( $width * $scale );
									$dst_x = 0;
									$dst_y = 0;
									$src_x = floor(($temp_w - $_yatterukun_width) / 2 / $scale);
									$src_y = 0;
									$src_w = floor($_yatterukun_width / $scale);
									$src_h = $height;
									
								}
								else {
									
									$scale = $_yatterukun_width / $width;
									$temp_h = floor( $height * $scale );
									$dst_x = 0;
									$dst_y = 0;
									$src_x = 0;
									$src_y = floor(($temp_h - $_yatterukun_height) / 2 / $scale);
									$src_w = $width;
									$src_h = floor($_yatterukun_height / $scale);
								}
								
								$image1 = imagecreatefromjpeg( $dst_file_origin );
								$image2 = ImageCreateTrueColor($_yatterukun_width, $_yatterukun_height);
								imagecopyresampled($image2, $image1, $dst_x, $dst_y, $src_x, $src_y, $_yatterukun_width, $_yatterukun_height, $src_w, $src_h);
								imagejpeg($image2, $dst_file_new, 100);
								
								echo 'src width = ' . $width . PHP_EOL;
								echo 'src height = ' . $height . PHP_EOL;
								echo 'dst_x = ' . $dst_x . PHP_EOL;
								echo 'dst_y = ' . $dst_y . PHP_EOL;
								echo 'src_x = ' . $src_x . PHP_EOL;
								echo 'src_y = ' . $src_y . PHP_EOL;
								echo 'src_w = ' . $src_w . PHP_EOL;
								echo 'src_h = ' . $src_h . PHP_EOL;
								
							}
							
				 			$attach = array(
								'guid'           => $image_url, 
								'post_mime_type' => 'image/jpeg',
								'post_title'     => 'yatterukun',
								'post_content'   => '',
								'post_status'    => 'inherit'
							);
				 			$attach_id_new = wp_insert_attachment( $attach, $dst_file_new );
				 			require_once( ABSPATH . 'wp-admin/includes/image.php' );
				 			$attach_data = wp_generate_attachment_metadata( $attach_id_new, $dst_file_new );
							wp_update_attachment_metadata( $attach_id_new, $attach_data );
							
							echo 'new attachment id is ' . $attach_id_new . PHP_EOL;
							
							//Update wp_postmeta
							//global $wpdb;
							$results = $wpdb->get_results( 
								"
								SELECT meta_id, meta_value 
								FROM $wpdb->postmeta
								WHERE meta_key = '_thumbnail_id'
								"
							);
							
							foreach ( $results as $result ) 
							{
								$meta_id = $result->meta_id;
								$meta_value = $result->meta_value;
								if ( $meta_value == $attach_id_old ) {
									
									$wpdb->query(
										"
										UPDATE $wpdb->postmeta 
										SET meta_value = $attach_id_new
										WHERE meta_id = $meta_id 
										"
									);
								}
							}
							
							//
							$wpdb->delete( $wpdb->posts, array( 'ID' => $attach_id_old ), array( '%d' ) );
							$wpdb->delete( $wpdb->postmeta, array( 'post_id' => $attach_id_old ), array( '%d' ) );
							
							echo 'Operation completed !' . PHP_EOL;
							
						}
						
					}
					else if ( 'mp4' == $uploaded_file_ext || 'mov' == $uploaded_file_ext) {
						
						echo 'Uploaded_file_ext is ' . $uploaded_file_ext . PHP_EOL;
						
						$dst_file = wp_upload_dir() ['basedir'] .'/yatterukun/yatterukun.mp4';
						if ( file_exists ( $dst_file ) ) {
							yatterukun_delete_files ( $dst_file );
						}
						move_uploaded_file( $_FILES[ $data_name ]['tmp_name'], $dst_file );
						
						echo 'Operation completed !' . PHP_EOL;
						
					}
					
				}else{
					
					echo 'Cannot accept your file. Please check the file extension.' . PHP_EOL;
				}
				
			}
			else{
				
				echo 'Cannot accept your file. Max file size is set at ';
				echo $max_size;
				echo ' MB.' . PHP_EOL;
			}
		}
		else if ( empty( $_FILES[ $data_name ]['temp_name'] ) && isset( $_FILES[ $data_name ][ 'error' ] ) ) {
			
			switch ( $_FILES[ $data_name ][ 'error' ] ) {
	            case UPLOAD_ERR_INI_SIZE:
	                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
	                break;
	            case UPLOAD_ERR_FORM_SIZE:
	                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
	                break;
	            case UPLOAD_ERR_PARTIAL:
	                $message = "The uploaded file was only partially uploaded";
	                break;
	            case UPLOAD_ERR_NO_FILE:
	                $message = "No file was uploaded";
	                break;
	            case UPLOAD_ERR_NO_TMP_DIR:
	                $message = "Missing a temporary folder";
	                break;
	            case UPLOAD_ERR_CANT_WRITE:
	                $message = "Failed to write file to disk";
	                break;
	            case UPLOAD_ERR_EXTENSION:
	                $message = "File upload stopped by extension";
	                break;
	            default:
	                $message = "Unknown upload error";
	                break;
	        }
			echo $message . PHP_EOL;
			
		}
		else{
			echo 'File not found. Please check the attached data name.' . PHP_EOL;
		}
		
	}
	else {
		global $wp_query;
    	$wp_query->set_404();
		status_header( 404 );
		http_response_code(404);
    	nocache_headers();
    	require get_404_template();
    	exit;
	}
}
