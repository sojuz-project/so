<?php
/**
 * Plugin Name:     DataSources
 * Plugin URI:      https://sojuz.team
 * Description:     Allows to populate posts with arbitrary JSON data sources.
 * Author:          Maciek &lt;minimal2&gt; Dmowski (SOJUZ Team)
 * Author URI:      https://dmowsk.it
 * Text Domain:     dataSources
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         DataSources
 */

function sidebar_plugin_register() {
    wp_register_script(
        'plugin-sidebar-js',
        plugins_url( 'build/index.js', __FILE__ ),
        array( 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data' )
    );

    wp_register_style(
        'plugin-sidebar-css',
        plugins_url( 'src/sidebar.css', __FILE__ )
    );

    register_post_meta( '', 'dataSources', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
    ) );

    // DEPRECATED!
    register_post_meta( '', 'dataGqls', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
    ) );

    register_post_meta( '', 'dataQueries', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
    ) );

    // DEPRECATED!
    register_post_meta( '', 'dataKeys', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
    ) );
}
add_action( 'init', 'sidebar_plugin_register' );

function sidebar_plugin_script_enqueue() {
    wp_enqueue_script( 'plugin-sidebar-js' );
    wp_enqueue_style( 'plugin-sidebar-css' );
}
add_action( 'enqueue_block_editor_assets', 'sidebar_plugin_script_enqueue' );
