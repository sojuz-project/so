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

class CFSImport {
    private static $cfs_instance = null;

    public static function getInstance() {
		
		if (CFSImport::$cfs_instance == null) {
			CFSImport::$cfs_instance = new CFSImport;
			return CFSImport::$cfs_instance;
		}
		return CFSImport::$cfs_instance;
    }
    function set_cfs_values($header_array ,$value_array , $map, $post_id , $type){
		
		$post_values = [];
		$helpers_instance = ImportHelpers::getInstance();	
		$post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);
		
		$this->cfs_import_function($post_values, $post_id);

    }

    public function cfs_import_function ($data_array, $pID) {
		
		global $wpdb;
		$helpers_instance = ImportHelpers::getInstance();
		$media_instance = MediaHandling::getInstance();
		$cfs_data = $this->CFSFields();
		foreach ($data_array as $dkey => $dvalue) {
			if(array_key_exists($dkey,$cfs_data['CFS'])){
				if($cfs_data['CFS'][$dkey]['type'] == 'hyperlink'){
					$linksfields = explode('|', $dvalue);
					$linksarr['url'] = $linksfields[0];
					$linksarr['text'] = $linksfields[1];
					$linksarr['target'] = $linksfields[2];
					$darray[$cfs_data['CFS'][$dkey]['name']] = $linksarr;
				}
				elseif($cfs_data['CFS'][$dkey]['type'] == 'file'){
					$darray[$cfs_data['CFS'][$dkey]['name']] = $media_instance->media_handling($dvalue, $pID);
				}elseif($cfs_data['CFS'][$dkey]['type'] == 'select'){
					if( strpos($dvalue, ',') !== false )
					{
						$multifields = explode(',', $dvalue);
						foreach($multifields as $mk => $mv){
							$meta_id = add_post_meta($pID, $cfs_data['CFS'][$dkey]['name'], $mv);
							$this->insert_cfs_values($cfs_data,$pID,$meta_id,$cfs_data['CFS'][$dkey]['name']);
						}
					}else{
						$darray[$cfs_data['CFS'][$dkey]['name']] = $dvalue;
					}
				}elseif($cfs_data['CFS'][$dkey]['type'] == 'relationship'){
					$relations = explode(',', $dvalue);
					foreach($relations as $rk => $rv){
						$relationid = $wpdb->get_col($wpdb->prepare("select ID from {$wpdb->prefix}posts where post_title = %s and post_type != %s",$rv,'revision'));
						$meta_id = add_post_meta($pID, $cfs_data['CFS'][$dkey]['name'], $relationid[0]);
						$this->insert_cfs_values($cfs_data,$pID,$meta_id,$cfs_data['CFS'][$dkey]['name']);
					}
				}
				elseif($cfs_data['CFS'][$dkey]['type'] == 'term'){
					$relationterms = explode(',', $dvalue);
					foreach($relationterms as $rtk => $rtv){
						$term = get_term_by('name',$rtv,'category');
						$termid = $term->term_id;
						$meta_id = add_post_meta($pID, $cfs_data['CFS'][$dkey]['name'], $termid);
						$this->insert_cfs_values($cfs_data,$pID,$meta_id,$cfs_data['CFS'][$dkey]['name']);
					}
				}elseif($cfs_data['CFS'][$dkey]['type'] == 'user'){
					$users = explode(',', $dvalue);
					foreach($users as $uk => $uv){
						$userdata = $helpers_instance->get_from_user_details($uv);
						$meta_id = add_post_meta($pID, $cfs_data['CFS'][$dkey]['name'], $userdata['user_id']);
						$this->insert_cfs_values($cfs_data,$pID,$meta_id,$cfs_data['CFS'][$dkey]['name']);
					}
				}
				else{
					$darray[$dkey] = $dvalue;
				}
			}
		}
		if($darray){
			foreach($darray as $mkey => $mval){
				$metaid = update_post_meta($pID, $mkey, $mval);
				$this->insert_cfs_values($cfs_data,$pID,$metaid,$mkey);
			}
		}

	}

	public function insert_cfs_values($cfs_data,$pID,$metaid,$mkey) {
		global $wpdb;
		$wpdb->insert($wpdb->prefix.'cfs_values',
			array('field_id' => $cfs_data['CFS'][$mkey]['fieldid'],
			      'meta_id' => $metaid,
			      'post_id' => $pID,
			),
			array('%s','%s','%s')
		);
	}

	public function CFSFields(){
		global $wpdb;
		$customFields = $cfs_field = array();
		$get_cfs_groups = $wpdb->get_results($wpdb->prepare("select ID from {$wpdb->prefix}posts where post_type = %s", 'cfs'),ARRAY_A);
		$group_id_arr = '';
		foreach ( $get_cfs_groups as $item => $group_rules ) {
			$group_id_arr .= $group_rules['ID'] . ',';
		}
		if($group_id_arr != '') {
			$group_id_arr = substr( $group_id_arr, 0, - 1 );
			// Get available CFS fields based on the import type and group id
			$get_cfs_fields = $wpdb->get_results( $wpdb->prepare("SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE post_id IN (%s) and meta_key =%s ",$group_id_arr,'cfs_fields'), ARRAY_A);
		}
		// Available CFS fields
		if (!empty($get_cfs_fields)) {
			foreach ($get_cfs_fields as $key => $value) {
				$get_cfs_field = @unserialize($value['meta_value']);
				foreach($get_cfs_field as $fk => $fv){
					$customFields["CFS"][$fv['name']]['label'] = $fv['label'];
					$customFields["CFS"][$fv['name']]['name'] = $fv['name'];
					$customFields["CFS"][$fv['name']]['type'] = $fv['type'];
					$customFields["CFS"][$fv['name']]['fieldid'] = $fv['id'];
					$cfs_field[] = $fv['name'];
				}
			}
		}
		return $customFields;
	}

}