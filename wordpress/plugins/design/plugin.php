<?php
/**
 * Plugin Name: Design
 * Plugin URI: https://wpdesignhub.com/pro/?utm_source=wp-org&utm_medium=plugin-homepage&utm_campaign=wp-org
 * Description: Design library for new WordPress editor.
 * Author: wpdesignhub
 * Author URI: https://wpdesignhub.com/?utm_source=plugin-ui&utm_medium=plugins-list&utm_campaign=wp-admin
 * Version: 0.1.3
 * License: GPL2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package WPDSGN
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * `wp-blocks`: includes block type registration and related functions.
 *
 * @since 1.0.0
 */
function dsgn_block_assets() {
	// Styles.
	wp_enqueue_style(
		'dsgn-style-css', // Handle.
		plugins_url( 'dist/dsgn.style.build.css', __FILE__ ), // Block style CSS.
		array( 'wp-block-library' ), // Dependency to include the CSS after it.
		filemtime( plugin_dir_path( __FILE__ ) . 'dist/dsgn.style.build.css' ) // Version: filemtime — Gets file modification time.
	);
} // End function design_cgb_block_assets().

// Hook: Frontend assets.
add_action( 'enqueue_block_assets', 'dsgn_block_assets' );

/**
 * Enqueue Gutenberg block assets for backend editor.
 *
 * `wp-blocks`: includes block type registration and related functions.
 * `wp-element`: includes the WordPress Element abstraction for describing the structure of your blocks.
 * `wp-i18n`: To internationalize the block's text.
 * `wp-editor`:
 * `wp-block-editor`:
 * https://github.com/WordPress/gutenberg/blob/1df302121f0ab7df8611d01e28ad41db25f40d52/lib/packages-dependencies.php#L36
 *
 * @since 1.0.0
 */
function dsgn_editor_assets() {
	// Scripts.
	wp_enqueue_script(
		'dsgn-editor-js', // Handle.
		plugins_url( '/dist/dsgn.build.js', __FILE__ ), // Main JS file.
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wp-edit-post', 'lodash' ), // Dependencies, defined above.
		filemtime( plugin_dir_path( __FILE__ ) . 'dist/dsgn.build.js' ), // Version: filemtime — Gets file modification time.
		true // Enqueue the script in the footer.
	);

	$uploads = wp_upload_dir();

	wp_localize_script(
		'dsgn-editor-js',
		'dsgnjs',
		array(
			'nonce' => wp_create_nonce( 'dsgn-ajax_' . get_current_user_id() ),
			'designLibrary' => get_option( 'dsgn_design_library', array() ),
			'uploadsUrl' => $uploads['baseurl'] . '/design-library',
			'pluginUrl' => plugin_dir_url( __FILE__ ),
			'license' => get_option( 'dsgn_license', array() ),
		)
	);

	// Styles.
	wp_enqueue_style(
		'dsgn-editor-css', // Handle.
		plugins_url( 'dist/dsgn.editor.build.css', __FILE__ ), // Block editor CSS.
		array( 'wp-edit-blocks' ), // Dependency to include the CSS after it.
		filemtime( plugin_dir_path( __FILE__ ) . 'dist/dsgn.editor.build.css' ) // Version: filemtime — Gets file modification time.
	);
} // End function dsgn_editor_assets().

// Hook: Editor assets.
add_action( 'enqueue_block_editor_assets', 'dsgn_editor_assets' );

/**
 * Launch the plugin.
 *
 * @return void
 */
if ( ! function_exists( 'dsgn_plugin_launch' ) ) {
	/**
	 * Plugin bootstrap process.
	 *
	 * @return void
	 */
	function dsgn_plugin_launch() {
		$abspath = wp_normalize_path( __DIR__ );
		require_once $abspath . '/lib/class-dsgn-core.php';
		$dsgn_core = new Dsgn_Core;

	} add_action( 'plugins_loaded', 'dsgn_plugin_launch', 10 );

	// @todo: else... show message that two copies of the plugin can't be active at the same time.
}

