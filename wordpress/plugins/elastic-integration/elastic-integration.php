<?php
/*
 * Plugin Name:     Elastic integration
 * Plugin URI:      https://sojuz.team
 * Description:     Integrates wordpress with elasticsearch
 * Author:          Maciek &lt;minimal2&gt; Dmowski (SOJUZ Team)
 * Author URI:      https://dmowsk.it
 * Text Domain:     elastic-integration
 * Domain Path:     /languages
 * Version:         1.0
 * Requires at least:4.0
 * Tested up to:     4.0
 *
 * @package WordPress
 * @author Maciej Dmowski
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

include 'inc/admin.php';
include 'inc/elasticsearch.php';

function setup_plugin() {
  $schema = json_decode(file_get_contents(__DIR__.'/schema.json'), ARRAY_A);
  $langs = array_keys(apply_filters( 'wpml_active_languages', [] ));
  $default = apply_filters( 'wpml_default_language', null );

  if (!count($langs)) $langs[] = '';
  foreach($langs as $lang) {
    elasticsearch($schema, ($default == $lang)? '' : $lang, 'PUT');
  }
  sync_index();
}
register_activation_hook( __FILE__, 'setup_plugin' );

function cleanup_indexes() {
  $langs = array_keys(apply_filters( 'wpml_active_languages', [] ));
  $default = apply_filters( 'wpml_default_language', null );

  if (!count($langs)) $langs[] = '';
  foreach($langs as $lang) {
    elasticsearch(false, ($default == $lang)? '' : $lang, 'DELETE');
  }
}
register_deactivation_hook( __FILE__, 'cleanup_indexes' );