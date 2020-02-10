
<?php
/**
 * Plugin Name:     columns-extend
 * Plugin URI:      https://sojuz.team
 * Description:     Allows to populate posts with arbitrary JSON data sources.
 * Author:          columns-extend (SOJUZ Team)
 * Author URI:      https://columns-extend.it
 * Text Domain:     columns-extend
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         columns-extend
 */
/**
 * Enqueue block JavaScript and CSS for the editor
 */
function my_columns_extend_scripts() {
	
    // Enqueue block editor JS
    wp_enqueue_script(
        'columns-extend-js',
        plugins_url( '/columns-extend.js', __FILE__ ),
        [ 'wp-blocks', 'wp-element', 'wp-components', 'wp-i18n' ],
        // filemtime( plugin_dir_path( __FILE__ ) . 'blocks/custom-block/index.js' )	
    );

}

// Hook the enqueue functions into the editor
add_action( 'enqueue_block_editor_assets', 'my_columns_extend_scripts' );
