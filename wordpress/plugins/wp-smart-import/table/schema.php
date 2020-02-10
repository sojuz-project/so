<?php
/**
 * Plugin database schema
 * WARNING: 
 * 	dbDelta() doesn't like empty lines in schema string, so don't put them there;
 *  WPDB doesn't like NULL values so better not to have them in the tables;
 */

/**
 * The database character collate.
 * @var string
 * @global string
 * @name $charset_collate
 */

// Declare these as global in case schema.php is included from a function.
 global $wpdb;
$prefix =  $wpdb->prefix."wpsi_";
$charset_collate = $wpdb->get_charset_collate();
$table = $prefix."imports";
$main_tab = $table;									
$sql =  "CREATE TABLE $table (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			name VARCHAR(255) NOT NULL DEFAULT '',
			feed_type ENUM('xml','csv','zip','gz','') NOT NULL DEFAULT '',	
			file_path TEXT,	
			options LONGTEXT,		
			post_data LONGTEXT,	
			post_type VARCHAR(255),	
			unique_key VARCHAR(255) NOT NULL DEFAULT '',
		  	root_element VARCHAR(255) DEFAULT '',
		  	count BIGINT(20) NOT NULL DEFAULT 0,
		  	created BIGINT(20) NOT NULL DEFAULT 0,
		  	updated BIGINT(20) NOT NULL DEFAULT 0,
		  	failed BIGINT(20) NOT NULL DEFAULT 0,
		  	created_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',			
		  	last_activity DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',			
			PRIMARY KEY  (id)
		) $charset_collate;";

$table = $prefix."posts";	
$sql .="CREATE TABLE $table (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			post_id BIGINT(20) UNSIGNED NOT NULL,
			import_id BIGINT(20) UNSIGNED NOT NULL,
			unique_key TEXT,
			PRIMARY KEY  (id),
			FOREIGN KEY (import_id) REFERENCES $main_tab(id) ON DELETE CASCADE ON UPDATE CASCADE 
		) $charset_collate;";

$table = $prefix."files";
$sql .="CREATE TABLE $table (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			name VARCHAR(255) NOT NULL DEFAULT '',
			file_name VARCHAR(255) NOT NULL DEFAULT '',
			file_path TEXT,
			folder_name TEXT,
		  	created_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',			
		  	updated_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',			
			PRIMARY KEY  (id)
		) $charset_collate;";
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);