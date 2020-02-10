<?php
// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}

class Dsgn_CSS_Manager {
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
		$this->enqueue_css();

		add_action( 'wp_ajax_dsgn_ajax_save_css', array( $this, 'ajax_save_css' ) );
		add_action( 'save_post', array( $this, 'on_save' ) );
		add_action( 'wp_creating_autosave', array( $this, 'on_autosave' ) );
	}

	private function enqueue_css() {
		add_action( 'enqueue_block_assets', array( $this, 'output_css' ) );
	}

	public function on_autosave( $revision_data ) {
		// $this->is_autosave = true;
		$this->write_css_file( $revision_data['post_parent'], $revision_data['ID'] );
	}

	public function on_save( $post_id ) {
		// On autosave (preview) we need to write css file only once
		// for 'wp_creating_autosave' action ignoring 'save_post' action.
		// Otherwise we will be getting css for final post not for the review.
		if ( ! ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) ) {
			$this->write_css_file( $post_id );
		}
	}

	/* ---------------------------------------------------- */
	/* CSS library for Stylist								*/
	/* ---------------------------------------------------- */
	public function output_css() {
		$post = get_post( null, ARRAY_A );

		// Check if the page has OUR custom classes.
		if ( ! stristr( $post['post_content'] , 'dsgn__' ) ) {
			return;
		}

		$uploads = wp_upload_dir();

		$directory_abs = $uploads['basedir'] . '/design-library';
		$filename_abs  = $directory_abs . '/styles/post-' . $post['ID'] . '.css';

		$directory_url = $uploads['baseurl'] . '/design-library';
		$filename_url  = $directory_url . '/styles/post-' . $post['ID'] . '.css';

		// Request CSS file only if it exists.
		if ( file_exists( $filename_abs ) ) {
			// Custom CSS Href.
			$href = add_query_arg( 'revision', $post['post_modified_gmt'], $filename_url, __FILE__ );
			// Add.
			wp_enqueue_style( 'dsgn-css', $href );
		}
	}

	/*-------------------------------------------------------*/
	/*	Ajax Save Callback – SAVE button clicked in Stylist	 */
	/*-------------------------------------------------------*/
	public function ajax_save_css() {
		if ( $this->core->has_access() ) {
			// Revisions.
			// $current_revision = get_option('dsgn_current_revision', 0);
			// Update revision.
			// update_option( 'dsgn_current_revision', $current_revision + 1 );

			$css = wp_strip_all_tags( $_POST['dsgn_data'] );
			if ( 'undefined' === $css ) {
				$css = '';
			}

			$dsgn_id = wp_strip_all_tags( $_POST['dsgn_id'] );
			if ( 'undefined' === $dsgn_id ) {
				$dsgn_id = '';
			}

			$post_id = wp_strip_all_tags( $_POST['post_id'] );
			if ( 'undefined' === $post_id ) {
				$post_id = '';
			}

			/* $page_code =  $_POST['post_content'];

			$id   = '';
			$type = '';

			if ( isset( $_POST['dsgn_id'] ) ) {
				$id = intval( $_POST['dsgn_id'] );
			}

			if ( isset( $_POST['dsgn_stype'] ) ) {
				$type = trim( strip_tags( $_POST['dsgn_stype'] ) );
				if ( count( explode( '#', $type ) ) == 2 ) {
					$type = explode( '#', $type );
					$type = $type[0];
				}
			}

			if ($id === 'undefined') {
				$id = '';
			}
			if ($type === 'undefined') {
				$type = '';
			} */

			// CSS Data.

			$dsgn_css = get_option( 'dsgn_css', array() );

			if ( ! empty( $css ) && ! empty( $dsgn_id ) ) {
				$dsgn_css[ $dsgn_id ] = $css;
				update_option( 'dsgn_css', $dsgn_css );
			}
		} // End if().

		wp_die( 1 );
	}

	private function compose_css( $post_id ) {
		$post = get_post( $post_id, ARRAY_A );

		// Check if the page has OUR custom classes.
		if ( ! stristr( $post['post_content'] , 'dsgn__' ) ) {
			return;
		}

		$dsgn_css = get_option( 'dsgn_css', array() );
		$composed_css = '';

		// Extract all the custom classes and see
		// if we have styles in the database for these.
		$class_names = array();
		preg_match_all(
			'/"className":"[^"]*(dsgn__[^\s"]+)/m',
			$post['post_content'],
			$matches,
			PREG_SET_ORDER, 0
		);

		$processed_ids = array();

		foreach ( $matches as $match ) {
			if ( ! empty( $match[1] ) ) {
				$css_class = trim( $match[1] );
				$dsgn_id = str_replace( 'dsgn__', '', $css_class );

				if ( ! empty( $dsgn_css[ $dsgn_id ] ) && ! in_array( $dsgn_id, $processed_ids ) ) {
					$composed_css .= "\n\n" . $dsgn_css[ $dsgn_id ];
					$processed_ids[] = $dsgn_id;
				}
			}
		}

		return $composed_css;
	}

	/*-------------------------------------------------------*/
	/*	Creating an Custom.css file (Static)				 */
	/*-------------------------------------------------------*/
	public function write_css_file( $post_id, $revision_id = null ) {
		$uploads = wp_upload_dir();

		$directory   = $uploads['basedir'] . '/design-library/styles';
		$filename  = $directory . '/post-' . $post_id . '.css';

		// Delete old version for the file if exists.
		if ( file_exists( $filename ) ) {
			wp_delete_file( $filename );
		}

		/**
		 * Initialize the WP_Filesystem
		 */
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		// get css data form database
		$post_id_for_css = $post_id ;

		if ( null !== $revision_id ) {
			$post_id_for_css = $revision_id;
		}

		$css = $this->compose_css( $post_id_for_css );

		if ( wp_mkdir_p( $directory ) && ! $wp_filesystem->put_contents( $filename, $css, FS_CHMOD_FILE ) ) {
			// If the directory is not writable, try inline css fallback.
			error_log( 'Design Library Plugin: Error writing CSS file in uploads dir.' );
		}
	}
}
