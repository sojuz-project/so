<?php
/**
 * Zero: Customizer
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function zero_customize_register( $wp_customize ) {
	$wp_customize->add_setting( 'header_color' , array(
		'default' => '#E93558',
		'sanitize_callback' => 'sanitize_hex_color',
	) );

	$wp_customize->add_control(new WP_Customize_Color_Control( 
		$wp_customize, 
		'header_color', 
		array(
			'label'      => __( 'Header Color', 'zero' ),
			'section'    => 'title_tagline',
		) ) );




	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '.site-title a',
				'render_callback' => 'zero_customize_partial_blogname',
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'blogdescription',
			array(
				'selector'        => '.site-description',
				'render_callback' => 'zero_customize_partial_blogdescription',
			)
		);
	}
}
add_action( 'customize_register', 'zero_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function zero_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function zero_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Bind JS handlers to instantly live-preview changes.
 */
function zero_customize_preview_js() {
	wp_enqueue_script( 'zero-customize-preview', get_theme_file_uri( '/js/customize-preview.js' ), array( 'customize-preview' ), '20181231', true );
}
add_action( 'customize_preview_init', 'zero_customize_preview_js' );

/**
 * Load dynamic logic for the customizer controls area.
 */
function zero_panels_js() {
	wp_enqueue_script( 'zero-customize-controls', get_theme_file_uri( '/js/customize-controls.js' ), array(), '20181231', true );
}
add_action( 'customize_controls_enqueue_scripts', 'zero_panels_js' );

/**
 * Sanitize custom color choice.
 *
 * @param string $choice Whether image filter is active.
 *
 * @return string
 */
function zero_sanitize_color_option( $choice ) {
	$valid = array(
		'default',
		'custom',
	);

	if ( in_array( $choice, $valid, true ) ) {
		return $choice;
	}

	return 'default';
}
