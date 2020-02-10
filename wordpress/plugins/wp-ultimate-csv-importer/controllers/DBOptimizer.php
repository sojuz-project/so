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
 * Class DBOptimizer
 * @package Smackcoders\FCSV
 */
class DBOptimizer {

	protected static $instance = null;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
			self::$instance->doHooks();
		}
		return self::$instance;
	}

	/**
	 * DBOptimizer constructor.
	 */
	public function __construct() {
		$this->plugin = Plugin::getInstance();
	}

	/**
	 * DBOptimizer hooks.
	 */
	public function doHooks(){
		add_action('wp_ajax_database_optimization_process', array($this,'DbOptimizer'));
	}

	/**
	 * Function for optimizing the database
	 *
	 */
	public function DbOptimizer() {
		global $wpdb;
		$affected_rows = array('orphaned' => 'non_affected', 'unassignedTags' => 'non_affected', 'postpagerevisions' => 'non_affected', 'autodraftedpostpage' => 'non_affected', 'postpagetrash' => 'non_affected', 'spamcomments' => 'non_affected', 'trashedcomments' => 'non_affected', 'unapprovedcomments' => 'non_affected', 'pingbackcomments' => 'non_affected', 'trackbackcomments' => 'non_affected');
		if(sanitize_text_field($_POST['orphaned']) == 'true') {
			$array_post_id = '';
			$get_post_id = $wpdb->get_results($wpdb->prepare("select DISTINCT pm.post_id from {$wpdb->prefix}postmeta pm JOIN {$wpdb->prefix}posts wp on wp.ID = %d", 'pm.post_id'));
			foreach($get_post_id as $postID) {
				$array_post_id .= $postID->post_id . ',';
			}
			$array_post_id = substr($array_post_id, 0, -1);
			$get_post_meta_id = $wpdb->get_results($wpdb->prepare("DELETE FROM {$wpdb->prefix}postmeta where post_id not in (%d)",$array_post_id),ARRAY_A);
			$affected_rows['orphaned'] = $wpdb->rows_affected;
		} 
		if(sanitize_text_field($_POST['unassignedTags']) == 'true') {
			$wpdb->query("DELETE t,tt FROM  $wpdb->terms t INNER JOIN {$wpdb->prefix}term_taxonomy tt ON t.term_id=tt.term_id WHERE tt.taxonomy='post_tag' AND tt.count=0");
			$affected_rows['unassignedTags'] = $wpdb->rows_affected;
		}
		if(sanitize_text_field($_POST['postpagerevisions']) == 'true') {
			$wpdb->query("DELETE FROM {$wpdb->prefix}posts WHERE post_type = 'revision'");
			$affected_rows['postpagerevisions'] = $wpdb->rows_affected;
		}
		if(sanitize_text_field($_POST['autodraftedpostpage']) == 'true') {
			$wpdb->query("DELETE FROM {$wpdb->prefix}posts WHERE post_status = 'auto-draft'");
			$affected_rows['autodraftedpostpage'] = $wpdb->rows_affected;
		}
		if(sanitize_text_field($_POST['postpagetrash']) == 'true') {
			$wpdb->query("DELETE FROM {$wpdb->prefix}posts WHERE post_status = 'trash'");
			$affected_rows['postpagetrash'] = $wpdb->rows_affected;
		}
		if(sanitize_text_field($_POST['spamcomments']) == 'true') {
			$wpdb->query("DELETE FROM {$wpdb->prefix}comments WHERE comment_approved = 'spam'");
			$affected_rows['spamcomments'] = $wpdb->rows_affected;
		}
		if(sanitize_text_field($_POST['trashedcomments']) == 'true') {
			$wpdb->query("DELETE FROM {$wpdb->prefix}comments WHERE comment_approved = 'trash'");
			$affected_rows['trashedcomments'] = $wpdb->rows_affected;
		}
		if(sanitize_text_field($_POST['unapprovedcomments']) == 'true') {
			$wpdb->query("DELETE FROM {$wpdb->prefix}comments WHERE comment_approved = '0'");
			$affected_rows['unapprovedcomments'] = $wpdb->rows_affected;
		}
		if(sanitize_text_field($_POST['pingbackcomments']) == 'true') {
			$wpdb->query("DELETE FROM {$wpdb->prefix}comments WHERE comment_type = 'pingback'");
			$affected_rows['pingbackcomments'] = $wpdb->rows_affected;
		}
		if(sanitize_text_field($_POST['trackbackcomments']) == 'true') {
			$wpdb->query("DELETE FROM {$wpdb->prefix}comments WHERE comment_type = 'trackback'");
			$affected_rows['trackbackcomments'] = $wpdb->rows_affected;
		}
		echo wp_json_encode($affected_rows);
		wp_die();
	}
}
