<?php
/**
 * Zero theme functions and definitions
 *
 * @link https://...
 *
 * @package WordPress
 * @subpackage Zero
 * @since 1.0.0
 */

/**
 * Zero only works in WordPress 4.7 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '4.7', '<' ) ) {
  print('update WP version up to 4.7');
	die();
}

if ( ! function_exists( 'zero_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function zero_setup() {
		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 1568, 9999 );
		add_image_size( 'halflarge', 800, 9999 );

		// This theme uses wp_nav_menu() in two locations.
		register_nav_menus(
			array(
				'main-menu' => __( 'Primary', 'zero' ),
				'footer' => __( 'Footer Menu', 'zero' ),
				'social' => __( 'Social Links Menu', 'zero' ),
			)
		);

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 190,
				'width'       => 190,
				'flex-width'  => false,
				'flex-height' => false,
			)
		);


		// Enqueue editor styles.
		add_editor_style( 'style-editor.css' );

		// Add custom editor font sizes.
		add_theme_support(
			'editor-font-sizes',
			array(
				array(
					'name'      => __( 'Small', 'zero' ),
					'shortName' => __( 'S', 'zero' ),
					'size'      => 19.5,
					'slug'      => 'small',
				),
				array(
					'name'      => __( 'Normal', 'zero' ),
					'shortName' => __( 'M', 'zero' ),
					'size'      => 22,
					'slug'      => 'normal',
				),
				array(
					'name'      => __( 'Large', 'zero' ),
					'shortName' => __( 'L', 'zero' ),
					'size'      => 36.5,
					'slug'      => 'large',
				),
				array(
					'name'      => __( 'Huge', 'zero' ),
					'shortName' => __( 'XL', 'zero' ),
					'size'      => 49.5,
					'slug'      => 'huge',
				),
			)
		);
	}
endif;
add_action( 'after_setup_theme', 'zero_setup' );

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Remove Certain Default Blocks from the Inserter.
 */
add_filter( 'allowed_block_types', 'misha_allowed_block_types' );
 
function misha_allowed_block_types( $allowed_blocks ) {
 
	return array(
		'sojuz/block-list-section',
		'sojuz/block-content-section',
		'cgb/block-content-section'
	);
 
}

add_filter( 'image_size_names_choose', 'my_custom_sizes' );
 
function my_custom_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'halflarge' => __( 'Half large thumbnail', 'zero' ),
    ) );
}