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

class ToolsetExtension extends ExtensionHandler{
	private static $instance = null;

    public static function getInstance() {
		
		if (ToolsetExtension::$instance == null) {
			ToolsetExtension::$instance = new ToolsetExtension;
		}
		return ToolsetExtension::$instance;
    }

	/**
	* Provides Toolset fields for specific post type
	* @param string $data - selected import type
	* @return array - mapping fields
	*/
	public function processExtension($data) {
		global $wpdb;
		$response = [];
		$response['types_fields'] = null;
		return $response;		
			
	}
	
	/**
	* Toolset extension supported import types
	* @param string $import_type - selected import type
	* @return boolean
	*/
	public function extensionSupportedImportType($import_type ){
		if(is_plugin_active('types/wpcf.php')){
			$import_type = $this->import_name_as($import_type);
			if($import_type == 'Posts' || $import_type == 'Pages' || $import_type == 'CustomPosts' || $import_type == 'Users' || $import_type == 'WooCommerce' || $import_type == 'Taxonomies' || $import_type == 'Categories' || $import_type == 'Tags' ) {
				return true;
			}
			else{
				return false;
			}
		}
	}
}