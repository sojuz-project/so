<?php
/**
 * Plugin Name: list-section — SOJUZ Gutenberg Block Plugin
 * Plugin URI: https://github.com/sojuz-project
 * Description: list-section — is a Gutenberg plugin created via create-guten-block.
 * Author: mrahmadawais, maedahbatool, SOJUZ team
 * Author URI: https://github.com/sojuz-project
 * Version: 1.0.0
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package SOJUZ
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Block Initializer.
 */
require_once plugin_dir_path( __FILE__ ) . 'src/init.php';
