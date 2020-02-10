<?php
/********************************************************************************************
 * Plugin Name: WP Ultimate CSV Importer
 * Description: Seamlessly create posts, custom posts, pages, media, SEO and more from your CSV data with ease.
 * Version: 6.0.7
 * Text Domain: wp-ultimate-csv-importer
 * Domain Path: /languages
 * Author: Smackcoders
 * Plugin URI: https://goo.gl/kKWPui
 * Author URI: https://goo.gl/kKWPui
 *
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

namespace Smackcoders\FCSV;

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

require_once('Plugin.php');

$extension_uploader = glob( __DIR__ . '/extensionUploader/*.php');
foreach ($extension_uploader as $extension_upload_value) {
	require_once($extension_upload_value);
}

$upload_modules = glob( __DIR__ . '/uploadModules/*.php');
foreach ($upload_modules as $upload_module_value) {
	require_once($upload_module_value);
}

$extension_modules = glob( __DIR__ . '/extensionModules/*.php');
foreach ($extension_modules as $extension_module_value) {
	require_once($extension_module_value);
}

$manager_extension = glob( __DIR__ . '/managerExtensions/*.php');
foreach ($manager_extension as $manager_extension_value) {
	require_once($manager_extension_value);
}

$import_extensions = glob( __DIR__ . '/importExtensions/*.php');
foreach ($import_extensions as $import_extension_value) {
	require_once($import_extension_value);
}

$export_extensions = glob( __DIR__ . '/exportExtensions/*.php');
foreach ($export_extensions as $export_extension_value) {
	require_once($export_extension_value);
}

require_once('Tables.php');
require_once('SaveMapping.php');
require_once('MediaHandling.php');
require_once('ImportConfiguration.php');
require_once('Dashboard.php');
require_once('SmackCSVImporterUninstall.php');
require_once('DragandDropExtension.php');
require_once('controllers/DBOptimizer.php');
require_once('controllers/SendPassword.php');
require_once('controllers/SupportMail.php');
require_once('controllers/Security.php');
require_once('SmackCSVImporterInstall.php');
require_once('languages/LangIT.php');
require_once('languages/LangEN.php');
require_once('languages/LangGE.php');
require_once('languages/LangFR.php');
require_once 'languages/LangES.php';
class SmackCSV extends MappingExtension{

	protected static $instance = null;
	private static $table_instance = null;
	private static $desktop_upload_instance = null;
	private static $url_upload_instance = null;
	private static $xml_instance = null;
	protected static $mapping_instance = null;
	private static $extension_instance = null;
	private static $save_mapping_instance = null;
	private static $plugin_instance = null;
	private static $import_config_instance = null;
	private static $dashboard_instance = null;
	private static $drag_drop_instance = null;
	private static $log_manager_instance = null;
	private static $media_instance = null;
	private static $db_optimizer = null;
	private static $send_password = null ; 
	private static $security = null ;
	private static $support_instance = null ;
	private static $uninstall = null ;
	private static $install = null ;
	private static $export_instance = null ;
	private static $en_instance = null ;
	private static $italy_instance = null ;
	private static $france_instance = null ;
	private static $german_instance = null ;
	private static $spanish_instance = null;
	public $version = '6.0.7';

	public function __construct(){ 
		add_action('init', array(__CLASS__, 'show_admin_menus'));
	}

	public static function show_admin_menus(){
		$ucisettings = get_option('sm_uci_pro_settings');
		if( is_user_logged_in() ) {
			$user = wp_get_current_user();
			$role = ( array ) $user->roles;
		} 
		if(!empty($role) && !empty($role[0]=='administrator')){
			if ( is_user_logged_in() &&  current_user_can('administrator') ) {
				add_action('admin_menu',array(__CLASS__,'testing_function'));
			}
		}else{
			if ( is_user_logged_in() && ( current_user_can( 'author') || current_user_can('editor') ) && $ucisettings['author_editor_access'] == "true" ) {
				add_action('admin_menu',array(__CLASS__,'editor_menu'));
			}
		}
	}

	public static function getInstance() {
		if (SmackCSV::$instance == null) {
			SmackCSV::$instance = new SmackCSV;
			SmackCSV::$table_instance = Tables::getInstance();
			SmackCSV::$desktop_upload_instance = DesktopUpload::getInstance(); 
			SmackCSV::$url_upload_instance = UrlUpload::getInstance(); 
			SmackCSV::$xml_instance = XmlHandler::getInstance();
			SmackCSV::$mapping_instance = MappingExtension::getInstance();
			SmackCSV::$extension_instance = new ExtensionHandler;
			SmackCSV::$save_mapping_instance = SaveMapping::getInstance();
			SmackCSV::$media_instance = MediaHandling::getInstance();
			SmackCSV::$import_config_instance = ImportConfiguration::getInstance();
			SmackCSV::$dashboard_instance = Dashboard::getInstance();
			SmackCSV::$drag_drop_instance = DragandDropExtension::getInstance();
			SmackCSV::$log_manager_instance = LogManager::getInstance();
			SmackCSV::$plugin_instance = Plugin::getInstance();
			SmackCSV::$db_optimizer = DBOptimizer::getInstance();
			SmackCSV::$send_password = SendPassword::getInstance();
			SmackCSV::$security = Security::getInstance();
			SmackCSV::$support_instance = SupportMail::getInstance();
			SmackCSV::$uninstall = SmackUCIUnInstall::getInstance();
			SmackCSV::$install = SmackCSVInstall::getInstance();
			SmackCSV::$export_instance = ExportExtension::getInstance();
			SmackCSV::$italy_instance = LangIT::getInstance();
			SmackCSV::$france_instance = LangFR::getInstance();
			SmackCSV::$german_instance = LangGE::getInstance();
			SmackCSV::$en_instance = LangEN::getInstance();
			SmackCSV::$spanish_instance = LangES::getInstance();
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ),  array(SmackCSV::$install, 'plugin_row_meta'), 10, 2 );
			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			if ( is_plugin_active('wp-ultimate-csv-importer-pro/wp-ultimate-csv-importer-pro.php') ) {
				add_action( 'admin_notices', array( SmackCSV::$install, 'wp_ultimate_csv_importer_notice' ) );
				add_action( 'admin_notices', array(SmackCSV::$install, 'important_cron_notice') );
			}
			self::init_hooks();

			return SmackCSV::$instance;
		}
		return SmackCSV::$instance;
	}


	public static function init_hooks() {																																												
		$ucisettings = get_option('sm_uci_pro_settings');
		if(isset($ucisettings['enable_main_mode']) && $ucisettings['enable_main_mode'] == 'true') {
			add_action( 'admin_bar_menu', array(SmackCSV::$instance,'admin_bar_menu'));
			add_action('wp_head', array(SmackCSV::$instance,'activate_maintenance_mode'));		
		}
		register_deactivation_hook( __FILE__, array( SmackCSV::$uninstall, 'unInstall' ) );
	}

	public static function testing_function (){
		remove_menu_page('com.smackcoders.csvimporternew.menu');
		$my_page = add_menu_page('Ultimate CSV Importer Free', 'Ultimate CSV Importer Free', 'manage_options',
			'com.smackcoders.csvimporternew.menu',array(__CLASS__,'menu_testing_function'),plugins_url("assets/images/wp-ultimate-csv-importer.png",__FILE__));
		add_action('load-'.$my_page, array(__CLASS__, 'load_admin_js'));
	}

	public static function load_admin_js() {
		add_action('admin_enqueue_scripts',array(__CLASS__,'csv_enqueue_function'));
	}

	public function editor_menu (){
		remove_menu_page('com.smackcoders.csvimporternew.menu');
		$my_page = add_menu_page('Ultimate CSV Importer Free', 'Ultimate CSV Importer Free', '2',
			'com.smackcoders.csvimporternew.menu',array(__CLASS__,'menu_testing_function'),plugins_url("assets/images/wp-ultimate-csv-importer.png",__FILE__));
		add_action('load-'.$my_page, array(__CLASS__, 'load_admin_js'));
	}

	public static function menu_testing_function(){
		?><div id="wp-csv-importer-admin"></div><?php
	}

	public static function csv_enqueue_function(){
		$upload = wp_upload_dir();
		$upload_base_url = $upload['baseurl'];       
		wp_register_script(SmackCSV::$plugin_instance->getPluginSlug().'jquery-ui-js',plugins_url( 'assets/js/deps/jquery-ui.min.js', __FILE__), array('jquery'));
		wp_enqueue_script(SmackCSV::$plugin_instance->getPluginSlug().'jquery-ui-js');
		wp_register_script(SmackCSV::$plugin_instance->getPluginSlug().'popper',plugins_url( 'assets/js/deps/popper.js', __FILE__), array('jquery'));
		wp_enqueue_script(SmackCSV::$plugin_instance->getPluginSlug().'popper');
		wp_register_script(SmackCSV::$plugin_instance->getPluginSlug().'bootstrap',plugins_url( 'assets/js/deps/bootstrap.min.js', __FILE__), array('jquery'));
		wp_enqueue_script(SmackCSV::$plugin_instance->getPluginSlug().'bootstrap');
		wp_register_script(SmackCSV::$plugin_instance->getPluginSlug().'main-js',plugins_url( 'assets/js/deps/main.js', __FILE__), array('jquery'));
		wp_enqueue_script(SmackCSV::$plugin_instance->getPluginSlug().'main-js');
		wp_register_script(SmackCSV::$plugin_instance->getPluginSlug().'file-tree',plugins_url( 'assets/js/deps/jQueryFileTree.min.js', __FILE__), array('jquery'));
		wp_enqueue_script(SmackCSV::$plugin_instance->getPluginSlug().'file-tree');
		wp_localize_script(SmackCSV::$plugin_instance->getPluginSlug().'script_csv_importer', 'wpr_object', array('imagePath' => plugins_url('/assets/images/', __FILE__)  ));
		$upload_url = $upload_base_url . '/smack_uci_uploads/imports';
		wp_enqueue_style(SmackCSV::$plugin_instance->getPluginSlug().'bootstrap-css', plugins_url( 'assets/css/deps/bootstrap.min.css', __FILE__));
		wp_enqueue_style(SmackCSV::$plugin_instance->getPluginSlug().'filepond-css', plugins_url( 'assets/css/deps/filepond.min.css', __FILE__));
		wp_enqueue_style(SmackCSV::$plugin_instance->getPluginSlug().'react-datepicker-css', plugins_url( 'assets/css/deps/react-datepicker.css', __FILE__));
		wp_enqueue_style(SmackCSV::$plugin_instance->getPluginSlug().'react-toasty-css', plugins_url( 'assets/css/deps/ReactToastify.min.css', __FILE__));	
		wp_enqueue_style(SmackCSV::$plugin_instance->getPluginSlug().'csv-importer-css', plugins_url( 'assets/css/deps/csv-importer-free.css', __FILE__));
		wp_register_script(SmackCSV::$plugin_instance->getPluginSlug().'main-js',plugins_url( 'assets/js/deps/main.js', __FILE__), array('jquery'));
		wp_enqueue_script(SmackCSV::$plugin_instance->getPluginSlug().'main-js');
		wp_register_script(SmackCSV::$plugin_instance->getPluginSlug().'script_csv_importer',plugins_url( 'assets/js/admin-v6.0.5.js', __FILE__), array('jquery'));
		wp_enqueue_script(SmackCSV::$plugin_instance->getPluginSlug().'script_csv_importer');
		$language = get_locale();
		if($language == 'it_IT'){
			$contents = SmackCSV::$italy_instance->contents();
			$response = wp_json_encode($contents);
			wp_localize_script(SmackCSV::$plugin_instance->getPluginSlug().'script_csv_importer', 'wpr_object', array( 'file' => $response,__FILE__, 'imagePath' => plugins_url('/assets/images/', __FILE__),'logfielpath' => $upload_url));
		}

		elseif($language == 'FR_fr'){
			$contents = SmackCSV::$france_instance->contents();
			$response = wp_json_encode($contents);
			wp_localize_script(SmackCSV::$plugin_instance->getPluginSlug().'script_csv_importer', 'wpr_object', array( 'file' => $response,__FILE__, 'imagePath' => plugins_url('/assets/images/', __FILE__),'logfielpath' => $upload_url));
		}
		elseif($language == 'de_DE'){
			$contents = SmackCSV::$german_instance->contents();
			$response = wp_json_encode($contents);
			wp_localize_script(SmackCSV::$plugin_instance->getPluginSlug().'script_csv_importer', 'wpr_object', array( 'file' => $response,__FILE__, 'imagePath' => plugins_url('/assets/images/', __FILE__),'logfielpath' => $upload_url));
		}elseif ($language == 'es_ES') {
			$contents = SmackCSV::$spanish_instance->contents();
			$response = wp_json_encode($contents);
			wp_localize_script(SmackCSV::$plugin_instance->getPluginSlug() . 'script_csv_importer', 'wpr_object', array('file' => $response, __FILE__, 'imagePath' => plugins_url('/assets/images/', __FILE__),'logfielpath' => $upload_url));
		}

		else{
			$contents = SmackCSV::$en_instance->contents();
			$response = wp_json_encode($contents);
			wp_localize_script(SmackCSV::$plugin_instance->getPluginSlug().'script_csv_importer', 'wpr_object', array( 'file' => $response,__FILE__ , 'imagePath' => plugins_url('/assets/images/', __FILE__),'logfielpath' => $upload_url));
		}
	}


	/**
	 * Generates unique key for each file.
	 * @param string $value - filename
	 * @return string hashkey
	 */
	public function convert_string2hash_key($value) {
		$file_name = hash_hmac('md5', "$value" . time() , 'secret');
		return $file_name;
	}


	/**
	 * Creates a folder in uploads.
	 * @return string path to that folder
	 */
	public function create_upload_dir(){

		$upload = wp_upload_dir();
		$upload_dir = $upload['basedir'];
		if(!is_dir($upload_dir)){
			return false;
		}else{
			$upload_dir = $upload_dir . '/smack_uci_uploads/imports/';	
			if (!is_dir($upload_dir)) {
				wp_mkdir_p( $upload_dir);
			}
			chmod($upload_dir, 0777);		
			return $upload_dir;
		}
		chmod($upload_dir, 0777);		
		return $upload_dir;
	}

	public function delete_image_schedule()
	{

		global $wpdb;
		$wpdb->get_results("DELETE FROM {$wpdb->prefix}ultimate_csv_importer_shortcode_manager");
	}

	public function image_schedule()
	{

		global $wpdb;
		$get_result = $wpdb->get_results("SELECT DISTINCT post_id FROM {$wpdb->prefix}ultimate_csv_importer_shortcode_manager", ARRAY_A);
		$records = array_column($get_result, 'post_id');

		foreach ($records as $title => $id) {
			$core_instance = CoreFieldsImport::getInstance();
			$post_id = $core_instance->image_handling($id);
		}
	}

	public function admin_bar_menu(){
		global $wp_admin_bar;
		$wp_admin_bar->add_menu( array(
			'id'     => 'debug-bar',
			'href' => admin_url().'admin.php?page=com.smackcoders.csvimporternew.menu',
			'parent' => 'top-secondary',
			'title'  => apply_filters( 'debug_bar_title', __('Maintenance Mode', 'ultimate-maintenance-mode') ),
			'meta'   => array( 'class' => 'smack-main-mode' ),
		) );
	}

	public function activate_maintenance_mode() { 
		include(ABSPATH . "wp-includes/pluggable.php");
		global $maintainance_text;
		$maintainance_text = "Site is under maintenance mode. Please wait few min!";
		if(!current_user_can('manage_options')) {
?> 
			<div class="main-mode-front"> <span> <?php echo $maintainance_text; ?> </span> </div> 
<?php }
	} 
}

global $csv_class;
$csv_class = new SmackCSV();

$activate_plugin = new SmackCSVInstall();
register_activation_hook( __FILE__, array($activate_plugin,'install'));

add_action( 'plugins_loaded', 'Smackcoders\\FCSV\\onpluginsload' );

function onpluginsload(){
	$plugin = SmackCSV::getInstance();
}

?>
