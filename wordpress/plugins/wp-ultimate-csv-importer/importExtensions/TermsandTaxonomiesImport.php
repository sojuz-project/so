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

class TermsandTaxonomiesImport {
	private static $terms_taxo_instance = null;

    public static function getInstance() {
		
		if (TermsandTaxonomiesImport::$terms_taxo_instance == null) {
			TermsandTaxonomiesImport::$terms_taxo_instance = new TermsandTaxonomiesImport;
			return TermsandTaxonomiesImport::$terms_taxo_instance;
		}
		return TermsandTaxonomiesImport::$terms_taxo_instance;
    }
    function set_terms_taxo_values($header_array ,$value_array , $map, $post_id , $type, $mode , $line_number){
		$post_values = [];
		$helpers_instance = ImportHelpers::getInstance();
		$post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);
		
		$this->terms_taxo_import_function($post_values,$type, $post_id , $mode , $line_number);
	
    }

    public function terms_taxo_import_function ($data_array, $type ,$pID , $mode , $line_number) {

		$core_instance = CoreFieldsImport::getInstance();
		$helpers_instance = ImportHelpers::getInstance();
		global $core_instance;
		
		unset($data_array['post_format']);
		unset($data_array['product_type']);
		$categories = $tags = array();
		foreach ($data_array as $termKey => $termVal) {
			$smack_taxonomy = array();
			switch ($termKey) {
				case 'post_category' :
					$categories [$termKey] = $data_array [$termKey];

					if(preg_match("(Can't|Skipped|Duplicate)", $core_instance->detailed_log[$line_number]['Message']) === 0) {  
						$core_instance->detailed_log[$line_number]['Categories'] = $data_array[$termKey];
					}

                    $category_name = 'category';

                    if($mode == 'Update'){
                        $categories_before = wp_get_object_terms($pID, 'category');
                    
                        foreach($categories_before as $category_before){
                            wp_remove_object_terms($pID, $category_before->name , 'category');
                            
                        }
                    }

					// Create / Assign categories to the post types
					if(isset($categories[$termKey]) && $categories[$termKey] != '')
						$this->assignTermsAndTaxonomies($categories, $category_name, $pID);
					//Get Default Category id
                    $default_category_id = get_option('default_category');
                   
					//Get Default Category Name
                    $default_category_details = get_term_by('id', $default_category_id , 'category');
                    
					//Remove Default Category
                    $categories = wp_get_object_terms($pID, 'category');
            
					if (count($categories) > 1) {
						foreach ($categories as $key => $category) {
							if ($category->name == $default_category_details->name ) {
								wp_remove_object_terms($pID, $default_category_details->name , 'category');
							}
						}
					}
					break;
				case 'post_tag' :
					$tags [$termKey] = $data_array [$termKey];
					
					if(preg_match("(Can't|Skipped|Duplicate)", $core_instance->detailed_log[$line_number]['Message']) === 0){
						$core_instance->detailed_log[$line_number]['Tags'] = $data_array[$termKey];
					}
					$tag_name = 'post_tag';
					break;
				case 'product_tag':
					$tags [$termKey] = $data_array [$termKey];

					if(preg_match("(Can't|Skipped|Duplicate)", $core_instance->detailed_log[$line_number]['Message']) === 0){
						$core_instance->detailed_log[$line_number]['Tags'] = $data_array[$termKey];
					}	
					$tag_name = 'product_tag';
					break;
				case 'product_category':
					if($type === 'MarketPress Product')
						$category_name = 'product_category';
					if($type == 'WooCommerce Product')
						$category_name = 'product_cat';
					if($type == 'WPeCommerce Products')
						$category_name = 'wpsc_product_category';
						else
					$category_name = 'product_cat';
					$categories [$termKey] = $data_array [$termKey];

					if(preg_match("(Can't|Skipped|Duplicate)", $core_instance->detailed_log[$line_number]['Message']) === 0){
						$core_instance->detailed_log[$line_number]['Categories'] = $data_array[$termKey];
					}

					// Create / Assign categories to the post types
					if(isset($categories[$termKey]) && $categories[$termKey] != '')
						$this->assignTermsAndTaxonomies($categories, $category_name, $pID);
					break;
				case 'event_tags':
					$eventtags [$termKey] = $data_array [$termKey];
					if(!empty($eventtags)){

						if(preg_match("(Can't|Skipped|Duplicate)", $core_instance->detailed_log[$line_number]['Message']) === 0){
							$core_instance->detailed_log[$line_number]['Tags'] = $data_array[$termKey];
						}
						
						foreach($eventtags as $e_key => $e_value){
							if(!empty($e_value)){
								if (strpos($e_value, ',') !== false) {
									$split_etag = explode(',', $e_value);
								
								} else {
									$split_etag = $e_value;
								}
								if(is_array($split_etag)) {
									foreach($split_etag as $item) {
										$etagData[] = (string)$item;
									}
								} else {
									$etagData = (string)$split_etag;
								}
								wp_set_object_terms($pID, $etagData,'event-tags');
							}
						}
					}
					break;
				case 'event_categories':
					$event_categories [$termKey] = $data_array [$termKey];
					if(!empty($event_categories)) {
						
						if(preg_match("(Can't|Skipped|Duplicate)", $core_instance->detailed_log[$line_number]['Message']) === 0){
							$core_instance->detailed_log[$line_number]['Categories'] = $data_array[$termKey];
						}

						foreach($event_categories as $ec_key => $ec_value){
							if(!empty($ec_value)) {
								if (strpos($ec_value, ',') !== false) {
									$split_ecat = explode(',', $ec_value);
								
								} else {
									$split_ecat = $ec_value;
								}
								if(is_array($split_ecat)) {
									foreach($split_ecat as $item) {
										$ecatData[] = (string)$item;
									}
								} else {
									$ecatData = (string)$split_ecat;
								}
								wp_set_object_terms($pID, $ecatData,'event-categories');
							}
						}
					}
					break;
				default :
					$smack_taxonomy[$termKey] = $data_array[$termKey];

					if($termKey != 'post_format')
					$core_instance->detailed_log[$line_number][$termKey] = $data_array[$termKey];

					$taxonomy_name = $termKey;

					// Create / Assign taxonomies to the post types
					if(isset($smack_taxonomy[$termKey]) && $smack_taxonomy[$termKey] != '')
						$this->assignTermsAndTaxonomies($smack_taxonomy, $taxonomy_name, $pID);
					break;
			}
		}

		// Create / Assign tags to the post types
		if (!empty ($tags)) {
			foreach ($tags as $tag_key => $tag_value) {
				if (!empty($tag_value)) {
					if (strpos($tag_value, ',') !== false) {
						$split_tag = explode(',', $tag_value); 
					} else {
						$split_tag = $tag_value;
					}
					if(is_array($split_tag)) {
						foreach($split_tag as $item) {
							$tag_list[] = $item;
						}
					} else {
						$tag_list = $split_tag;
					}
					wp_set_object_terms($pID, $tag_list, $tag_name);
				}
			}
		}
    }
    
    public function assignTermsAndTaxonomies($categories, $category_name, $pID) {
		$get_category_list = $category_list = array();
		// Create / Assign categories to the post types
		if (!empty($categories)) {
    
			foreach ( $categories as $cat_key => $cat_value ) {
				if (strpos($cat_value, ',') !== false) {
					$get_category_list = explode(',', $cat_value);
				} else {
					$get_category_list[] = $cat_value;
				}
            }
        
		}
		if(!empty($get_category_list)) {
			$i = 0;
			foreach($get_category_list as $key => $value) {
				if (strpos($value, '>') !== false) {
					$split_line = explode('>', $value);
					if(is_array($split_line)) {
						foreach($split_line as $category) {
							$category_list[$i][] = $category;
						}
					}
				} else {
					$category_list[$i][] = $value;
				}
				$i++;
            }

		}
		foreach($category_list as $index => $category_set) {
        
			foreach ( $category_set as $item => $category_value ) {
				$term_children_options= get_option( "$category_name" . "_children" );
				$parentTerm           = $item;
				$termName             = trim( $category_value );
				$_name                = (string) $termName;
				$_slug                = preg_replace( '/\s\s+/', '-', strtolower( $_name ) );
				$checkAvailable       = array();
				$checkSuperParent     = $checkParent1 = $checkParent2 = null;
				$super_parent_term_id = $parent_term_id1 = $parent_term_id2 = 0;
				if ( $parentTerm != 0 ) {
            
					if ( isset( $category_set[ $item - 1 ] ) ) {
						$checkParent1 = trim( $category_set[ $item - 1 ] );
						$checkParent1 = (string) $checkParent1;
						$parent_term  = term_exists( "$checkParent1", "$category_name" );
						if ( isset( $parent_term['term_id'] ) ) {
							$parent_term_id1 = $parent_term['term_id'];
                        }
                        
					}
					if ( isset( $category_set[ $item - 2 ] ) ) {
						$parent_term_id1   = 0;
						$checkSuperParent  = trim( $category_set[ $item - 2 ] );
						$checkSuperParent  = (string) $checkSuperParent;
						$super_parent_term = term_exists( "$checkSuperParent", "$category_name" );
						if ( isset( $super_parent_term['term_id'] ) ) {
							$super_parent_term_id = $super_parent_term['term_id'];
						}
						$checkParent2 = trim( $category_set[ $item - 1 ] );
						$checkParent2 = (string) $checkParent2;
						$parent_term  = term_exists( "$checkParent2", "$category_name", $super_parent_term_id );
						if ( isset( $parent_term['term_id'] ) ) {
							$parent_term_id2 = $parent_term['term_id'];
                        }
                
					}
				}
				if ( $super_parent_term_id != 0 ) {
        
					if ( $parent_term_id2 == 0 ) {
                        $checkAvailable = term_exists( "$checkParent2", "$category_name" );
            
						if ( ! is_array( $checkAvailable ) ) {
							$taxonomyID          = wp_insert_term( "$checkParent2", "$category_name", array(
								'description' => '',
								'slug'        => $_slug,
								'parent'      => $super_parent_term_id
							) );

							if(!is_wp_error($taxonomyID)){
								$parent_term_id2 = $retID = $taxonomyID['term_id'];
								wp_set_object_terms( $pID, $retID, $category_name, true );
							}
                            
						} else {
							$exist_term_id = array( $checkAvailable['term_id'] );
							$exist_term_id = array_map( 'intval', $exist_term_id );
							$exist_term_id = array_unique( $exist_term_id );
							$parent_term_id2 = $checkAvailable['term_id'];
                            wp_set_object_terms( $pID, $exist_term_id, $category_name, true );
                            
						}
					}
					unset( $checkAvailable );
					$checkAvailable = term_exists( "$_name", "$category_name", $parent_term_id2 );
					if ( ! is_array( $checkAvailable ) ) {
						$taxonomyID = wp_insert_term( "$_name", "$category_name", array(
							'description' => '',
							'slug'        => $_slug,
							'parent'      => $parent_term_id2
						) );

						if(!is_wp_error($taxonomyID)){
							$retID  = $taxonomyID['term_id'];
                        	wp_set_object_terms( $pID, $retID, $category_name, true );
						}
						    
					} else {
						$exist_term_id = array( $checkAvailable['term_id'] );
						$exist_term_id = array_map( 'intval', $exist_term_id );
						$exist_term_id = array_unique( $exist_term_id );
                        wp_set_object_terms( $pID, $exist_term_id, $category_name, true );
                        
					}
					unset( $checkAvailable );
				}
				elseif ( $parent_term_id1 != 0 ) {
                
                    $checkAvailable = term_exists( "$_name", "$category_name", $parent_term_id1 );
					if ( ! is_array( $checkAvailable ) ) {
                
						$taxonomyID = wp_insert_term( "$_name", "$category_name", array(
							'description' => '',
							'slug'        => $_slug,
							'parent'      => $parent_term_id1
						) );

						if(!is_wp_error($taxonomyID)){
							$retID  = $taxonomyID['term_id'];
                        	wp_set_object_terms( $pID, $retID, $category_name, true );
						}    
                        
					} else {
                    
						$exist_term_id = array( $checkAvailable['term_id'] );
						$exist_term_id = array_map( 'intval', $exist_term_id );
						$exist_term_id = array_unique( $exist_term_id );
                        wp_set_object_terms( $pID, $exist_term_id, $category_name, true );
                        
					}
					unset( $checkAvailable );
				}
				elseif ( $super_parent_term_id == 0 && $parent_term_id2 == 0 && $parent_term_id1 == 0 ) {
					$checkAvailable = term_exists( "$_name", "$category_name" );
					if ( !is_array( $checkAvailable ) ) {
						$taxonomyID = wp_insert_term( "$_name", "$category_name", array(
							'description' => '',
							'slug'        => $_slug,
						) );

						if(!is_wp_error($taxonomyID)){
							$retID  = $taxonomyID['term_id'];
                        	wp_set_object_terms( $pID, $retID, $category_name, true );
						}	
                        
					} else {
						$exist_term_id = array( $checkAvailable['term_id'] );
						$exist_term_id = array_map( 'intval', $exist_term_id );
						$exist_term_id = array_unique( $exist_term_id );
                        wp_set_object_terms( $pID, $exist_term_id, $category_name, true );
                        
					}
					unset( $checkAvailable );
				}
				#if ( ! is_wp_error( $retID ) ) {
				update_option( "$category_name" . "_children", $term_children_options );
				delete_option( $category_name . "_children" );
				#}
				$categoryData[] = (string) $category_value;
			}
		}
        
		return $categoryData;
	}
}