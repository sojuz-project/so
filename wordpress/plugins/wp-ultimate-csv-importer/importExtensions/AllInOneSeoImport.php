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

class AllInOneSeoImport {
    private static $all_seo_instance = null;

    public static function getInstance() {
		
		if (AllInOneSeoImport::$all_seo_instance == null) {
			AllInOneSeoImport::$all_seo_instance = new AllInOneSeoImport;
			return AllInOneSeoImport::$all_seo_instance;
		}
		return AllInOneSeoImport::$all_seo_instance;
    }
    function set_all_seo_values($header_array ,$value_array , $map, $post_id , $type){

		$post_values = [];
		$helpers_instance = ImportHelpers::getInstance();	
		$post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);
		
		$this->all_seo_import_function($post_values,$type, $post_id);
        
    }

    function all_seo_import_function($data_array, $importas,$pID) {
		$createdFields = array();
		foreach($data_array as $dkey => $dvalue) {
			$createdFields[] = $dkey;
		}
		if(isset($data_array['keywords'])) {
			$custom_array['_aioseop_keywords'] = $data_array['keywords'];
		}
		if(isset($data_array['description'])) {
			$custom_array['_aioseop_description'] = $data_array['description'];
		}
		if(isset($data_array['title'])) {
			$custom_array['_aioseop_title'] = $data_array['title'];
		}
		if(isset($data_array['noindex'])) {
			$custom_array['_aioseop_noindex'] = $data_array['noindex'];
		}
		if(isset($data_array['nofollow'])) {
			$custom_array['_aioseop_nofollow'] = $data_array['nofollow'];
		}
		if(isset($data_array['custom_link'])) {
			$custom_array['_aioseop_custom_link'] = $data_array['custom_link'];
		}
		if(isset($data_array['noodp'])) {
			$custom_array['_aioseop_noodp'] = $data_array['noodp'];
		}
		if(isset($data_array['noydir'])) {
			$custom_array['_aioseop_noydir'] = $data_array['noydir'];
		}
		if(isset($data_array['titleatr'])) {
			$custom_array['_aioseop_titleatr'] = $data_array['titleatr'];
		}
		if(isset($data_array['menulabel'])) {
			$custom_array['_aioseop_menulabel'] = $data_array['menulabel'];
		}
		if(isset($data_array['disable'])) {
			$custom_array['_aioseop_disable'] = $data_array['disable'];
			if($data_array['disable'] == 'off'){
				unset($custom_array['_aioseop_disable']);
			}
		}
		if(isset($data_array['disable_analytics'])) {
			$custom_array['_aioseop_disable_analytics'] = $data_array['disable_analytics'];
			if($data_array['disable_analytics'] == 'off'){
				unset($custom_array['_aioseop_disable_analytics']);
			}
		}
		if(!empty ($custom_array)) {
			foreach ($custom_array as $custom_key => $custom_value) {
				update_post_meta($pID, $custom_key, $custom_value);
			}
		}
		return $createdFields;
	}
    
}