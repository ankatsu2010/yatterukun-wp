<?php
class Yatterukun {
	const WP_SETTINGS_KEY = 'yatterukun_settings_key';
	private static $_settings;
	private static $_file_extensions = array('jpg', 'mp4', 'mov');
	/**
	 *Constructor
	 */
	function __construct() {
		add_action('admin_menu', array( $this, 'add_setting_page' ) );
		add_filter( 'template_include', array( $this, 'template_loader' ) );
		
		if (function_exists( 'register_activation_hook' ))
			register_activation_hook( __DIR__.'/index.php', array ( $this, 'init_yatterukun_file' ) );
		
		//add_action( 'send_headers', array( $this, 'add_header_nocache' ) );
		add_filter('the_content', array( $this, 'img_cache_buster' ));
		//add_action('wp_footer', array( $this, 'theme_header_cache_buster' ) );
		
		//add_filter( 'header_video_settings', array( $this, 'header_video_chachebuster' ) );
		
		
		add_action( 'plugins_loaded', array( $this, 'yatterukun_load_plugin_textdomain' ) );
		
	}
	/**
	 *Prepare placehoder dummy file
	 */
	function init_yatterukun_file(){
		/*
		 * jpg place holder file
		 */
	 	$src_file = plugin_dir_path( __FILE__ ) . 'images/yatterukun.jpg';
	 	$dst_dir = ABSPATH .'wp-content/uploads/yatterukun';
	 	$dst_file = $dst_dir .'/yatterukun.jpg';
	 	
	 	if ( ! file_exists ( $dst_dir) ) {
	 		wp_mkdir_p( $dst_dir );
	 	}
	 	
	 	if ( ! file_exists ( $dst_file ) ) {
	 		if ( copy ( $src_file, $dst_file ) ) {
	 			
	 			$filetype = wp_check_filetype( basename( $dst_file ), null );
	 			$wp_upload_url = site_url( '/uploads/yatterukun/', 'https' );
	 			$attachment = array(
					'guid'           => $wp_upload_url . 'yatterukun.jpg', 
					'post_mime_type' => $filetype['type'],
					'post_title'     => '',
					'post_content'   => '',
					'post_status'    => 'inherit'
				);
	 			$attach_id = wp_insert_attachment( $attachment, $dst_file );
	 			require_once( ABSPATH . 'wp-admin/includes/image.php' );
	 			$attach_data = wp_generate_attachment_metadata( $attach_id, $dst_file );
				wp_update_attachment_metadata( $attach_id, $attach_data );
	 			
	 		}
	 	}
	 	/*
		 * mp4 place holder file
		 */
	 	$src_file = plugin_dir_path( __FILE__ ) . 'images/yatterukun.mp4';
	 	$dst_dir = ABSPATH .'wp-content/uploads/yatterukun';
	 	$dst_file = $dst_dir .'/yatterukun.mp4';
	 	$buster = '?x=' . rand();
	 	
	 	if ( ! file_exists ( $dst_file ) ) {
	 		if ( copy ( $src_file, $dst_file ) ) {
	 			
	 			$filetype = wp_check_filetype( basename( $dst_file ), null );
	 			$wp_upload_url = site_url( '/uploads/yatterukun/', 'https' );
	 			$attachment = array(
					'guid'           => $wp_upload_url . 'yatterukun.mp4', 
					'post_mime_type' => 'video/mp4',
					'post_title'     => '',
					'post_content'   => '',
					'post_status'    => 'inherit'
				);
	 			$attach_id = wp_insert_attachment( $attachment, $dst_file );
	 			require_once( ABSPATH . 'wp-admin/includes/image.php' );
	 			$attach_data = wp_generate_attachment_metadata( $attach_id, $dst_file );
				wp_update_attachment_metadata( $attach_id, $attach_data );
	 			
	 			
	 			
	 		}
	 	}
	 }
	 
	/**
	 *Add settings menu
	 */
	function add_setting_page() {
		add_options_page(
            __('Yatterukun Settings', 'yatterukun'),
            __('Yatterukun', 'yatterukun'),
            'manage_options',
            'yatterukun-settings',
            array($this, 'show_setting')
        );
	}
	/**
	 *Settings page
	 */
	function show_setting() {
		if ( isset($_POST['submit'])) {
			check_admin_referer('yatterukun_settings_nonce');
			$fields = array('page_slug', 'user_name', 'upload_key', 'data_name', 'max_size', 'file_types');
			foreach ($fields as $field) {
                if (array_key_exists($field, $_POST) && $_POST[$field]) {
                	
                	static::$_settings[$field] = $_POST[$field];
                }
            }
            update_option(self::WP_SETTINGS_KEY, static::$_settings);
            $message = __('Settings Saved.', 'yatterukun');
		}
		include_once( 'setting.php' );
	}
	/**
     * Returns options array
     * @return array
     */
    public static function getOptions()
    {
        if (static::$_settings) {
            return static::$_settings;
        }
        $default_user = wp_get_current_user()->display_name;
        $upload_key = chr(mt_rand(65,90)) . chr(mt_rand(65,90)) . chr(mt_rand(65,90)) .
        				chr(mt_rand(65,90)) . chr(mt_rand(65,90)) . chr(mt_rand(65,90)) .
        				chr(mt_rand(65,90)) . chr(mt_rand(65,90)) . chr(mt_rand(65,90)) .
            			chr(mt_rand(65,90)) . chr(mt_rand(65,90)) . chr(mt_rand(65,90));
        
        $defaults = array(
            'page_slug' => 'yatterukun',
            'user_name' => $default_user,
            'upload_key' => $upload_key,
            'data_name' => 'yatterukun_data',
            'max_size' => 2,
            'file_types' => static::$_file_extensions,
        );
        
        return static::$_settings = wp_parse_args(get_option(self::WP_SETTINGS_KEY), $defaults);
    }
	/**
     * Returns the option value with specific key
     * @param $key
     * @return mixed
     */
    public static function getOption($key, $default = null)
    {
        $options = static::getOptions();
        if (isset($options[$key]) === false) {
            return $default;
        }
        return $options[$key];
    }
    
    /**
     * Browser cache buster
     */
    function add_header_nocache() {
    	header( 'Cache-Control: no-cache, must-revalidate, max-age=0' );
    }
    
    function img_cache_buster ( $content ) {
    	$buster = '?x=' . rand() . '"';
    	//$pattern = '/\/yatterukun\/(yatterukun.*?\.)(jpg.*?"|mp4.*?")/';
    	$pattern = '/\/yatterukun\/(yatterukun.*?\.)(jpg|mp4).*?"/';
    	
    	//$replacement = '/yatterukun/$1$2' . $buster . '"';
    	$replacement = '/yatterukun/$1$2' . $buster;
    	
    	return preg_replace($pattern, $replacement, $content);
    }
    
    function theme_header_cache_buster() {
    	?>
    	<script type="text/javascript">
    	var headerElem = document.getElementsByTagName( 'header' );
    	if( headerElem.length > 0 ){
    		//headerElem[0].style.display = 'none';
    		headerElem[0].style.visibility = 'hidden';
    	}
    	
    	window.onload = function() {
	    	var buster = '?x=' + Math.floor( Math.random() * 1000000 );
	    	//var headerElem = document.getElementsByTagName( 'header' );
	    	//console.log( headerElem.length );
	    	if( headerElem.length > 0 ){
	    		var originStr = headerElem[0].innerHTML;
	    		var newStr = originStr.replace( 'yatterukun.mp4"', 'yatterukun.mp4' + buster + '"');
	    		//console.log( newStr );
	    		headerElem[0].innerHTML = newStr;
	    		//headerElem[0].style.display = 'block';
	    		headerElem[0].style.visibility = 'visible';
	    	}
    	}
    	</script>
    	<?php
    }
    
    function header_video_chachebuster ( $settings ){
    	$buster = '?x=' . rand();
    	$editedURL = $settings['url'] . $buster;
    	$settings['url'] = $editedURL;
    	return $settings;
    }
    
    
	/**
	 *
	 */
	 function template_loader( $template ) {
		
		$template_dir = plugin_dir_path( __FILE__ ) . 'templates/';
		
		$page_slug = self::getOption( 'page_slug' );
		
		if ( is_page( $page_slug ) ) {
			$file_name = 'yatterukun-page.php';
			return $template_dir . $file_name;
		}
		
		return $template;
	}
	
	function yatterukun_load_plugin_textdomain() {
	    load_plugin_textdomain( 'yatterukun', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	}
	
	
	
}
