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

class CCTMExtension extends ExtensionHandler{
	private static $instance = null;

    public static function getInstance() {
		
		if (CCTMExtension::$instance == null) {
			CCTMExtension::$instance = new CCTMExtension;
		}
		return CCTMExtension::$instance;
    }

	/**
	* Provides CCTM mapping fields for specific post type
	* @param string $data - selected import type
	* @return array - mapping fields
	*/
    public function processExtension($data) {
        $response = []; 
		$response['cctm_fields'] =  null;
		return $response;		
	}
	
	/**
	* CCTM extension supported import types
	* @param string $import_type - selected import type
	* @return boolean
	*/
    public function extensionSupportedImportType($import_type ){
		if(is_plugin_active('custom-content-type-manager/index.php')){
			$import_type = $this->import_name_as($import_type);
			if($import_type =='Posts' || $import_type =='Pages' || $import_type =='CustomPosts' || $import_type =='event' || $import_type =='location' || $import_type == 'event-recurring' || $import_type =='Users' || $import_type =='WooCommerce' || $import_type =='MarketPress' || $import_type =='WPeCommerce' || $import_type =='eShop') {		
				return true;
			}
			else{
				return false;
			}
		}
    }
}