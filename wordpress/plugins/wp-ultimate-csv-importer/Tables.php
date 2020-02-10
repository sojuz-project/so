<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

namespace Smackcoders\FCSV;

if ( ! defined( 'ABSPATH' ) )
exit; // Exit if accessed directly

class Tables {

	private static $instance = null;
	private static $smack_csv_instance = null;

	public static function getInstance() {
		if (Tables::$instance == null) {
			Tables::$instance = new Tables;
			Tables::$smack_csv_instance = SmackCSV::getInstance();
			Tables::$instance->create_tables();
			return Tables::$instance;
		}
		return Tables::$instance;
	}

	public function create_tables(){
		global $wpdb;
		$file_table_name = $wpdb->prefix ."smackcsv_file_events";
		$result = $wpdb->query("SHOW COLUMNS FROM `{$wpdb->prefix}import_detail_log` LIKE 'running'");
		if($result == 0){
			$wpdb->query("ALTER TABLE `{$wpdb->prefix}import_detail_log` ADD COLUMN running boolean not null default 1");
		}
		$wpdb->query("CREATE TABLE IF NOT EXISTS $file_table_name (
			`id` int(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			`file_name` VARCHAR(255) NOT NULL,
			`status` VARCHAR(255) NOT NULL,
			`mode` VARCHAR(255) NOT NULL,
			`hash_key` VARCHAR(255) NOT NULL,
			`total_rows` INT(255) NOT NULL,
			`lock` BOOLEAN DEFAULT false,
			`progress` INT(6)) ENGINE=InnoDB" 
				);

		$image_table =  $wpdb->prefix ."ultimate_csv_importer_media";
		$wpdb->query("CREATE TABLE IF NOT EXISTS $image_table (
			`post_id` INT(6),
			`attach_id` INT(6) NOT NULL,
			`image_url` VARCHAR(255) NOT NULL,
			`hash_key` VARCHAR(255) NOT NULL,
			`status` VARCHAR(255) DEFAULT 'pending',
			`module` VARCHAR(255) DEFAULT NULL,
			`image_type` VARCHAR(255) DEFAULT NULL
				) ENGINE=InnoDB"
				);

		$shortcode_table_name =  $wpdb->prefix ."ultimate_csv_importer_shortcode_manager";
		$wpdb->query("CREATE TABLE IF NOT EXISTS $shortcode_table_name (
			`post_id` INT(6),
			`image_shortcode` VARCHAR(255) NOT NULL,
			`original_image` VARCHAR(255) NOT NULL,
			`hash_key` VARCHAR(255) NOT NULL,
			`status` VARCHAR(255) DEFAULT 'pending'
				) ENGINE=InnoDB"
				);

		$template_table_name = $wpdb->prefix ."ultimate_csv_importer_mappingtemplate";
		$wpdb->query( "CREATE TABLE IF NOT EXISTS $template_table_name (
			`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`templatename` varchar(250) NOT NULL,
			`mapping` blob NOT NULL,
			`createdtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`deleted` int(1) DEFAULT '0',
			`templateused` int(10) DEFAULT '0',
			`mapping_type` varchar(30),
			`module` varchar(50) DEFAULT NULL,
			`csvname` varchar(250) DEFAULT NULL,
			`eventKey` varchar(60) DEFAULT NULL				
				) ENGINE = InnoDB "
				);  

		$log_table_name = $wpdb->prefix ."import_detail_log";
		$wpdb->query("CREATE TABLE IF NOT EXISTS $log_table_name (
			`id` int(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			`file_name` VARCHAR(255) NOT NULL,
			`status` VARCHAR(255) NOT NULL,
			`hash_key` VARCHAR(255) NOT NULL,
			`total_records` INT(6),
			`processing_records` INT(6) default 0,
			`remaining_records` INT(6) default 0,
			`filesize` VARCHAR(255) NOT NULL,
			`created` bigint(20) NOT NULL default 0,
			`updated` bigint(20) NOT NULL default 0,
			`skipped` bigint(20) NOT NULL default 0
				) ENGINE=InnoDB" 
				);

		$importlog_table_name = $wpdb->prefix ."import_log_detail";
		$wpdb->query("CREATE TABLE IF NOT EXISTS $importlog_table_name (
			`id` int(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			`hash_key` VARCHAR(255) NOT NULL,
			`message` VARCHAR(255) NOT NULL,
			`status` VARCHAR(255) NOT NULL,
			`verify` blob NOT NULL,
			`categories` VARCHAR(255) NOT NULL,
			`tags` VARCHAR(255) NOT NULL,
			`post_id` int(6) NULL
				) ENGINE=InnoDB" 
				);

		$import_table_name = $wpdb->prefix ."import_postID";
		$wpdb->query("CREATE TABLE IF NOT EXISTS $import_table_name(
			`post_id` int(6) NOT NULL,
			`line_number` int(6) NOT NULL) "
				);

		$import_records_table = "smackuci_events";
		$wpdb->query("CREATE TABLE IF NOT EXISTS $import_records_table (
			`id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`revision` bigint(20) NOT NULL default 0,
			`name` varchar(255),
			`original_file_name` varchar(255),
			`friendly_name` varchar(255),
			`import_type` varchar(32),
			`filetype` text,
			`filepath` text,
			`eventKey` varchar(32),
			`registered_on` datetime NOT NULL default '0000-00-00 00:00:00',
			`parent_node` varchar(255),
			`processing` tinyint(1) NOT NULL default 0,
			`executing` tinyint(1) NOT NULL default 0,
			`triggered` tinyint(1) NOT NULL default 0,
			`event_started_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`count` bigint(20) NOT NULL default 0,
			`processed` bigint(20) NOT NULL default 0,
			`created` bigint(20) NOT NULL default 0,
			`updated` bigint(20) NOT NULL default 0,
			`skipped` bigint(20) NOT NULL default 0,
			`deleted` bigint(20) NOT NULL default 0,
			`is_terminated` tinyint(1) NOT NULL default 0,
			`terminated_on` datetime NOT NULL default '0000-00-00 00:00:00',
			`last_activity` datetime NOT NULL default '0000-00-00 00:00:00',
			`siteid` int(11) NOT NULL DEFAULT 1,
			`month` varchar(60) DEFAULT NULL,
			`year` varchar(60) DEFAULT NULL
				) ENGINE=InnoDB"
				);

		$acf_fields_table = $wpdb->prefix ."ultimate_csv_importer_acf_fields";
		$wpdb->query("CREATE TABLE IF NOT EXISTS $acf_fields_table (
			`id` int(10) NOT NULL AUTO_INCREMENT,
			`groupId` varchar(100) NOT NULL,
			`fieldId` varchar(100) NOT NULL,
			`fieldLabel` varchar(100) NOT NULL,
			`fieldName` varchar(100) NOT NULL,
			`fieldType` varchar(60) NOT NULL,
			`fdOption` varchar(100) DEFAULT NULL,
			PRIMARY KEY (`id`)
				) ENGINE=InnoDB"
				);
	}
}
