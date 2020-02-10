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
		add_theme_support( 'woocommerce' );
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 1568, 9999 );
		// add_image_size( 'w1200', 1200, 9999 );
		add_image_size( 'w1100', 1100, 9999 );
		// add_image_size( 'w1000', 1000, 9999 );
		add_image_size( 'w900', 900, 9999 );
		// add_image_size( 'w800', 800, 9999 );
		add_image_size( 'w700', 700, 9999 );
		// add_image_size( 'w600', 600, 9999 );
		add_image_size( 'w500', 500, 9999 );
		// add_image_size( 'w400', 400, 9999 );
		add_image_size( 'w300', 300, 9999 );
		// add_image_size( 'w200', 200, 9999 );
		add_image_size( 'w100', 100, 9999 );


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
				'flex-width'  => true,
				'flex-height' => true,
			)
		);

		add_theme_support(
			'editor-color-palette',
			array(
				array(
					'name'  => __( 'Primary', 'zero' ),
					'slug'  => 'primary',
					'color'	=> '#0073a8',
				),
				array(
					'name'  => __( 'Secondary', 'zero' ),
					'slug'  => 'secondary',
					'color'	=> '#005075',
				),
				array(
					'name'  => __( 'Dark gray', 'zero' ),
					'slug'  => 'dark-gray',
					'color'	=> '#111111',
				),
				array(
					'name'  => __( 'Light gray', 'zero' ),
					'slug'  => 'light-gray',
					'color'	=> '#767676',
				),
				array(
					'name'  => __( 'White', 'zero' ),
					'slug'  => 'white',
					'color'	=> '#ffffff',
				)
			)
		);


		// Enqueue editor styles.
		add_editor_style( 'style-editor.css' );

		// Add custom editor font sizes.
		add_theme_support(
			'editor-font-sizes',
			array(
				array(
					'name'      => __( 'Extra Small', 'zero' ),
					'shortName' => __( 'S', 'zero' ),
					'size'      => 16.5,
					'slug'      => 'xsmall',
				),
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

add_filter( 'image_size_names_choose', 'my_custom_sizes' );

function my_custom_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'halflarge' => __( 'Half large thumbnail', 'zero' ),
    ) );
}

function allow_rest_regiser() {
    $users_controller = new WP_REST_Users_Controller();

    register_rest_route( 'wp/v2', '/users',
        array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array($users_controller, 'create_item'),
            'permission_callback' => function( $request ) {
				// For now only customers can register via REST
                $request->set_param('roles', array('customer'));
                return true;
            },
            'args'                => $users_controller->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
        )
    );

};
add_action( 'rest_api_init', 'allow_rest_regiser' );

function strip_backend_from_preview_link($link) {
	return str_replace("/backend", '', $link);
}
add_filter( 'preview_page_link', 'strip_backend_from_preview_link');

/**
 * Register support for Gutenberg wide images in your theme
 */
function mytheme_setup() {
  add_theme_support( 'align-wide' );
}
add_action( 'after_setup_theme', 'mytheme_setup' );

function prepare_menu_item_object( $menu_item ) {
	$menu_item->component = get_post_meta( $menu_item->ID, '_menu_item_component', true );
	$menu_item->component_attrs = str_replace('\\u0022', '"', get_post_meta( $menu_item->ID, '_menu_item_component_attrs', true ));
	return $menu_item;
}
add_filter( 'wp_setup_nav_menu_item', 'prepare_menu_item_object' );

include_once(__DIR__.'/nav_walker.php');
add_filter( 'wp_edit_nav_menu_walker', function () {return 'Walker_Nav_Menu_Edit_Custom';}, 10, 0 );

function update_custom_navitem_attrs( $menu_id, $menu_item_db_id, $args ) {
	// Get menuItem parent
	$parent = intVal($args['menu-item-parent-id']);
	$object = $args['menu-item-object-id'];
	// Check if element is properly sent
	if ( is_array( $_REQUEST['menu-item-component']) ) {
			$component_value = $_REQUEST['menu-item-component'][$menu_item_db_id];
			if ('' == $component_value) $component_value = 'NavItem';
			// if ($parent) update_post_meta($parent, '_menu_item_component' )
			$args['menu-item-tagName'] = $component_value;
			update_post_meta( $menu_item_db_id, '_menu_item_component', $component_value );
	}
		if ( is_array( $_REQUEST['menu-item-component-attrs']) ) {
			$component_attrs_value = str_replace('"', '\\u0022', $_REQUEST['menu-item-component-attrs'][$menu_item_db_id]);
			$args['menu-item-component-attrs'] = $component_attrs_value;
			update_post_meta( $menu_item_db_id, '_menu_item_component_attrs', $component_attrs_value );
	}
	// Setup initial content
	$theContent = "<!-- wp:cgb/block-wrapper %s -->\n@children\n<!-- /wp:cgb/block-wrapper -->";
	// Unset unwanted attrs
	$toUnset = [
		'menu-item-object-id',
		'menu-item-db-id',
		'menu-item-parent-id',
		'menu-item-position',
	];
	foreach($toUnset as $unset) unset($args[$unset]);
	// Strip "menu-item-" prefixes
	foreach ($args as $arg => $value) {
		unset($args[$arg]);
		$arg = str_replace('menu-item-', '', $arg);
		if ('' == $value) {
			switch ($arg) {
				case 'title':
					$value = get_the_title($object);
					break;
				case 'url':
					$value = str_replace(get_bloginfo('url'), '', get_permalink($object));
					break;
			}
		}
		$args[$arg] = $value;
	}
	// $args['tagName'] = ($parent)? 'Dropdown' : 'NavItem';
	// Prepare block attrs
	$encoded = json_encode($args, JSON_UNESCAPED_SLASHES);
	// Get menuItem content
	$itemContent = sprintf($theContent, $encoded, $menu_item_db_id);
	update_post_meta($menu_item_db_id, '_menu_blocks', $itemContent);
	wp_update_post([
		'ID' => $menu_item_db_id,
	// 	'post_content' => $itemContent,
		'post_parent' => $parent,
	]);
}
add_action( 'wp_update_nav_menu_item', 'update_custom_navitem_attrs', 10, 3 );


function myImageOutput( $attributes, $content ) {
    // $content contains e.g.
    // <!-- wp:image {"id":123,"linkDestination":"custom"} -->
    // <figure class="wp-block-image"><a href="https://www.example.com"><img src="path/to/my/image.jpg" alt="Alternative text here" class="wp-image-123"/></a><figcaption>Caption goes here</figcaption></figure>
    // <!-- /wp:image -->

    // prepare array for all info. Note: alignment and customized
    // size are ignored here since it was not required in this case
    $info = [
        'title' => '',
        'imagUrl' => '',
        'blank' => FALSE,
        'url' => '',
        'caption' => '',
    ];

    // Fortunately, the attachment id is saved in $attributes, so
    // we can get the image's url
    $infos[ 'imageUrl' ] = wp_get_attachment_image_src( $attributes[ 'id' ], 'your-size' )[ 0 ];

    // we get the remaining info by loading the html via DOMDocument
    libxml_use_internal_errors( TRUE );
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = FALSE;
    $dom->loadHtml( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) );

    // get the figure element
    $figure = $dom->getElementsByTagName( 'figure' )[ 0 ];

    // alternatively, get the image title or description etc.
    // by querying it from the database
    $infos[ 'title' ] = $figure->getElementsByTagName( 'img' )[ 0 ]->getAttribute( 'alt' );

    // if we have a custom url on the image
    if ( isset( $attributes[ 'linkDestination' ] ) && $attributes[ 'linkDestination' ] == 'custom' ) {
        $a = $figure->getElementsByTagName( 'a' )[ 0 ];
        $infos[ 'url' ] = $a->getAttribute( 'href' );
        $infos[ 'blank' ] = strpos( $infos[ 'url' ], get_home_url() ) !== 0;
    }

    // caption, also see https://stackoverflow.com/a/2087136/1107529
    // because the caption can contain html
    $figCaption = $figure->getElementsByTagName( 'figcaption' );
    if ( count( $figCaption ) ) {
        $caption = '';
        foreach ( $figCaption[ 0 ]->childNodes as $child ) {
            $caption .= $dom->saveHTML( $child );
        }
        $infos[ 'caption' ] = $caption;
    }

    // create your custom html output here. In my case, I passed the
    // info to a vue component
    $html = sprintf( '<my-custom-vue-component :info="%s"></my-custom-vue-component>',
                esc_attr( json_encode( $info ) ) );

    return $html;
}

function parseMenuItems($items, $args) {
	global $wpdb;
	$defaults = [
		'post_type' => 'nav_menu_item',
		'posts_per_page' => -1,
	];

	$args = wp_parse_args($args, $defaults);
	$childrenQuery = "SELECT COUNT(*) FROM $wpdb->posts WHERE post_parent = %d;";
	$contents = [];
	foreach ($items as $item) {
		// echo '&raquo; '.$item->ID.' -------------------------------------------------<br />';
		$theContent = get_post_meta($item->ID, '_menu_blocks', true);
		$hasChildren = intVal($wpdb->get_var(sprintf($childrenQuery, $item->ID)));
		// echo 'Initial content<pre>';var_dump(htmlentities($theContent));echo '</pre>';
		if ($hasChildren) {
			// echo '&raquo; Children ----------------------------------------------------<br />';
			$theContent = str_replace("-->\n@children\n<!--", "-->\n%s\n<!--", $theContent);
			// echo 'Replaced content <pre>';var_dump(htmlentities($theContent), $hasChildren);echo '</pre>';
			// $args['post_parent'] = $item->ID;
			$nArgs = array_merge($args, [ 'post_parent' => $item->ID]);
			$submenu = new WP_Query($nArgs);
			$submenus = parseMenuItems($submenu->posts, $args);
			// echo '&raquo;&laquo;<pre>';var_dump(htmlentities($theContent), htmlentities(implode('',$submenus)));echo '</pre>';
			$theContent = sprintf($theContent, implode('', $submenus));
		}
		// echo 'Parsed content <pre>';var_dump(htmlentities($theContent));echo '</pre>';
		$contents[] = $theContent;
	}
	// echo '<pre>';var_dump(array_map(function($e) {return htmlentities($e); }, $contents));echo '</pre>';
	return $contents;
}

function setupMenuContent($menuId) {
	$term = get_term($menuId);
	$args = [
		'post_parent' => 0,
		'post_type' => 'nav_menu_item',
		'posts_per_page' => -1,
		'order' => 'asc',
		'orderby' => 'menu_order',
		'tax_query' => [
			[
				'taxonomy' => 'nav_menu',
				'terms' => $menuId
			]
		]
	];
	$mainMenuItems = new WP_Query($args);
	$contents = parseMenuItems($mainMenuItems->posts, $args);
	// echo '<pre>';var_dump(array_map(function($e) {return htmlentities($e); }, $contents));echo '</pre>';

	$eArgs = [
		'post_type' => 'menu_group',
		'name' => $term->slug
	];
	$isset = new WP_Query($eArgs);
	if ($isset->have_posts()) {
		wp_update_post([
			'ID'=> $isset->posts[0]->ID,
			'post_title' => $term->slug,
			'post_name' => $term->slug,
			'post_content' => implode("\n", $contents),
		]);
	} else {
		wp_insert_post([
			'post_content' => implode("\n", $contents),
			'post_name' => $term->slug,
			'post_title' => $term->slug,
			'post_type' => 'menu_group',
			'post_status' => 'publish',
		]);
	}
}
add_action('wp_update_nav_menu', 'setupMenuContent');


/**
 * Enqueue blockstyles JavaScript
 */
function block_styles_enqueue_javascript() {
    wp_enqueue_script( 'block-styles-script',
        get_template_directory_uri().'/inc/block-style.js',
        array( 'wp-blocks')
		);
}
add_action( 'enqueue_block_editor_assets', 'block_styles_enqueue_javascript' );

/**
 * Enqueue blockstyles Stylesheet
 */
function block_styles_enqueue_stylesheet() {
    wp_enqueue_style( 'block-styles-stylesheet',
       get_template_directory_uri().'/inc/block-style.css'
    );
}
add_action( 'enqueue_block_assets', 'block_styles_enqueue_stylesheet' );



add_filter('fse_starter_page_templates_config', function ($cfg) {
  $cfg['templates'][] = [
    'title' => 'Test T',
		'slug' => 'test_t',
		'id' => 6273,
	];
	return $cfg;

});

function add_extra_menus() {
	register_nav_menus([
		'profile-menu' => __( 'User profile Menu' , 'zero'),
	]);
}
add_action( 'init', 'add_extra_menus' );

function index_menu_group($post_types, $args) {
	$post_types['menu_group'] = 'menu_group';
	$post_types['attachment'] = 'attachment';
	$post_types['product_variation'] = 'product_variation';
	$post_types['acf-field-group'] = 'acf-field-group';
	return $post_types;
}
add_filter( 'index_extra_post_types', 'index_menu_group', 10, 2);

function remove_default_image_sizes( $sizes) {
	unset($sizes['thumbnail']);
	unset($sizes['medium']);
	unset($sizes['medium_large']);
	unset($sizes['large']);
	unset($sizes['post-thumbnail']);
	unset($sizes['woocommerce_thumbnail']);
	unset($sizes['shop_catalog']);
	unset($sizes['shop_thumbnail']);
	unset($sizes['woocommerce_single']);
	unset($sizes['woocommerce_gallery_thumbnail']);
	unset($sizes['shop_single']);
	return $sizes;
}
add_filter('intermediate_image_sizes_advanced','remove_default_image_sizes');

/**
 * Theme settings.
 */
require get_template_directory() . '/inc/starter-page.php';

function acf_load_color_field_choices( $field ) {
    
	// reset choices
	$field['post_type'] = 'acf-field-group';
	return $field;
	
}

add_filter('acf/load_field/name=_acf_schema', 'acf_load_color_field_choices');

function extend_scripts_And_styles() {
	global $post;
	?>
	<style>
		#createForm {
			display: flex;
			justify-content: space-between;
		}

		#editLinkSpan {
			float: right;
		}
		.schema-field {
			cursor: pointer;
			text-decoration: underline;
		}
	</style>
	<script>
		jQuery('#createForm').click(() => {
			Object.assign(document.createElement('a'), { target: '_blank', href: "post-new.php?post_type=acf-field-group&object=post&obid=<?php echo $post->ID; ?>"}).click();

			// window.location.href="post-new.php?post_type=acf-field-group&object=post&obid=<?php echo $post->ID; ?>"
		})

		function directCopy(str){
			document.oncopy = function(event) {
				event.clipboardData.setData("Text", str);
				event.preventDefault();
			};
			document.execCommand("Copy");
			document.oncopy = undefined;
		}

		jQuery(document).on('click',  '.schema-field', function() {
			directCopy('%'+jQuery(this).data('fn')+'%')
			alert('Field copied to clipboard')
			// const attrs = wp.data.select('core/editor').getSelectedBlock()
			// attrs.attributes.content = jQuery(this).data('fn')
			// wp.data.dispatch('core/editor').updateBlockAttributes(opts)
		})

		jQuery(window).bind('storage', function (e) {
			jQuery('[data-name="_acf_schema"] select').val(e.originalEvent.newValue).change()
		});

		jQuery(document).ready(() => {
			let text = ''
			if ('' != jQuery('[data-name="_acf_schema"] select').val()) {
				text = '<a target="_blank" class="button button-primary" href="/backend/wp-admin/post.php?post='+jQuery('[data-name="_acf_schema"] select').val()+'&action=edit&object=post&obid=<?php echo $post->ID; ?>">Edit group</a>'
			}
			jQuery('#fieldsData .acf-label span').html(text)

			jQuery('[data-name="_acf_schema"] select').change(function (e) {
				wp.ajax.post({
					action: 'acf_schema',
					id: jQuery(this).val()
				}).done((r) => {
					console.log('r', r)
					if (!r.length) {
						jQuery('#fieldsData ul').html('<li>No fields avaliable</li>')
						return
					}
					const rcd = r.map((i) => {
						return `<li class="schema-field" data-fn="${i.name}">${i.label}</li>`
					})
					jQuery('#fieldsData ul').html(rcd.join(''))
					console.log('rcd', rcd)
				})
				let text = ''
				if ('' != jQuery(this).val()) {
					text = '<a class="button button-primary" href="/backend/wp-admin/post.php?post='+jQuery(this).val()+'&action=edit">Edit group</a>'
				}
				jQuery('#fieldsData .acf-label span').html(text)

			})

			<?php if ('acf-field-group' == $post->post_type): ?>
			jQuery(window).on("unload", function(e) {
				window.localStorage.setItem('acfUpdated', "<?php echo $post->ID; ?>")
			});
			<?php endif; ?>
	
			const ls = window.location.search.substr(1)
			if (ls.includes('&object=') && ls.includes('&obid=')) {
				const param = {}
				const params = ls.split('&').map(e => e.split('=')).map(e => {
					param[e[0]] = e[1]
				})
				jQuery('#acf_field_group-location-group_0-rule_0-param').val(param.object)
				jQuery('#acf_field_group-location-group_0-rule_0-value').val(param.obid)
				jQuery('#acf-field-group-locations').hide()
				jQuery('#acf_field_group-label_placement').val('left')
				jQuery('#acf-field-group-options').hide()
			}
		})

	</script>
	<?php
}
add_action('admin_footer', 'extend_scripts_And_styles');

/**
 * Extend blocks
 */
add_action( 'enqueue_block_editor_assets', function(){
 wp_enqueue_script( 'extended-gutenberg-script',
        get_template_directory_uri(). '/inc/extended-gutenberg.js',
        array( 'wp-blocks' )
    );
} );

function rff ($c, $i) {
	$c[$i['name']] = $i['label'];
	return $c;
}

function embed_acf_schema($post_id, $post, $update) {
	$schemaId = get_post_meta($post_id, '_acf_schema', true);
	if ($schemaId) {
		update_post_meta($post_id, 'acf_schema', acf_get_fields($schemaId));
		$fields = acf_get_fields($schemaId);
		$reduced = array_reduce($fields, 'rff');
		$template = '<li class="schema-field" data-fn="%s">%s</li>';
		$fieldsArray = [];
		foreach ($reduced as $name => $label) {
			$fieldsArray[] = sprintf($template, $name, $label);
		}
		update_post_meta($post_id, '_schema_fields', '<ul>'.implode("\n", $fieldsArray).'</ul>');
	}
	// $ref = $_SERVER['HTTP_REFERER'];
	// parse_str(parse_url($ref, PHP_URL_QUERY), $qs);
	// if ('acf-field-group' == $post->post_type && isset($qs['obid'])) {
	// 	// // update_post_meta()
	// 	// var_dump($ref, $qs);
	// 	// die;
	// }
}
add_action('save_post', 'embed_acf_schema', 10, 3);

function my_acf_prepare_field( $field ) {
	global $post;
	
	$fds = get_post_meta($post->ID, '_schema_fields', true);
	if ($fds) {
		$field['message'] = $fds;
	}
	return $field;  
}
add_filter('acf/prepare_field/key=field_5e15e704e4154', 'my_acf_prepare_field');

function retrive_acf_schema() {
	wp_send_json_success(acf_get_fields($_REQUEST['id']));
	die;
}
add_action( 'wp_ajax_acf_schema', 'retrive_acf_schema');  




add_action('admin_menu', 'my_cool_plugin_create_menu');
function my_cool_plugin_create_menu() {
	//create new top-level menu
	add_menu_page('My Cool Plugin Settings', 'Cool Settings', 'administrator', __FILE__, 'my_cool_plugin_settings_page' , plugins_url('/images/icon.png', __FILE__) );
	//call register settings function
	add_action( 'admin_init', 'register_my_cool_plugin_settings' );
}
function register_my_cool_plugin_settings() {
	//register our settings
	register_setting( 'my-cool-plugin-settings-group', 'new_option_name' );
}
function my_cool_plugin_settings_page() {

}

function handle_acf_action() {
	global $action;
	if (!$action) die(0);
	if('_' == $action[0]) $action = substr($action, 1);
	if (function_exists($action)) call_user_func_array($action, $_REQUEST);
	else if (function_exists($action.'_cb')) call_user_func_array($action.'_cb', $_REQUEST);
	else {
		wp_die('No such action!', 404);
	}
}

function hook_custom_ajax_actions() {
	$actions = apply_filters('sojuz_actions', [
		'Built in' => [
			'login'=> 'Login',
			'register' => 'Register',
			'add_to_cart' => 'Add to cart',
			'clear_cart' => 'Clear cart',
			'remove_from_cart' => 'Remove from cart',
			'update_cart' => 'Update cart',
			'like' => 'Like',
			'bookmark' => 'Bookmark',
			'checkout' => 'Checkout',
			'update_profile' => 'Profile',
			'apply_coupon' => 'Coupon',
			'false' => 'Frontend action',
		],
		'User defined' => [
			'__action'			=> 'This is how actions beggining with _ are defined',
			'_action'			=> 'Actions containing _ as first character do not require user to be legged in',
		],
	]);

	foreach($actions as $type => $group) {
		if ('Built in' == $type) continue;
		foreach ($group as $action => $label) {
			$glue = ('_' == substr($action, 0, 1)) ? 'nopriv_': '';
			$key = "wp_ajax_{$glue}{$action}";
			add_action($key, 'handle_acf_action');
		}
	}

	return $actions;
}

function _action() {
	echo 'Simple non privileged function example for functions that start with _ char. Action should start with __';
	die;
}

function action() {
	echo 'Simple non privileged function example for normal functions';
	die;
}

function add_cb_field($fg) {
	$actions = hook_custom_ajax_actions();
	// foreach array_pop(($actions) as $action)
	acf_render_field_wrap([
		'label'			=> __('Callback','acf'),
		'instructions'	=> __('Action to be ttaken when form gets processed','acf'),
		'type'			=> 'select',
		'choices'		=> $actions,
		'value' => $fg['action']
	]);
}
add_action('acf/render_field_group_settings', 'add_cb_field');
add_action('admin_init', 'hook_custom_ajax_actions');