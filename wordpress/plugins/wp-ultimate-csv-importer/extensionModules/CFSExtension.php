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

class CFSExtension extends ExtensionHandler{
	private static $instance = null;

    public static function getInstance() {
		
		if (CFSExtension::$instance == null) {
			CFSExtension::$instance = new CFSExtension;
		}
		return CFSExtension::$instance;
    }

	/**
	* Provides CFS mapping fields for specific post type
	* @param string $data - selected import type
	* @return array - mapping fields
	*/
    public function processExtension($data) {
        $response = []; 
        global $wpdb;
		$customFields = $cfs_field = array();
		$get_cfs_groups = $wpdb->get_results($wpdb->prepare("select ID from {$wpdb->prefix}posts where post_type = %s and post_status = %s", 'cfs' , 'publish'),ARRAY_A);
		$group_id_arr = [];
		foreach ( $get_cfs_groups as $item => $group_rules ) {
			$group_id_arr[] .= $group_rules['ID'] . ',';
		}
			
		if($group_id_arr != '') {
			foreach($group_id_arr as $group_id){	
				$get_cfs_fields[]= $wpdb->get_results( $wpdb->prepare("SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE post_id IN (%s) and meta_key =%s ",$group_id,'cfs_fields'), ARRAY_A);		
			}
		}
		// Available CFS fields
		if (!empty($get_cfs_fields)) {
			foreach ($get_cfs_fields as $key => $value) {
				$get_cfs_field = @unserialize($value[0]['meta_value']);
				foreach($get_cfs_field as $fk => $fv){
					$customFields["CFS"][$fv['name']]['label'] = $fv['label'];
					$customFields["CFS"][$fv['name']]['name'] = $fv['name'];
					$cfs_field[] = $fv['name'];
				}
			}
		}
		$cfs_value = $this->convert_fields_to_array($customFields);
		$response['custom_fields_suite_fields'] =  $cfs_value;
		return $response;	
    }

	/**
	* CFS extension supported import types
	* @param string $import_type - selected import type
	* @return boolean
	*/
    public function extensionSupportedImportType($import_type ){
		if(is_plugin_active('custom-field-suite/cfs.php')){
			$import_type = $this->import_name_as($import_type);
			if($import_type == 'Posts' || $import_type == 'Pages' || $import_type == 'CustomPosts' || $import_type == 'event' || $import_type == 'event-recurring') {
				return true;
			}
			else{
				return false;
			}
		}
	}
}