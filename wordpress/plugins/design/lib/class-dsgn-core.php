<?php
// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}


/**
 * Core Class
 */
class Dsgn_Core {

	/**
	 * Was this class ever instantiated?
	 *
	 * @var bool
	 */
	private static $initiated = false;

	/**
	 * Current version of the plugin.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * If current user allowed to use Stylist.
	 *
	 * @var bool
	 */
	private $canuse = false;

	/**
	 * If is in editing mode (editor is active).
	 *
	 * @var bool
	 */
	private $editing = false;

	/**
	 * Is current session initiated from the inframe view?
	 *
	 * @var bool
	 */
	private $inframe = false;

	/**
	 * Is current session initiated from the preview tab?
	 *
	 * @var bool
	 */
	private $preview = false;

	/**
	 * Define the plugin absolute path.
	 *
	 * @var string
	 */
	private $abspath;

	/**
	 * URL to the editing screen.
	 *
	 * @var string
	 */
	private $editing_screen_uri;

	/**
	 * Do all the required job on core object creation.
	 */
	function __construct() {
		// Actions that needs to be lunched only once.
		if ( ! self::$initiated ) {
			$this->set_abspath();
			$this->set_version();
			$this->set_permissions();
			$this->set_slug();
			$this->require_files();
			$this->load_design_manager();
			$this->load_css_manager();
			$this->load_license_manager();
			/*
			$this->load_code_manager();

			$this->editing_mode();
			$this->non_editing_mode(); */

			self::$initiated = true;
		}
	}

	/**
	 * Set $abspath class property value.
	 */
	private function set_abspath() {
		$path_to_current_folder = wp_normalize_path( __DIR__ ) ;
		// Fixes the issue with path on Windows machines.
		$this->abspath = str_replace( '/lib', '', $path_to_current_folder );
	}

	/**
	 * Set $version class property value.
	 */
	private function set_version() {

		$default_headers = array(
			'Name' => 'Plugin Name',
			'PluginURI' => 'Plugin URI',
			'Version' => 'Version',
			'Description' => 'Description',
			'Author' => 'Author',
			'AuthorURI' => 'Author URI',
			'TextDomain' => 'Text Domain',
			'DomainPath' => 'Domain Path',
			'Network' => 'Network',
			// Site Wide Only is deprecated in favor of Network.
			'_sitewide' => 'Site Wide Only',
		);

		$plugin_data = get_file_data(  $this->abspath . '/plugin.php', $default_headers, 'plugin' );
		$this->version = $plugin_data[ 'Version' ];
	}

	/**
	 * Determine if current user has enough permissions to use the plugin.
	 */
	private function set_permissions() {
		if ( is_user_logged_in() && current_user_can( 'edit_pages' ) && current_user_can( 'upload_files' ) ) {
			$this->canuse = true;
		}
	}

	/**
	 * Plugin slug and translation functionality.
	 */
	private function set_slug() {
		// Get Translation Text Domain.
		load_plugin_textdomain('design', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}

	/**
	 * Required actions on plugin bootstrap.
	 *
	 * @return void
	 */
	public function require_files() {
		require_once $this->abspath . '/lib/class-dsgn-css-manager.php';

		// Load the next files only if user can edit pages.
		// To not overuse resources for regular visitors.
		if ( $this->has_access() ) {
			require_once $this->abspath . '/lib/class-dsgn-design-manager.php';
			require_once $this->abspath . '/lib/class-dsgn-license-manager.php';
		}
	}

	/**
	 * Initiate Dsgn_Design_Manager.
	 */
	private function load_design_manager() {
		// If is in Editing mode.
		if ( $this->has_access() && is_admin() ) {
			$design_manager = new Dsgn_Design_Manager( $this );
		}
	}

	/**
	 * Initiate Dsgn_License_Manager.
	 */
	private function load_license_manager() {
		// If is in Editing mode.
		if ( $this->has_access() && is_admin() ) {
			$license_manager = new Dsgn_License_Manager( $this );
		}
	}

	/**
	 * Initiate Stylist_Class_Manager.
	 */
	private function load_css_manager() {
		$css_manager = new Dsgn_CSS_Manager( $this );
	}

	/**
	 * Initiate Dsgn_Design_Manager.
	 */
	private function load_code_manager() {
		// $code_manager = new Dsgn_Design_Manager( $this );
	}


	// Getters ----------------------------------------------------------

	/**
	 * Get current version of the plugin.
	 * Use like this: Stylist_Core::get_version()
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Return true if current user has enough permissions to use Stylist.
	 * Use like this: Stylist_Core::has_access()
	 */
	public function has_access() {
		return $this->canuse;
	}
}
