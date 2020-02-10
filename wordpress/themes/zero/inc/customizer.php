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
		'default' => '',
		'sanitize_callback' => 'sanitize_hex_color',
	) );

	$wp_customize->add_control(new WP_Customize_Color_Control( 
		$wp_customize, 
		'header_color', 
		array(
			'label'      => __( 'Header Color', 'zero' ),
			'section'    => 'title_tagline',
		) ) );

		// -----------------------------------------------------------------------
			$wp_customize->add_setting( 'footer_template' , array(
			'default'           => '',
			'sanitize_callback' => 'absint'
		)  );
		// https://codex.wordpress.org/Class_Reference/WP_Customize_Control
		$wp_customize->add_control(
			'footer_template', 
			array(
				'label'    => __( 'Footer template', 'zero' ),
				'section'  => 'title_tagline',
				'settings' => 'footer_template',
				'type'     => 'dropdown-pages',
			)
		);
	
			// -----------------------------------------------------------------------
		$wp_customize->add_setting( 'header_template' , array(
			'default'           => '',
			'sanitize_callback' => 'absint'
		)  );
		// https://codex.wordpress.org/Class_Reference/WP_Customize_Control
		$wp_customize->add_control(
			'header_template', 
			array(
				'label'    => __( 'Header template', 'zero' ),
				'section'  => 'title_tagline',
				'settings' => 'header_template',
				'type'     => 'dropdown-pages',
			)
		);
	
		// -----------------------------------------------------------------------
		$wp_customize->add_setting( 'elements_align' , array(
			'align-default' => 'Align default',
		) );
		// https://codex.wordpress.org/Class_Reference/WP_Customize_Control
		$wp_customize->add_control(
			'elements_align', 
			array(
				'label'    => __( 'Align header elements', 'zero' ),
				'section'  => 'title_tagline',
				'settings' => 'elements_align',
				'type'     => 'radio',
				'choices'  => array(
					'align-default'  => __('Align default', 'zero'),
					'align-full' => __('Align full', 'zero'),
					'align-wide' => __('Align wide', 'zero'),
				),
			)
		);
		// -----------------------------------------------------------------------
		
		// -----------------------------------------------------------------------
		$wp_customize->add_setting( 'display_mode' , array(
			'align-default' => 'Align default',
		) );
		// https://codex.wordpress.org/Class_Reference/WP_Customize_Control
		$wp_customize->add_control(
			'display_mode', 
			array(
				'label'    => __( 'Header display mode', 'zero' ),
				'section'  => 'title_tagline',
				'settings' => 'display_mode',
				'type'     => 'radio',
				'choices'  => array(
					'absolute-mode'  => __('Absolute', 'zero'),
					'sticky-mode' => __('Sticky', 'zero'),
					'fixed-mode' => __('Fixed', 'zero'),
				),
			)
		);
		// -----------------------------------------------------------------------
		$wp_customize->add_setting('background_image', array(
			'default' => '',
			'type' => 'theme_mod',
			'capability' => 'edit_theme_options',
		));
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'image_control_one', array(
			'label' => __( 'Background image', 'zero' ),
			'section' => 'title_tagline',
			'settings' => 'background_image',
			))
		);

		// -----------------------------------------------------------------------
		$wp_customize->add_setting( 'background_class' , array(
			'cover-fixed' => 'Contain top',
		) );
		// https://codex.wordpress.org/Class_Reference/WP_Customize_Control
		$wp_customize->add_control(
			'display_mode', 
			array(
				'label'    => __( 'Background class', 'zero' ),
				'section'  => 'title_tagline',
				'settings' => 'background_class',
				'type'     => 'radio',
				'choices'  => array(
					'contain-top'  => __('Contain top', 'zero'),
					'contain-bottom' => __('Contain bottom', 'zero'),
					'cover-fixed' => __('Cover fixed', 'zero'),
					'pattern' => __('Pattern tiles', 'zero'),
				),
			)
		);

		// --

		
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

function action_customize_save_after( $array ) { 
	set_theme_mod('header_template_slug', get_post_field( 'post_name', get_theme_mod('header_template') ));
	set_theme_mod('footer_template_slug', get_post_field( 'post_name', get_theme_mod('footer_template') ));
	die();
}; 
         
// add the action 
add_action( 'customize_save_after', 'action_customize_save_after', 10, 1 ); 
