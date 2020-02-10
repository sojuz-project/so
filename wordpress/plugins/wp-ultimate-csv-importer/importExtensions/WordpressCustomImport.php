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

class WordpressCustomImport {
    private static $wordpress_custom_instance = null;

    public static function getInstance() {
		
		if (WordpressCustomImport::$wordpress_custom_instance == null) {
			WordpressCustomImport::$wordpress_custom_instance = new WordpressCustomImport;
			return WordpressCustomImport::$wordpress_custom_instance;
		}
		return WordpressCustomImport::$wordpress_custom_instance;
    }
    function set_wordpress_custom_values($header_array ,$value_array , $map, $post_id , $type){	
        $post_values = [];
        $helpers_instance = ImportHelpers::getInstance();
		$post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);
		
		$this->wordpress_custom_import_function($post_values, $post_id ,$type , 'off');
		
    }

    public function wordpress_custom_import_function ($data_array, $pID, $importType , $core_serialize_info) {
		
		global $wpdb;
		$createdFields = array();
		if(!empty($data_array)) {
            foreach ($data_array as $custom_key => $custom_value) {
                $createdFields[] = $custom_key;
                if( $importType != 'Users'){
					//POSTMETA
                    if( isset($core_serialize_info[$custom_key]) && $core_serialize_info[$custom_key] == 'on'){
					    //Check entry in postmeta table
		
                        $get_meta_info = $wpdb->get_results($wpdb->prepare("select meta_key,meta_value from {$wpdb->prefix}postmeta where post_id=%d and meta_key=%s" , $pID , $custom_key ), ARRAY_A);
                        if( !empty($get_meta_info)){
                            $wpdb->update($wpdb->prefix.'postmeta' , array('meta_value' => $custom_value ) , array('meta_key' => $custom_key , 'post_id' => $pID ));
                        }else{
                            $wpdb->insert($wpdb->prefix.'postmeta' , array('meta_key'=> $custom_key , 'meta_value' => $custom_value , 'post_id' => $pID ));
                        }
                    }else{	
                        					
                        update_post_meta($pID, $custom_key, $custom_value);
                    }
                }else{
                    //USERMETA
                    if( isset($core_serialize_info[$custom_key]) && $core_serialize_info[$custom_key] == 'on'){
						//Check entry in usermeta table
												
                        $get_meta_info = $wpdb->get_results($wpdb->prepare("select meta_key,meta_value from {$wpdb->prefix}usermeta where user_id=%d and meta_key=%s" , $pID , $custom_key ), ARRAY_A);
                        if( !empty($get_meta_info)){
                            $wpdb->update($wpdb->prefix.'usermeta' , array('meta_value' => $custom_value ) , array('meta_key' => $custom_key , 'user_id' => $pID ));
                        }else{
                            $wpdb->insert($wpdb->prefix.'usermeta' , array('meta_key'=> $custom_key , 'meta_value' => $custom_value , 'user_id' => $pID ));
                        }
                    }else{
                        update_user_meta($pID, $custom_key, $custom_value);
                    }
                }
            }
        }
		return $createdFields;

	}
}
