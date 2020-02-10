<?php
/**
 * Extract primary image colors
 *
 * @author    Maciek &lt;minimal2&gt; Dmowski <matt@sojuz.team>
 * @copyright 2019 SOJUZ Team
 * @package wpipc
 *
 * @wordpress-plugin
 * Plugin Name: Extract primary image colors
 * Description: This plugin extracts image's primary colors. It hooks into <code>wp_generate_attachment_metadata</code> filter and extends meta with an array of main colors.
 * Version:     1.0.0
 * Author:      Maciek &lt;minimal2&gt; Dmowski
 * Author URI:  https://sojuz.team
 * License:     GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

 define('DEFAULT_NUMBER_OF_COLORS', 2);

require_once 'colors.inc.php';

/**
 * Filter for extending the attachements metadata
 * @param array $meta Image's metadata
 * @param int   $id   Attachment ID
 * @return array Modified metas
 */
function get_colors($meta, $id) {
  // var_dump($meta['image_meta']);
  $settings = get_option('ipc_settings', array());
  if (isset($meta['image_meta'])) {
    $uploads = wp_upload_dir()['basedir'];
    $ex=new GetMostCommonColors();
    $c = $ex->Get_Color("{$uploads}/{$meta['file']}", $settings['ipc_number'], $settings['ipc_brightness'], $settings['ipc_gradients'], $settings['ipc_delta']);
    $meta['colors'] = [(count($c) > 1) ? []: array_shift($c)];
  }
  return $meta;
}
add_filter('wp_generate_attachment_metadata', 'get_colors', 30, 2);

/**
 * Registers primary colors metabox
 *
 * @return void
 */
function add_primary_color_metabox() {
	add_meta_box(
		'primary_color_metabox',
		'Primary colors',
		'primary_color_metabox',
		'attachment',
		'side'
	) ;
}
add_action( 'add_meta_boxes', 'add_primary_color_metabox' );

/**
 * Profides markup for primary color metabox
 *
 * @return void
 */
function primary_color_metabox() {
  $attMeta = get_post_meta( get_the_ID(), '_wp_attachment_metadata', true );
  print('<style>.colorBox {width: 100%; text-align: center; padding: 10px 0; }</style>');
  if ($attMeta['colors']) {
    foreach($attMeta['colors'] as $color => $fl) {
      printf('<div class="colorBox" style="background-color: #%1$s;">#%1$s</div>', $color);
    }
  } else {
    print('<p class="colorBox">Avaliable only for image attachements</p>');
  }
}

/**
 * Provides Settings API functionality
 *
 * @return void
 */
function register_ipc_settings() {
  add_settings_section (
    'ipc_settings', //section name for the section to add
    'Image primary colors', //section title visible on the page
    '__return_false', //callback for section description
    'media'//page to which section will be added.
  );
  register_setting( 'media', 'ipc_settings', array('default' => array(
    'ipc_number' => 2,
    'ipc_brightness' => true,
    'ipc_gradients' => true,
    'ipc_delta' => 24,
  )) );
  add_settings_field(
  	'ipc_number',
  	'Number of colors',
  	'ipc_render_field',
    'media',
    'ipc_settings',
    array('name' => 'ipc_number', "type" => 'number')
  );
  add_settings_field(
  	'ipc_brightness',
  	'Reduce brightness',
  	'ipc_render_field',
    'media',
    'ipc_settings',
    array('name' => 'ipc_brightness', "type" => 'checkbox')
  );
  add_settings_field(
  	'ipc_gradients',
  	'Reduce gradients',
  	'ipc_render_field',
    'media',
    'ipc_settings',
    array('name' => 'ipc_gradients', "type" => 'checkbox')
  );
  add_settings_field(
  	'ipc_delta',
  	'Delta',
  	'ipc_render_field',
    'media',
    'ipc_settings',
    array('name' => 'ipc_delta', "type" => 'number', 'description' => 'TBD')
  );
}
add_action('admin_init', 'register_ipc_settings', 10);

/**
 * Renders form field based on args array
 *
 * @param array $args
 * @return void
 */
function ipc_render_field($args) {
  $settings = get_option( 'ipc_settings' );
  $markup = '<input type="%s" name="ipc_settings[%s]" value="%s" %s />%s';
  $markup = sprintf($markup, $args['type'], $args['name'], '%s', '%s', '%s');
  $params = array();
  $extra = '';
  $value = $settings[$args['name']];
  switch($args['type']) {
    case 'checkbox':
      $value = 'true';
      if ($settings[$args['name']]) {
        $params[] = 'checked="checked"';
      }
      break;
    case 'number':
      $params = array(
        'min="1"',
        'step="1"'
      );
      break;
    default:
  }
  printf($markup, $value, implode(' ', $params), $extra);
}