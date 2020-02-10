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

class EventsManagerExtension extends ExtensionHandler{
    private static $instance = null;
	
    public static function getInstance() {
		
		if (EventsManagerExtension::$instance == null) {
			EventsManagerExtension::$instance = new EventsManagerExtension;
		}
		return EventsManagerExtension::$instance;
    }

	/**
	* Provides Events Manager mapping fields for specific post type
	* @param string $data - selected import type
	* @return array - mapping fields
	*/
    public function processExtension($data) {
		$response = [];
		$response['events_manager_fields'] = null;
		return $response;	
    }

	/**
	* Events Manager extension supported import types
	* @param string $import_type - selected import type
	* @return boolean
	*/
    public function extensionSupportedImportType($import_type){
		
		if(is_plugin_active('events-manager/events-manager.php')){
			$import_type = $this->import_name_as($import_type);
			
			if($import_type =='event' || $import_type =='location' || $import_type == 'event-recurring' || $import_type == 'ticket') {				
				return true;
			}
			else{
				return false;
			}
		}
	}
}