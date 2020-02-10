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

class CommentsImport {
    private static $comments_instance = null;

    public static function getInstance() {
		
		if (CommentsImport::$comments_instance == null) {
			CommentsImport::$comments_instance = new CommentsImport;
			return CommentsImport::$comments_instance;
		}
		return CommentsImport::$comments_instance;
    }

    public function comments_import_function($data_array , $mode , $hash_key , $line_number) {
		global $wpdb;
		$core_instance = CoreFieldsImport::getInstance();
		global $core_instance;
		$helpers_instance = ImportHelpers::getInstance();
		$log_table_name = $wpdb->prefix ."import_detail_log";
		$returnArr = [];

		$updated_row_counts = $helpers_instance->update_count($hash_key);
		$created_count = $updated_row_counts['created'];
		$updated_count = $updated_row_counts['updated'];
		$skipped_count = $updated_row_counts['skipped'];
		
		$commentid = '';
		$post_id = $data_array['comment_post_ID'];
		$post_exists = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}posts WHERE id = '" . $post_id . "' and post_status in ('publish','draft','future','private','pending')", ARRAY_A);
		$valid_status = array('1', '0', 'spam');
		if(empty($data_array['comment_approved'])) {
			$data_array['comment_approved'] = 0;
		}
		if(!in_array($data_array['comment_approved'], $valid_status)) {
			$data_array['comment_approved'] = 0;
		}
		$data_array['comment_approved'] = trim($data_array['comment_approved']);
		if ($post_exists) {
			if($mode == 'Insert'){
				$retID = wp_insert_comment($data_array);
				$mode_of_affect = 'Inserted';
				
				if(is_wp_error($retID) || $retID == '') {
					
					$core_instance->detailed_log[$line_number]['Message'] = "Skipped, Due to unknown post ID.";	
					$fields = $wpdb->get_results("UPDATE $log_table_name SET skipped = $skipped_count WHERE hash_key = '$hash_key'");
				
					$returnArr['MODE'] = $mode_of_affect;
					return $returnArr;
				}
				$core_instance->detailed_log[$line_number]['Message'] = 'Inserted Comment ID: ' . $retID;		
				$fields = $wpdb->get_results("UPDATE $log_table_name SET created = $created_count WHERE hash_key = '$hash_key'");
			}
			if($mode == 'Update'){
				$ID_result =  $wpdb->get_results("SELECT comment_ID FROM {$wpdb->prefix}comments WHERE comment_post_ID = $post_id order by comment_ID DESC ");		
				if ( is_array( $ID_result ) && ! empty( $ID_result ) ) {

					$retID = $ID_result[0]->comment_ID;		
					$data_array['comment_ID'] = $retID;
					wp_update_comment( $data_array );
					$mode_of_affect = 'Updated';

					$core_instance->detailed_log[$line_number]['Message'] = 'Updated Comment ID: ' . $retID;	
					$fields = $wpdb->get_results("UPDATE $log_table_name SET updated = $updated_count WHERE hash_key = '$hash_key'");
				}else{
					
					$retID = wp_insert_comment($data_array);
					$mode_of_affect = 'Inserted';
					
					if(is_wp_error($retID) || $retID == '') {	
						$core_instance->detailed_log[$line_number]['Message'] = "Skipped, Due to unknown post ID.";
						$fields = $wpdb->get_results("UPDATE $log_table_name SET skipped = $skipped_count WHERE hash_key = '$hash_key'");
						
						$returnArr['MODE'] = $mode_of_affect;
						return $returnArr;
					}
					$core_instance->detailed_log[$line_number]['Message'] = 'Inserted Comment ID: ' . $retID;		
					$fields = $wpdb->get_results("UPDATE $log_table_name SET created = $created_count WHERE hash_key = '$hash_key'");
				}
			}
		}else {
			$retID = $commentid;
			$core_instance->detailed_log[$line_number]['Message'] = "Skipped, Due to unknown post ID.";
			$fields = $wpdb->get_results("UPDATE $log_table_name SET skipped = $skipped_count WHERE hash_key = '$hash_key'");
		}
			
		$returnArr['ID'] = $retID;
		$returnArr['MODE'] = $mode_of_affect;
		return $returnArr;
    }
}
