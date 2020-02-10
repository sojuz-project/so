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

class CustomerReviewsImport {
    private static $customer_reviews_instance = null;

    public static function getInstance() {
		
		if (CustomerReviewsImport::$customer_reviews_instance == null) {
			CustomerReviewsImport::$customer_reviews_instance = new CustomerReviewsImport;
			return CustomerReviewsImport::$customer_reviews_instance;
		}
		return CustomerReviewsImport::$customer_reviews_instance;
    }

    public function customer_reviews_import($data_array, $mode , $hash_key , $line_number) {
        global $wpdb;
		$mapping_instance = MappingExtension::getInstance();
		$helpers_instance = ImportHelpers::getInstance();
		$core_instance = CoreFieldsImport::getInstance();
		global $core_instance;
		$log_table_name = $wpdb->prefix ."import_detail_log";
		
		$reviewId = '';
		$returnArray = array('MODE' => $mode);
		$mode_of_affect = 'Inserted';
		$update_review_info = false;
		
		if(isset($data_array['review_format'])) {
            if(!array_key_exists('review_format', $data_array) && !isset($data_array['review_format']) && !empty($data_array['review_format'])) {
                $reviewFormat = 'business';
            } else {
                $reviewFormat = strtolower($data_array['review_format']);
            }
		}

		$post_id = $data_array['review_post'];
		$post_exists = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}posts WHERE id = '" . $post_id . "' and post_status in ('publish','draft','future','private','pending')", 'ARRAY_A');
		$get_available_plugin_lists = $mapping_instance->get_active_plugins();

		$updated_row_counts = $helpers_instance->update_count($hash_key);
		$created_count = $updated_row_counts['created'];
		$updated_count = $updated_row_counts['updated'];
		$skipped_count = $updated_row_counts['skipped'];

		if($mode == 'Insert') {
			if ($post_exists) {
				update_post_meta($post_id, 'wpcr3_enable', 1);
				update_post_meta($post_id, 'wpcr3_format', $reviewFormat);
				if(in_array('wp-customer-reviews/wp-customer-reviews-3.php', $get_available_plugin_lists)) {
					$review_date = current_time('mysql', 0);
					if(isset($data_array['date_time'])) {
						$review_date = date( 'Y-m-d H:i:s', strtotime( $data_array['date_time'] ) );
					}
					$review_title = $data_array['review_name'];
					$review_slug = preg_replace('/[^a-zA-Z0-9._\-\s]/', '', $review_title);
					$review_slug = wp_unique_filename('', $review_slug);
					if(isset($data_array['status'])) {
						$data_array['status'] = strtolower( $data_array['status'] );
					}
					if ($data_array['status'] != 'publish' && $data_array['status'] != 'private' && $data_array['status'] != 'draft' && $data_array['status'] != 'pending' && $data_array['status'] != 'sticky') {
						$data_array ['post_password'] = '';
						$stripPSF = strpos($data_array['status'], '{');
						if ($stripPSF === 0) {
							$poststatus = substr($data_array['status'], 1);
							$stripPSL = substr($poststatus, -1);
							if ($stripPSL == '}') {
								$postpwd = substr($poststatus, 0, -1);
								$data_array['status'] = 'publish';
								$data_array ['post_password'] = $postpwd;
							} else {
								$data_array['status'] = 'publish';
								$data_array ['post_password'] = $poststatus;
							}
						} else {
							$data_array['status'] = 'publish';
						}
					}
					$review_array = array(
						'post_author' => '1',
						'post_date' => $review_date,
						'post_content' => $data_array['review_text'],
						'post_title' => $review_title,
						'post_status' => $data_array['status'],
						'comment_status' => 'closed',
						'ping_status' => 'closed',
						'post_password' => $data_array['post_password'],
						'post_name' => $review_slug,
						'post_parent' => 0,
						'post_type' => 'wpcr3_review',
					);
					$reviewId = wp_insert_post($review_array);
					if(is_wp_error($reviewId)) {
						
						$core_instance->detailed_log[$line_number]['Message'] = "Can't insert this Review. " . $reviewId->get_error_message();
						$fields = $wpdb->get_results("UPDATE $log_table_name SET skipped = $skipped_count WHERE hash_key = '$hash_key'");
						return $returnArray;
					}
					$guId = site_url() . '/?post_type=wpcr3_review&#038;p=' . $reviewId;

					wp_update_post(array('ID' => $reviewId, 'guid' => $guId));
					// Review meta information
					$review_meta_data = array(
						'wpcr3_review_ip'       => $data_array['review_ip'],
						'wpcr3_review_post'     => $data_array['review_post'],
						'wpcr3_review_name'     => $data_array['review_name'],
						'wpcr3_review_email'    => $data_array['review_email'],
						'wpcr3_review_rating'   => $data_array['review_rating'],
						'wpcr3_review_title'    => $data_array['review_title'],
						'wpcr3_review_website'  => $data_array['review_website'],
						'wpcr3_review_admin_response' => $data_array['review_admin_response'],
						'wpcr3_f1'  => $data_array['custom_field1'],
						'wpcr3_f2'  => $data_array['custom_field2'],
						'wpcr3_f3'  => $data_array['custom_field3'],
					);
					foreach($review_meta_data as $metaKey => $metaValue) {
						update_post_meta($reviewId, $metaKey, $metaValue);
					}
				} else {
					$wpdb->insert($wpdb->wpcreviews, array('date_time' => $data_array['date_time'], 'review_name' => $data_array['review_name'], 'review_email' => $data_array['review_email'], 'review_ip' => $data_array['reviewer_ip'], 'review_title' => $data_array['review_title'], 'review_text' => $data_array['review_text'], 'review_admin_response' => $data_array['review_admin_response
'], 'status' => $data_array['status'], 'review_rating' => $data_array['review_rating'], 'review_website' => $data_array['review_website'], 'page_id' => $data_array['review_post']));
					$reviewId = $wpdb->insert_id;
					if(is_wp_error($reviewId)) {

						$core_instance->detailed_log[$line_number]['Message'] = "Can't insert this Review. " . $reviewId->get_error_message();
						$fields = $wpdb->get_results("UPDATE $log_table_name SET skipped = $skipped_count WHERE hash_key = '$hash_key'");
						return $returnArray;
					}
				}

				$mode_of_affect = 'Inserted';
				$core_instance->detailed_log[$line_number]['Message'] = 'Inserted Review ID: ' . $reviewId;
				$fields = $wpdb->get_results("UPDATE $log_table_name SET created = $created_count WHERE hash_key = '$hash_key'");
				
			}
		} else {
			if($post_exists) {
				if($mode == 'Update') {
					
					update_post_meta($post_id, 'wpcr3_enable', 1);
					update_post_meta($post_id, 'wpcr3_format', $reviewFormat);
					if(in_array('wp-customer-reviews/wp-customer-reviews-3.php', $get_available_plugin_lists)) {
						$query = "select *from {$wpdb->prefix}posts where ID = '{$data_array['review_id']}' and post_type = 'wpcr3_review'";
						$id_results = $wpdb->get_results($query);
						$reviewId = $id_results[0]->ID;
						$review_date = current_time('mysql', 0);
						if(isset($data_array['date_time'])) {
							$review_date = date( 'Y-m-d H:i:s', strtotime( $data_array['date_time'] ) );
						}
						$review_title = $data_array['review_name'];
						$review_slug = preg_replace('/[^a-zA-Z0-9._\-\s]/', '', $review_title);
						$review_slug = wp_unique_filename('', $review_slug);
						if(isset($data_array['status'])) {
							$data_array['status'] = strtolower( $data_array['status'] );
						}
						if ($data_array['status'] != 'publish' && $data_array['status'] != 'private' && $data_array['status'] != 'draft' && $data_array['status'] != 'pending' && $data_array['status'] != 'sticky') {
							$stripPSF = strpos($data_array['status'], '{');
							if ($stripPSF === 0) {
								$poststatus = substr($data_array['status'], 1);
								$stripPSL = substr($poststatus, -1);
								if ($stripPSL == '}') {
									$postpwd = substr($poststatus, 0, -1);
									$data_array['status'] = 'publish';
									$data_array ['post_password'] = $postpwd;
								} else {
									$data_array['status'] = 'publish';
									$data_array ['post_password'] = $poststatus;
								}
							} else {
								$data_array['status'] = 'publish';
							}
						}
						$review_array = array(
							'post_author' => '1',
							'post_date' => $review_date,
							'post_content' => $data_array['review_text'],
							'post_title' => $review_title,
							'post_status' => $data_array['status'],
							'comment_status' => 'closed',
							'ping_status' => 'closed',
							'post_password' => $data_array['post_password'],
							'post_name' => $review_slug,
							'post_parent' => 0,
							'post_type' => 'wpcr3_review',
						);
						if ( $reviewId == null ) {
							$reviewId = wp_insert_post($review_array);
							if(is_wp_error($reviewId)) {

								$core_instance->detailed_log[$line_number]['Message'] = "Can't insert this Review. " . $reviewId->get_error_message();
								$fields = $wpdb->get_results("UPDATE $log_table_name SET skipped = $skipped_count WHERE hash_key = '$hash_key'");	
								return $returnArray;
							}

							$mode_of_affect = 'Inserted';
							$core_instance->detailed_log[$line_number]['Message'] = 'Inserted Review ID: ' . $reviewId;;
							$fields = $wpdb->get_results("UPDATE $log_table_name SET created = $created_count WHERE hash_key = '$hash_key'");
							
						} else {
							$review_array['ID'] = $reviewId;
							wp_update_post($review_array);
							$mode_of_affect = 'Updated';

							$core_instance->detailed_log[$line_number]['Message'] = 'Updated Review ID: ' . $reviewId;
							$fields = $wpdb->get_results("UPDATE $log_table_name SET updated = $updated_count WHERE hash_key = '$hash_key'");
						}
						$guId = site_url() . '/?post_type=wpcr3_review&#038;p=' . $reviewId;
						wp_update_post(array('ID' => $reviewId, 'guid' => $guId));
						// Review meta information
						$review_meta_data = array(
							'wpcr3_review_ip'       => $data_array['review_ip'],
							'wpcr3_review_post'     => $data_array['review_post'],
							'wpcr3_review_name'     => $data_array['review_name'],
							'wpcr3_review_email'    => $data_array['review_email'],
							'wpcr3_review_rating'   => $data_array['review_rating'],
							'wpcr3_review_title'    => $data_array['review_title'],
							'wpcr3_review_website'  => $data_array['review_website'],
							'wpcr3_review_admin_response' => $data_array['review_admin_response'],
							'wpcr3_f1'  => $data_array['custom_field1'],
							'wpcr3_f2'  => $data_array['custom_field2'],
							'wpcr3_f3'  => $data_array['custom_field3'],
						);
						foreach($review_meta_data as $metaKey => $metaValue) {
							update_post_meta($reviewId, $metaKey, $metaValue);
						}
					} else {
						$query = "select id from $wpdb->wpcreviews where (review_title = '{$data_array['review_title']}') and (page_id = '{$data_array['review_post']}') ";
						$id_results = $wpdb->get_results( $query );
						$reviewId   = $id_results[0]->id;
						if ( $reviewId == null ) {
							$wpdb->insert( $wpdb->wpcreviews, array(
								'date_time'       => $data_array['date_time'],
								'review_name'   => $data_array['review_name'],
								'review_email'  => $data_array['review_email'],
								'review_ip'     => $data_array['review_ip'],
								'review_title'    => $data_array['review_title'],
								'review_text'     => $data_array['review_text'],
								'review_admin_response' => $data_array['review_admin_response'],
								'status'          => $data_array['status'],
								'review_rating'   => $data_array['review_rating'],
								'review_website'    => $data_array['review_website'],
								'page_id'         => $data_array['review_post']
							) );
								
							$reviewId = $wpdb->insert_id;
							$mode_of_affect = 'Inserted';

							$core_instance->detailed_log[$line_number]['Message'] = 'Inserted Review ID: ' . $reviewId;;
							$fields = $wpdb->get_results("UPDATE $log_table_name SET created = $created_count WHERE hash_key = '$hash_key'");
						} else {
							$wpdb->update( $wpdb->wpcreviews, array(
								'date_time'       => $data_array['date_time'],
								'review_name'   => $data_array['review_name'],
								'id'              => $reviewId,
								'review_email'  => $data_array['review_email'],
								'review_ip'     => $data_array['review_ip'],
								'review_title'    => $data_array['review_title'],
								'review_text'     => $data_array['review_text'],
								'review_admin_response' => $data_array['review_admin_response'],
								'status'          => $data_array['status'],
								'review_rating'   => $data_array['review_rating'],
								'review_website'    => $data_array['review_website'],
								'page_id'         => $data_array['review_post']
							) );
							$mode_of_affect = 'Updated';

							$core_instance->detailed_log[$line_number]['Message'] = 'Updated Review ID: ' . $reviewId;
							$fields = $wpdb->get_results("UPDATE $log_table_name SET updated = $updated_count WHERE hash_key = '$hash_key'");
						}
					}
				}
			}
		}
		return array('ID' => $reviewId, 'MODE' => $mode_of_affect);
    }
}