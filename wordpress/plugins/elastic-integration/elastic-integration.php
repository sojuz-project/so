<?php
/*
 * Plugin Name:     Elastic integration
 * Plugin URI:      https://sojuz.team
 * Description:     Integrates wordpress with elasticsearch
 * Author:          Maciek &lt;minimal2&gt; Dmowski (SOJUZ Team)
 * Author URI:      https://dmowsk.it
 * Text Domain:     elastic-integration
 * Domain Path:     /languages
 * Version:         0.1.0
 * Requires at least:4.0
 * Tested up to:     4.0
 *
 * @package WordPress
 * @author Maciej Dmowski
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

include 'inc/admin.php';
include 'inc/elasticsearch.php';

function setup_plugin() {
  $schema = json_decode(file_get_contents(__DIR__.'/schema.json'), ARRAY_A);
  elasticsearch(false, 'sojuz', 'DELETE');
  elasticsearch($schema, 'sojuz', 'PUT');
  sync_index();
}
register_activation_hook( __FILE__, 'setup_plugin' );