<?php
// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

class Dsgn_License_Manager {
	/**
	 * Reference to the main class.
	 *
	 * @var object
	 */
	private $core;

	/**
	 * Do all the required job on core object creation.
	 *
	 * $core @param object – Reference to the main class.
	 */
	function __construct( $core ) {
		$this->core = $core;

		add_action( 'wp_ajax_dsgn_update_license', array( $this, 'ajax_update_lisence' ) );
	}

	public function ajax_update_lisence() {

		// Check nonce and User rights before proceeding.
		if ( ! $this->core->has_access() || ! check_ajax_referer( 'dsgn-ajax_' . get_current_user_id() ) ) {
			wp_die( 'Save option error: User has no permissions to update license' );
		}

		if ( isset( $_REQUEST['licenseData'] ) ) {

			// $license_data = wp_unslash ( $_REQUEST['licenseData'] );
			$license_data = json_decode( wp_unslash ( $_REQUEST['licenseData'] ), true );
			// vovaphperror( $license_data, '$license_data' );
			// $license_data = json_encode( $_REQUEST['licenseData'] );
			update_option( 'dsgn_license', $license_data );
		}

		wp_die( 1 );
	}
}
