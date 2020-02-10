<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
namespace Smackcoders\FCSV;

if ( ! defined( 'ABSPATH' ) )
exit; // Exit if accessed directly

/**
 * Class Security
 * @package Smackcoders\FCSV
 */
class Security {

	protected static $instance = null;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
			self::$instance->doHooks();
		}
		return self::$instance;
	}

	/**
	 * Security constructor.
	 */
	public function __construct() {
		$this->plugin = Plugin::getInstance();
	}

	/**
	 *  Security hooks.
	 */
	public function doHooks(){
		add_action('wp_ajax_security_performance', array($this,'securityPerformance'));
		add_action('wp_ajax_active_addons', array($this,'activeAddons'));
	}

	public function activeAddons(){
		$result = array();
		if(is_plugin_active('wp-ultimate-exporter/wp-ultimate-exporter.php') ){
			$result['exporter'] = true;
		}
		else{
			$result['exporter'] = false;
		}
		if(is_plugin_active('import-woocommerce/import-woocommerce.php') ){
			$result['woocommerce'] = true;
		}
		else{
			$result['woocommerce'] = false;
		}
		if(is_plugin_active('import-users/import-users.php') ){
			$result['users'] = true;
		}
		else{
			$result['users'] = false;
		}
		echo wp_json_encode($result);
		wp_die();
	}

	public function get_active_plugins() {
		$active_plugins = get_option('active_plugins');
		return $active_plugins;
	}

	public  function securityPerformance(){
		global $wpdb,$wp_version;
		$result['post_max_size'] = ini_get('post_max_size');
		$result['auto_append_file'] = ini_get('auto_append_file');
		$result['auto_prepend_file'] = ini_get('auto_prepend_file');
		$result['upload_max_filesize'] = ini_get('upload_max_filesize');
		$result['max_execution_time'] = ini_get('max_execution_time');
		$result['max_input_time'] = ini_get('max_input_time');
		$result['max_input_vars'] = ini_get('max_input_vars');
		$result['php_version'] = phpversion();
		$loader_extensions = get_loaded_extensions();
		if(in_array('PDO', $loader_extensions)) {
			$result['PDO'] = 'Yes';
		}
		else{
			$result['PDO'] = 'No';
		}
		if(in_array('curl', $loader_extensions)) {
			$result['curl'] = 'Yes';
		}
		else{
			$result['curl'] = 'No';
		}
		if(ini_get('file_uploads') == 1){
			$result['file_uploads'] = 'On';
		}
		else{
		     $result['file_uploads'] = 'Off';
		}
		if(ini_get('allow_url_fopen') == 1){
			$result['allow_url_fopen'] = 'On';
		}
		else{
			$result['allow_url_fopen'] = 'Off';
		}
		$result['wp_version'] = $wp_version;
		$result['db_version'] = $wpdb->db_version();
		$result['server_software'] = $_SERVER[ 'SERVER_SOFTWARE' ];
		$result['http_agent'] = $_SERVER['HTTP_USER_AGENT'];
		$result['db_prefix'] = $wpdb->prefix;
		$result['wp_memory_limit'] = (int) ini_get('memory_limit');
		if(is_multisite()){
			$result['wp_multi_site'] = 'Eanbled';
		}
		else{
		$result['wp_multi_site'] = 'Disabled';
		}
		echo wp_json_encode($result);
		wp_die();

	}

}
