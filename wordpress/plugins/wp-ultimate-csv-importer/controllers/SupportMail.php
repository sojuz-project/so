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
 * Class SupportMail
 * @package Smackcoders\FCSV
 */
class SupportMail {

	protected static $instance = null;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
			self::$instance->doHooks();
		}
		return self::$instance;
	}

	/**
	 * SupportMail constructor.
	 */
	public function __construct() {
		$this->plugin = Plugin::getInstance();
	}

	/**
	 * SupportMail hooks.
	 */
	public function doHooks(){
		add_action('wp_ajax_support_mail', array($this,'supportMail'));
		add_action('wp_ajax_send_subscribe_email', array($this,'sendSubscribeEmail'));
	}

	public static function supportMail(){
		if($_POST){
			$email = $_POST['email'];
			$url = get_option('siteurl');
			$site_name = get_option('blogname');
			$headers = "From: " . $site_name . "<$email>" . "\r\n";
			$headers.= 'MIME-Version: 1.0' . "\r\n";
			$headers= array( "Content-type: text/html; charset=UTF-8");
			$to = 'support@smackcoders.com';
			$subject = $_POST['query'];
			$message = "Site URL: " . $url . "\r\n<br>";
			$message .= "Plugin Name: WP Ultimate CSV Importer PRO " . "\r\n<br>";
			$message .= "Message: "."\r\n" . $_POST['message'] . "\r\n<br>";
			if(wp_mail($to, $subject, $message, $headers)) {
				$success_message = 'Mail Sent!';
				echo wp_json_encode($success_message);
			} else {
				$error_message = "Please draft a mail to support@smackcoders.com. If you doesn't get any acknowledgement within an hour!";
				echo wp_json_encode($error_message);
			}
			wp_die();
		}
	}

	public static function sendSubscribeEmail(){
		if($_POST){
			$email = $_POST['subscribe_email'];
			$url = get_option('siteurl');
			$site_name = get_option('blogname');
			$headers = "From: " . $site_name . "<$email>" . "\r\n";
			$headers.= 'MIME-Version: 1.0' . "\r\n";
			$headers.= "Content-type: text/html; charset=iso-8859-1 \r\n";
			$to = 'support@smackcoders.com';
			$subject = 'Newsletter Subscription';
			$message = "Site URL: " . $url . "\r\n<br>";
			$message .= "Plugin Name: " . SM_UCI_SETTINGS . "\r\n<br>";
			$message .= "Message: Hi Team, I want to subscribe to your newsletter." . "\r\n<br>";
			if(wp_mail($to, $subject, $message, $headers)) {
				$success_message = 'Mail Sent!';
				echo wp_json_encode($success_message);
			} else {
				$error_message = "Please draft a mail to support@smackcoders.com. If you doesn't get any acknowledgement within an hour!";
				echo wp_json_encode($error_message);
			} 
			wp_die();
		}
	}
}
