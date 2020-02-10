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

class YoastSeoExtension extends ExtensionHandler{
	private static $instance = null;

    public static function getInstance() {
		
		if (YoastSeoExtension::$instance == null) {
			YoastSeoExtension::$instance = new YoastSeoExtension;
		}
		return YoastSeoExtension::$instance;
    }

	/**
	* Provides Yoast Seo fields for specific post type
	* @param string $data - selected import type
	* @return array - mapping fields
	*/
    public function processExtension($data) {
      $response = [];
			$response['yoast_seo_fields'] = null;
			return $response;
    }

	/**
	* Yoast Seo extension supported import types
	* @param string $import_type - selected import type
	* @return boolean
	*/
    public function extensionSupportedImportType($import_type ){
		if(is_plugin_active('wordpress-seo/wp-seo.php') || is_plugin_active('wordpress-seo-premium/wp-seo-premium.php')){
			$import_type = $this->import_name_as($import_type);
			if($import_type == 'Posts' || $import_type == 'Pages' || $import_type == 'CustomPosts' || $import_type == 'WooCommerce' || $import_type == 'Taxonomies' || $import_type == 'Tags' || $import_type == 'Categories' ) {	
				return true;
			}
			else{
				return false;
			}
		}
	}
}