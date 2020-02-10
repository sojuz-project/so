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
 * Class SendPassword
 * @package Smackcoders\FCSV
 */
class SendPassword {

	protected static $instance = null;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
			self::$instance->doHooks();
		}
		return self::$instance;
	}

	/**
	 * SendPassword constructor.
	 */
	public function __construct() {
		$this->plugin = Plugin::getInstance();
	}

	/**
	 * SendPassword hooks.
	 */
	public function doHooks(){
		add_action('wp_ajax_settings_options', array($this,'settingsOptions'));
		add_action('wp_ajax_send_login_credentials_to_users', array($this,'send_login_credentials_to_users'));
		add_action('wp_ajax_get_options', array($this,'showOptions'));
	}

	/**
	 * Function for save settings options
	 *
	 */
	public function settingsOptions() {
		$ucisettings = get_option('sm_uci_pro_settings');
		$option = sanitize_text_field($_POST['option']);
		$value = sanitize_text_field($_POST['value']);
		foreach ($ucisettings as $key => $val) {
			$settings[$key] = $val;
		}
		$settings[$option] = $value;
		update_option('sm_uci_pro_settings', $settings);
		$result['success'] = true;
		echo wp_json_encode($result);
		wp_die();
	}

	/**
	 * Function for show settings options
	 *
	 */
	public function showOptions() {
		$ucisettings = get_option('sm_uci_pro_settings');
		foreach ($ucisettings as $key => $val) {
			$settings[$key] = json_decode($val);
		}
		$result['options'] = $settings;
		echo wp_json_encode($result);
		wp_die();
	}

	/**
	 * send login credential to user
	 *
	 */
	public  function send_login_credentials_to_users() {
		require_once(ABSPATH . "wp-includes/pluggable.php");
		global $wpdb;
		$ucisettings = get_option('sm_uci_pro_settings');
		if($ucisettings['send_user_password'] == "true") {
			$get_user_meta_info = $wpdb->get_results( $wpdb->prepare("select *from {$wpdb->prefix}usermeta where meta_key like %s", '%' . 'smack_uci_import' . '%') );
			if(!empty($get_user_meta_info)) {
				foreach($get_user_meta_info as $key => $value) {
					$data_array = maybe_unserialize($value->meta_value);
					$currentUser             = wp_get_current_user();
					$admin_email             = $currentUser->user_email;
					$em_headers              = "From: Administrator <$admin_email>"; # . "\r\n";
					$message                 = "Hi,You've been invited with the role of " . $data_array['role'] . ". Here, your login details." . "\n" . "username: " . $data_array['user_login'] . "\n" . "userpass: " . $data_array['user_pass'] . "\n" . "Please click here to login " . wp_login_url();
					$emailaddress            = $data_array['user_email'];
					$subject                 = 'Login Details';
					if( wp_mail( $emailaddress, $subject, $message) ){
						delete_user_meta($value->user_id, 'smack_uci_import');
					}
				}
			}
		}
	}
}
