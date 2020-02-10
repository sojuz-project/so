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

class CMB2Extension extends ExtensionHandler{
	private static $instance = null;

    public static function getInstance() {
		
		if (CMB2Extension::$instance == null) {
			CMB2Extension::$instance = new CMB2Extension;
		}
		return CMB2Extension::$instance;
    }

	/**
	* Provides CMB2 mapping fields for specific post type
	* @param string $data - selected import type
	* @return array - mapping fields
	*/
    public function processExtension($data) {
        $response = [];
		$response['cmb2_fields'] = null;
    	return $response;	
    }

	/**
	* CMB2 extension supported import types
	* @param string $import_type - selected import type
	* @return boolean
	*/
    public function extensionSupportedImportType($import_type ){
		if(is_plugin_active('cmb2/init.php')){
			$import_type = $this->import_name_as($import_type);
			if($import_type == 'Posts' || $import_type == 'Pages' || $import_type == 'CustomPosts' || $import_type == 'event' || $import_type == 'event-recurring' ) {
				return true;
			}
			else{
				return false;
			}
		}
	}
}