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

class ExportExtension {

	public $response = array();
	public  $headers = array();
	public  $module;	
	public  $exportType = 'csv';
	public $optionalType = null;	
	public $conditions = array();	
	public $eventExclusions = array();
	public $fileName;	
	public $data = array();	
	public $heading = true;	
	public $delimiter = ',';
	public $enclosure = '"';
	public $auto_preferred = ",;\t.:|";
	public $output_delimiter = ',';
	public $linefeed = "\r\n";
	public $export_mode;
	public $export_log = array();
	public $limit;
	protected static $instance = null,$mapping_instance,$export_handler,$post_export,$woocom_export,$review_export,$ecom_export;
	protected $plugin,$activateCrm,$crmFunctionInstance;
	public $plugisnScreenHookSuffix=null;
	public static function getInstance() {
		global $post_export_class;
		if ( null == self::$instance ) {
			self::$instance = new self;
			ExportExtension::$mapping_instance = MappingExtension::getInstance();
			self::$instance->doHooks();
		}
		return self::$instance;
	}	

	public  function doHooks(){
		add_action('wp_ajax_total_records', array($this, 'totalRecords'));
		add_action('wp_ajax_check_export', array($this, 'checkExport'));
		/*add_action('wp_ajax_parse_data',array($this,'parseData'));
		add_action('wp_ajax_total_records', array($this, 'totalRecords'));*/
	}

	public function checkExport(){
		if(\is_plugin_active('wp-ultimate-exporter/wp-ultimate-exporter.php')){
			$result['success'] =true;
		}
		else{
			$result['success'] = false;
		}
		echo wp_json_encode($result);
		wp_die();
	}

	public function totalRecords(){

		global $wpdb,$post_export_class;
		$module = $_POST['module'];
		$optionalType = $_POST['optionalType'];
		if ($module == 'WooCommerceOrders') {
			$module = 'shop_order';
		}
		elseif ($module == 'WooCommerceCoupons') {
			$module = 'shop_coupon';
		}
		elseif ($module == 'Marketpress') {
			$module = 'product';
		}
		elseif ($module == 'WooCommerceRefunds') {
			$module = 'shop_order_refund';
		}
		elseif ($module == 'WooCommerceVariations') {
			$module = 'product_variation';
		}
		elseif($module == 'WPeCommerceCoupons'){
			$query = $wpdb->get_col("SELECT * FROM {$wpdb->prefix}wpsc_coupon_codes");
			echo wp_json_encode(count($query));
			wp_die();
		}
		elseif($module == 'Comments'){
			$get_all_comments = $this->commentsCount();	
			echo wp_json_encode($get_all_comments);
			wp_die();
		}
		elseif($module == 'Users'){
			$get_available_user_ids = "select DISTINCT ID from {$wpdb->prefix}users u join {$wpdb->prefix}usermeta um on um.user_id = u.ID";
			$availableUsers = $wpdb->get_col($get_available_user_ids);
			$total = count($availableUsers);
			echo wp_json_encode($total);
			wp_die();
		}
		elseif($module == 'Tags'){
			$get_all_terms = get_tags('hide_empty=0');
			echo wp_json_encode(count($get_all_terms));
			wp_die();
		}
		elseif($module == 'Categories'){
			$get_all_terms = get_categories('hide_empty=0');
			echo wp_json_encode(count($get_all_terms));
			wp_die();
		}
		elseif($module == 'Taxonomies'){
			$query = "SELECT * FROM {$wpdb->prefix}terms t INNER JOIN {$wpdb->prefix}term_taxonomy tax 
				ON  `tax`.term_id = `t`.term_id WHERE `tax`.taxonomy =  '{$optionalType}'";         
				$get_all_taxonomies =  $wpdb->get_results($query);
			echo wp_json_encode(count($get_all_taxonomies));
			wp_die();
		}
		else {
			if($module == 'CustomPosts') {
				$optional_type = $optionalType;
			}
			$module = $post_export_class->import_post_types($module,$optional_type);
		}
		$get_post_ids = "select DISTINCT ID from {$wpdb->prefix}posts";
		$get_post_ids .= " where post_type = '$module'";

		/**
		 * Check for specific status
		 */
		if($module == 'shop_order'){

			$get_post_ids .= " and post_status in ('wc-completed','wc-cancelled','wc-on-hold','wc-processing','wc-pending')";

		}elseif ($module == 'shop_coupon') {

			$get_post_ids .= " and post_status in ('publish','draft','pending')";

		}elseif ($module == 'shop_order_refund') {

		}
		else{
			$get_post_ids .= " and post_status in ('publish','draft','future','private','pending')";
		}
		$get_total_row_count = $wpdb->get_col($get_post_ids);
		$total = count($get_total_row_count);
		echo wp_json_encode($total);
		wp_die();
	}

	/**
	 * ExportExtension constructor.
	 * Set values into global variables based on post value
	 */
	public function __construct() {
		global $post_export_class;
		$this->plugin = Plugin::getInstance();
	}

	public  function parseData(){
		global $export_class;
		$export_class->parseData($_POST);
	}

	public function commentsCount($mode = null) {
		global $wpdb;
		$get_comments = "select * from $wpdb->comments";
		// Check status
		if($this->conditions['specific_status']['is_check'] == 'true') {
			if($this->conditions['specific_status']['status'] == 'Pending')
				$get_comments .= " where comment_approved = '0'";
			elseif($this->conditions['specific_status']['status'] == 'Approved')
				$get_comments .= " where comment_approved = '1'";
			else
				$get_comments .= " where comment_approved in ('0','1')";
		}
		else
			$get_comments .= " where comment_approved in ('0','1')";
		// Check for specific period
		if($this->conditions['specific_period']['is_check'] == 'true') {
			$get_comments .= " and comment_date >= '" . $this->conditions['specific_period']['from'] . "' and comment_date <= '" . $this->conditions['specific_period']['to'] . "'";
		}
		// Check for specific authors
		if($this->conditions['specific_authors']['is_check'] == 'true') {
			if(isset($this->conditions['specific_authors']['author'])) {
				$get_comments .= " and comment_author_email = '".$this->conditions['specific_authors']['author']."'"; 
			}
		}
		$get_comments .= " order by comment_ID";
		$comments = $wpdb->get_results( $get_comments );
		$totalRowCount = count($comments);
		return $totalRowCount;
	}

	/**
	 * Get activated plugins
	 * @return mixed
	 */
	public function get_active_plugins() {
		$active_plugins = get_option('active_plugins');
		return $active_plugins;
	}

}
