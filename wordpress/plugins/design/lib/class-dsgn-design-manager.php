<?php
// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

class Dsgn_Design_Manager {
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

		add_action( 'wp_ajax_dsgn_design_import', array( $this, 'ajax_import_design' ) );
		add_action( 'wp_ajax_dsgn_thumbnails_import', array( $this, 'ajax_import_thumbnails' ) );
		add_action( 'wp_ajax_dsgn_delete_library', array( $this, 'ajax_delete_library' ) );
		add_action( 'wp_ajax_dsgn_get_library', array( $this, 'ajax_get_library' ) );
		add_action( 'wp_ajax_dsgn_save_option', array( $this, 'ajax_save_option' ) );
	}

	public function ajax_save_option() {

		// Check nonce and User rights before proceeding.
		if ( ! $this->core->has_access() || ! check_ajax_referer( 'dsgn-ajax_' . get_current_user_id() ) ) {
			wp_die( 'Save option error: User has no permissions to import designs' );
		}

		if ( isset( $_REQUEST['id'] ) ) {
			$id = esc_attr( $_REQUEST['id'] );

			if ( isset( $_REQUEST['value'] ) ) {
				$value = esc_attr( $_REQUEST['value'] );

				switch ( $value ) {
					case 'basic':
						break;
					/*
					case label:
						break; */

					default:
						// Basic set.
				}
			}
		}
	}

	public function ajax_get_library() {

		// Check nonce and User rights before proceeding.
		if ( ! $this->core->has_access() || ! check_ajax_referer( 'dsgn-ajax_' . get_current_user_id() ) ) {
			wp_die( 'Get designs function error: User has no permissions to import designs' );
		}

		// if ( isset( $_REQUEST['id'] ) ) {
			// $id = esc_attr( $_REQUEST['id'] );
			$data = get_option( 'dsgn_design_library', array() );
		// }

		// return json_encode( $data );
		wp_die( wp_json_encode( $data ) );
	}

	public function ajax_delete_library() {
		// Check nonce and User rights before proceeding.
		if ( ! $this->core->has_access() || ! check_ajax_referer( 'dsgn-ajax_' . get_current_user_id() ) ) {
			wp_die( 'Designs import process error: User has no permissions to import designs' );
		}

		if ( ! delete_option( 'dsgn_design_library' ) ) {
			wp_die( 'Can\'t delete design library.' );
		}

		wp_die( 1 );
	}

	/*-------------------------------------------------------*/
	/*	Ajax Callback	 									*/
	/*-------------------------------------------------------*/
	// @todo Rewrite using REST API
	// use apiFetch in js for that https://github.com/WordPress/gutenberg/blob/fcd0e871f92345fc1262c066df6fccd4bcd00fb0/packages/api-fetch/README.md
	public function ajax_import_design() {

		// Check nonce and User rights before proceeding.
		if ( ! $this->core->has_access() || ! check_ajax_referer( 'dsgn-ajax_' . get_current_user_id() ) ) {
			wp_die( 'Designs import process error: User has no permissions to import designs' );
		}

		$set_to_import = '';
		$url_to_import = '';

		if ( isset( $_REQUEST['design_set'] ) ) {
			$set_to_import = esc_attr( $_REQUEST['design_set'] );

			switch ( $set_to_import ) {
				case 'premium':
					$url_to_import = 'https://wpdesignhub.com/designs/premium.json';
					break;

				default:
					// Basic set.
					$url_to_import = 'https://wpdesignhub.com/designs/basic.json';
			}
		}

		$response = $this->import_from_url( $url_to_import );
		$return = array();
		if ( $response['data'] ) {
			$db_save_err = $this->save_in_database( $response['data'] );
			error_log ( '$db_save_err: ' . $db_save_err );
			if ( $db_save_err ) {
				$return['status'] = '1';
				$return['data'] = $response['data'];
			} else {
				// Can't save in database. Return error message.
				$return['status'] = '0';
				$return['data'] = $db_save_err;
			}
		} else {
			$return['status'] = '0';
			$return['data'] = $response['message'];
		}

		if ( function_exists( 'vovaphperror' ) ) {
			vovaphperror( $return, '$return' );
		}

		// Save locally all the thumbnails for the imported design set.
		/* if ( 1 === $return ) {
			$return = $this->import_thumbnails( $response['data'] );
		} */

		wp_die( json_encode( $return ) );
	}

	public function ajax_import_thumbnails( $data ) {

		// Check nonce and User rights before proceeding.
		if ( ! $this->core->has_access() || ! check_ajax_referer( 'dsgn-ajax_' . get_current_user_id() ) ) {
			wp_die( 'Designs import process error: User has no permissions to import designs' );
		}

		$set_to_import = '';
		$url_to_import = '';

		if ( isset( $_REQUEST['data_to_import'] ) ) {
			$data_to_import = json_decode( wp_unslash ( $_REQUEST['data_to_import'] ), true );

			$return = $this->import_thumbnails( $data_to_import );
		}


		wp_die( json_encode( $return ) );
	}

	public function import_thumbnails( $data = '' ) {
		// $data_decoded = json_decode( $data, true );
		$data_decoded = $data;
		$base_url = '';
		$return = '';

		foreach ( $data_decoded as $design_pack ) {

			if ( empty( $design_pack ) ) {
				return 'Designs import process error: damaged data provided in the design set (empy pack).';
			}

			if ( ! array_key_exists( 'base_url', $design_pack ) ) {
				return 'Designs import process error: wrong base_url provided in the design set.';
			}

			if ( ! array_key_exists( 'designs', $design_pack ) ) {
				return 'Designs import process error: damaged data provided in the design set (no designs set).';
			}

			$base_url = $design_pack['base_url'];

			foreach ( $design_pack['designs'] as $design ) {
				if ( array_key_exists( 'name', $design ) ) {
					$thumbnail_url = $base_url . $design['name'] . 'thumbnail.png';

					$response = $this->import_from_url( $thumbnail_url );

					if ( $response['data'] ) {
						$save_result = $this->save_in_uploads( $response['data'], $design['name'] . 'thumbnail.png' );

						if ( 1 === $save_result && ( 1 === $return || ! $return ) ) {
							$return = $save_result;
						} else {
							$return .= ' | ' . $save_result;
						}
					} else {
						$return .= ' | ' . $response['message'];
					}
				}
			}
		} // End foreach().

		return $return;
	}

	public function import_from_url( $url_to_import = '' ) {

		$return = array(
			'data' => '',
		);

		$uploads = wp_upload_dir();
		$directory = apply_filters( 'dsgn_custom_import_dir', $uploads['basedir'] . '/design-library' );

		$url_to_import_parts = explode( '/', $url_to_import );
		$filename = $url_to_import_parts[ count( $url_to_import_parts ) - 1 ];
		$return['filename'] = $filename;

		if ( '' === $filename || 5 > strlen( $filename ) ) {
			$return['message'] = 'Designs import process error: wrong file name.';
		}

		$response = wp_remote_get( $url_to_import );

		if ( ! is_wp_error( $response ) ) {
			$response_code = wp_remote_retrieve_response_code( $response );

			if ( 200 !== $response_code ) {
				$return['message'] = '#39cd Designs import process error: can\'t get the remote file ' . $url_to_import . ' – responce code #' . $response_code;
			} else {
				$return['data'] = wp_remote_retrieve_body( $response );
			}
		} else {
			if ( stristr( $response->get_error_message(), 'cURL error 7' ) ) {
				$return['message'] = 'Can\'t connect to our remote server to download requested desings. Looks like your hosting provider is blocking connections created with cURL function. Please, ask your hosting company to unblock this functionality.';
			} else {
				$return['message'] = '#89wb Designs import process error: can\'t get the remote file – ' . esc_attr( $response->get_error_message() );
			}
		}

		return $return;
	}

	public function save_in_uploads( $data = '', $filename = '' ) {

		$uploads = wp_upload_dir();
		$directory = $uploads['basedir'] . '/design-library';

		if ( '' === $filename || 5 > strlen( $filename ) ) {
			return 'Designs import process error: wrong file name.';
		}

		if ( stristr( $filename, '/' ) ) {
			$filename_exploded = explode( '/', $filename );

			foreach ( $filename_exploded as $part ) {
				if ( $part ) {
					if ( stristr( $part, '.' ) ) {
						$filename = $part;
					} else {
						$directory .= '/' . $part;
					}
				}
			}
		}

		/**
		 * Initialize the WP_Filesystem
		 */
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		// Delete old version for the file if exists.
		if ( file_exists( $directory . '/' . $filename ) ) {
			if ( ! $wp_filesystem->delete( $directory . '/' . $filename ) ) {
				return( 'Designs import process error: Can\'t delete the files on the local server.' );
			}
		}

		// Create directory (if needed) and put data in a new file.
		if ( $data &&
			 wp_mkdir_p( $directory ) &&
			 ! $wp_filesystem->put_contents( $directory . '/' . $filename, $data, FS_CHMOD_FILE ) ) {
			// If the directory is not writable, or something else went wrong.
			return( 'Designs import process error: Can\'t write the files on the local server.' );
		}

		// Check if file created properly.
		if ( ! file_get_contents( $directory . '/' . $filename ) ) {
			// Handle the error
			return( 'Designs import process error: Saved file on the local server is damaged.' );
		}

		return( 1 );
	}

	public function save_in_database( $data = '' ) {
		$saved_data   = get_option( 'dsgn_design_library', array() );
		$data_to_save = json_decode( $data, true );
		$updated_data   = array_merge( (array) $saved_data, (array) $data_to_save );

		// If data changed (arrays aren't equal) go ahead and update database.
		if ( $saved_data !== $updated_data &&
			 ! update_option( 'dsgn_design_library', $updated_data, false ) ) {
			return 'Designs import process error: can\'t save option in the database.';
		}

		return 1;
	}
}
