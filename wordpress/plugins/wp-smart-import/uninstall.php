<?php 
if (!defined('WP_UNINSTALL_PLUGIN')) //if uninstall not called from WordPress exit
    exit();

global $wpdb;
// Delete created table when plugin uninstall
$prefix = $wpdb->prefix .'wpsi_';
$table_name = $prefix . 'files';
$sql = "DROP TABLE IF EXISTS $table_name";
$wpdb->query($sql);

$table_name = $prefix . 'posts';
$sql = "DROP TABLE IF EXISTS $table_name";
$wpdb->query($sql);

$table_name = $prefix . 'imports';
$sql = "DROP TABLE IF EXISTS $table_name";
$wpdb->query($sql);

// Delete options when plugin uninstall
delete_option('wp-smart-import-settings');
delete_option('wp-smart-import-session');