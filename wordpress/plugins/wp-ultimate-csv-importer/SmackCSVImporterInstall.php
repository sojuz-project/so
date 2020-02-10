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

class SmackCSVInstall {

	protected static $instance = null,$smack_instance,$tables_instance;

	/**
	 * SmackCSVInstall Constructor
	 */
	public function __construct() {
		$this->plugin = Plugin::getInstance();
		self::$tables_instance = new Tables();
	}

	/**
	 * SmackCSVInstall Instance
	 */
	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
			self::$instance->doHooks();
		}
		return self::$instance;
	}

	/**
	 * SmackCSVInstall constructor.
	 */
	public static function csvOptions(){
		$callbackUrl['callbackurl']=site_url().'/wp-admin/admin.php?action=csv_options&show=settings';
		echo json_encode($callbackUrl);
		wp_die();
	}

	public function doHooks(){
		add_action('wp_ajax_csv_options', array($this,'csvOptions'));
	}

	/** @var array DB updates that need to be run */
	private static $db_updates = array(
			
			);

	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'check_version' ), 5 );
		add_action( 'admin_init', array( __CLASS__, 'install_actions' ) );
	}

	/**
	 * Check WPUltimateCSVImporterPro version.
	 */
	public static function check_version() {
		if ( get_option( 'ULTIMATE_CSV_IMP_VERSION' ) != SmackUCI()->version )  {
			self::install();
			do_action( 'sm_uci_pro_updated' );
		}
	}

	/**
	 * Install actions when a update button is clicked.
	 */
	public static function install_actions() {
		if ( ! empty( $_GET['do_update_sm_uci_pro'] ) ) {
			self::update();
		}
	}

	/**
	 * Show notice stating update was successful.
	 */
	public static function updated_notice() {
		?>
			<div class='notice updated uci-message wc-connect is-dismissible'>
			<p><?php esc_html__( 'Ultimate CSV Importer PRO data update complete. Thank you for updating to the latest version!', 'wp-ultimate-csv-importer-pro' ); ?></p>
			</div>
			<?php
	}

	/**
	 * Install WUCI.
	 */
	public  function install() {
		$current_uci_version    = get_option( 'ULTIMATE_CSV_IMP_VERSION', null );
		self::$tables_instance->create_tables(); 
		if ( is_null( $current_uci_version )) {
			self::create_options();         // Create option data on the initial stage
	        
		} 

		self::update_uci_version();

		// Flush rules after install
		flush_rewrite_rules();

		// Trigger action
		do_action( 'sm_uci_installed' );
	}

	/**
	 * Update UCI version to current.
	 */
	private static function update_uci_version() {
		$version = '5.7';
		delete_option( 'ULTIMATE_CSV_IMP_VERSION' );
		add_option( 'ULTIMATE_CSV_IMP_VERSION', $version );
	}

	/**
	 * @param null $version
	 * Update DB version to current.
	 */
	private static function update_db_version( $version = null ) {
		delete_option( 'sm_uci_db_version' );
		add_option( 'sm_uci_db_version', is_null( $version ) ? SmackUCI()->version : $version );
	}

	/**
	 * Handle updates.
	 */
	private static function update() {
		$current_db_version = get_option( 'ULTIMATE_CSV_IMP_VERSION' );
		foreach ( self::$db_updates as $version => $updater ) {
			if ( version_compare( $current_db_version, $version, '<' ) ) {
				include_once ( $updater );
				self::update_db_version( $version );
			}
		}

		self::update_db_version();
	}

	/**
	 * Default options.
	 *
	 * Sets up the default options used on the settings page.
	 */
	public static function create_options() {

		// We assign the default option data for the fresh instalization
		$settings = array('debug_mode' => 'off',
				'send_log_email' => 'on',
				'drop_table' => 'off',
				'author_editor_access' => 'off',
				'woocomattr' => 'off'
				);

		add_option('sm_uci_pro_settings', $settings);

	}

	/**
	 * Todo: add PHP docs
	 */
	public static function remove_options() {
		delete_option('ULTIMATE_CSV_IMP_VERSION');
		delete_option('ULTIMATE_CSV_IMPORTER_UPGRADE_VERSION');
	}

	/**
	 * @param $links
	 *
	 * @return array
	 */
	public function smack_uci_action_links( $links ) {
		$links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=sm-uci-settings') ) .'">Settings</a>';
		$links[] = '<a href="http://wp-buddy.com" target="_blank">More plugins by WP-Buddy</a>';
		return $links;
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param       mixed $links Plugin Row Meta
	 * @param       mixed $file  Plugin Base file
	 * @return      array
	 */
	public static function plugin_row_meta( $links, $file ) {
			$row_meta = array(
				'upgrade_to_csv_pro' => '<a style="font-weight: bold;color: #d54e21;font-size: 105%;" href="' . esc_url( apply_filters( 'upgrade_to_csv_pro_url',  'http://www.smackcoders.com/wp-ultimate-csv-importer-pro.html?utm_source=plugin&utm_campaign=csv_importer_free&utm_medium=wordpress' ) ) . '" title="' . esc_attr( __( 'Upgrade to Pro', 'wp-ultimate-csv-importer' ) ) . '" target="_blank">' . __( 'Upgrade to Pro', 'wp-ultimate-csv-importer' ) . '</a>',
				'docs'    => '<a href="' . esc_url( apply_filters( 'sm_uci_docs_url', 'http://www.smackcoders.com/documentation/ultimate-csv-importer-pro/how-to-import-csv?utm_source=plugin&utm_campaign=csv_importer_pro&utm_medium=wordpress' ) ) . '" title="' . esc_attr( __( 'View WP Ultimate CSV Importer Pro Documentation', 'wp-ultimate-csv-importer' ) ) . '" target="_blank">' . __( 'Docs', 'wp-ultimate-csv-importer' ) . '</a>',
				'videos' => '<a href="' . esc_url( apply_filters( 'sm_uci_videos_url', 'https://www.youtube.com/embed/GbDlQcbnNJY?utm_source=plugin&utm_campaign=csv_importer_free&utm_medium=wordpress' ) ) . '" title="' . esc_attr( __( 'View Videos For WP Ultimate CSV Importer Pro', 'wp-ultimate-csv-importer' ) ) . '" target="_blank">' . __( 'Videos', 'wp-ultimate-csv-importer' ) . '</a>',
				'support' => '<a href="' . esc_url( apply_filters( 'sm_uci_support_url', admin_url() . 'admin.php?page=com.smackcoders.csvimporternew.menu&support' ) ) . '" title="' . esc_attr( __( 'Contact Support', 'wp-ultimate-csv-importer' ) ) . '" target="_blank">' . __( 'Support', 'wp-ultimate-csv-importer' ) . '</a>',
				'free_trial' => '<a href="' . esc_url( apply_filters( 'sm_uci_support_url', 'http://www.smackcoders.com/wp-ultimate-csv-importer-pro.html?utm_source=plugin&utm_campaign=csv_importer_free&utm_medium=wordpress' ) ) . '" title="' . esc_attr( __( 'Get your free trial', 'wp-ultimate-csv-importer' ) ) . '" target="_blank">' . __( 'Free Trial', 'wp-ultimate-csv-importer' ) . '</a>',
			);

			return array_merge( $row_meta, $links );
		}

	public static function showUpgradeNotification($currentPluginMetadata, $newPluginMetadata){
		if (isset($newPluginMetadata->upgrade_notice) && strlen(trim($newPluginMetadata->upgrade_notice)) > 0){
			echo '<p style="background-color: #d54e21; padding: 10px; color: #f9f9f9; margin-top: 10px"><strong>'.esc_html__('Important Upgrade Notice:','wp-ultimate-csv-importer').'</strong> ';
			echo esc_html($newPluginMetadata->upgrade_notice), '</p>';
		}
	}

	public static function important_upgrade_notice() {
		$get_notice = get_option('smack_uci_upgrade_notice');
		if($get_notice != 'off') {
			?>
			<div class="notice notice-error is-dismissible" onclick="dismiss_notices('upgrade_notice');">
				<p style="margin-top: 10px"><strong><?php echo esc_html__('Upgrade Notice:','wp-ultimate-csv-importer');?> </strong> <?php echo esc_html__('Download and replace the latest version of','wp-ultimate-csv-importer');?> <a href="https://wordpress.org/plugins/wp-ultimate-csv-importer/" target="_blank">WP Ultimate CSV Importer</a> <?php echo esc_html__('for 10x faster import performance with easy user interface.','wp-ultimate-csv-importer');?> </p>
			</div>
			<?php
		}
	}

	public static function important_cron_notice() {
		$get_notice = get_option('smack_uci_enable_cron_notice');
		if($get_notice != 'off' && isset($_REQUEST['page']) && $_REQUEST['page'] == 'sm-uci-import') {
				?>
				<div class="notice notice-error wc-connect is-dismissible" onclick="dismiss_notices('enable_cron_notice');" >
				<p style="margin-top: 10px">
				<strong><?php echo esc_html__( 'Notice:', 'wp-ultimate-csv-importer' ); ?> </strong> <?php echo esc_html__( 'To populate Featured images, Please make sure that CRON is enabled in your server. ', 'wp-ultimate-csv-importer' ); ?></p>
				</div>
				<?php
				if(function_exists( 'curl_version' ) == null || function_exists( 'curl_version' ) == '' && isset($_REQUEST['page']) && $_REQUEST['page'] == 'sm-uci-import') { ?>
						<div class="notice notice-error">
								<p style="margin-top: 10px;">
										<strong><?php echo esc_html__( 'Notice:', 'wp-ultimate-csv-importer' ); ?> </strong> <?php echo esc_html__( 'Please install CURL & enable it in your server. ', 'wp-ultimate-csv-importer' ); ?>
								</p>
						</div>
				<?php }
		}
	}

	public static function wp_ultimate_csv_importer_notice() {
		$get_notice = get_option('smack_uci_rating_notice');
		$smack_uci_pages = array('sm-uci-import', 'sm-uci-dashboard', 'sm-uci-managers', 'sm-uci-export', 'sm-uci-settings', 'sm-uci-support');
		if($get_notice != 'off' && isset($_REQUEST['page']) && in_array($_REQUEST['page'], $smack_uci_pages)) {
				?>
				<div class='notice Updated uci-message wc-connect is-dismissible' onclick="dismiss_notices('rating_notice');">
				<p><?php echo esc_html__("If you love WP Ultimate CSV Importer show us you care with a 5-star review on","wp-ultimate-csv-importer")?> <a href='https://wordpress.org/support/plugin/wp-ultimate-csv-importer/reviews/?rate=5#new-post' target='_blank'><?php echo esc_html__('wordpress.org!','wp-ultimate-csv-importer') ?></a></p>
				</p></div>
				<?php
		}
	} 
}
