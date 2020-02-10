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

class WordpressCustomExtension extends ExtensionHandler{
    private static $instance = null;

    public static function getInstance() {
		
		if (WordpressCustomExtension::$instance == null) {
			WordpressCustomExtension::$instance = new WordpressCustomExtension;
		}
		return WordpressCustomExtension::$instance;
    }

    /**
	* Provides Wordpress Custom fields for specific post type
	* @param string $data - selected import type
	* @return array - mapping fields
	*/
    public function processExtension($data) {		
        global $wpdb;
        $import_types = $data;

        $import_type = $this->import_type_as($import_types);
        $response =[];
        $module = $this->import_post_types($import_type);
        $acf_values = [];
        $acf_values = array('admin_color', 'comment_shortcuts', 'community-events-location', 'dbem_phone', 'health-check', 'first_name', 'last_name', 'last_update', 'locale',
                            'nickname', 'orderby', 'rich_editing', 'syntax_highlighting', 'toolset-rg-view', 'username', 'use_ssl', 'session_tokens', 'smack_uci_import', 'description');

        $get_acf_groups = $wpdb->get_results( $wpdb->prepare("SELECT ID, post_content FROM {$wpdb->prefix}posts WHERE post_status != 'trash' AND post_type = %s", 'acf-field-group'));
		foreach ( $get_acf_groups as $item => $group_rules ) {
			$rule = maybe_unserialize($group_rules->post_content);
			
			if(!empty($rule)) {
				if ($import_types != 'Users') {
					foreach($rule['location'] as $key => $value) {
						if($value[0]['operator'] == '==' && $value[0]['value'] == $this->import_post_types($import_types)){	
							$group_id_arr[] = $group_rules->ID; #. ',';
						}
						elseif($value[0]['operator'] == '==' && $value[0]['value'] == 'all' && $value[0]['param'] == 'taxonomy' && in_array($this->import_post_types($import_types) , get_taxonomies())){
							$group_id_arr[] = $group_rules->ID;
						}
					}
				} else {
					foreach($rule['location'] as $key => $value) {
						if( $value[0]['operator'] == '==' && $value[0]['param'] == 'user_role'){
							$group_id_arr[] = $group_rules->ID;
						}
					}
				}
			}
		}
        if ( !empty($group_id_arr) ) {	
			foreach($group_id_arr as $groupId) {	
				$get_acf_fields = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_title, post_content, post_excerpt, post_name FROM {$wpdb->prefix}posts where post_status != 'trash' AND post_parent in (%s)", array($groupId) ) );				
				if ( ! empty( $get_acf_fields ) ) {						
					foreach ( $get_acf_fields as $acf_pro_fields ) {
						$acf_values[] = $acf_pro_fields->post_excerpt;   
                    }
                }
            }   
        }

        $acf = [];
        $get_acf_fields = $wpdb->get_results("SELECT post_excerpt FROM {$wpdb->prefix}posts where post_type = 'acf-field' ");
        foreach($get_acf_fields as $acf_fields){
            $acf[] = $acf_fields->post_excerpt;
        }

        $pods = [];
        $get_pods_fields = $wpdb->get_results("SELECT post_name FROM {$wpdb->prefix}posts where post_type = '_pods_field' ");
        
        foreach($get_pods_fields as $pods_fields){
            $pods[] = $pods_fields->post_name;  
        }
  
        $commonMetaFields = array();
        
        if($module != 'user') {   
            $keys = $wpdb->get_col( "SELECT pm.meta_key FROM {$wpdb->prefix}posts p
                                    JOIN {$wpdb->prefix}postmeta pm
                                    ON p.ID = pm.post_id
                                    WHERE p.post_type = '{$module}' AND NOT p.post_status = 'trash'
                                    GROUP BY meta_key
                                    HAVING meta_key NOT LIKE '\_%' and meta_key NOT LIKE 'field_%' and meta_key NOT LIKE 'wpcf-%' and meta_key NOT LIKE 'wpcr3_%' and meta_key NOT LIKE '%pods%' and meta_key NOT LIKE '%group_%' and meta_key NOT LIKE '%repeat_%' and meta_key NOT LIKE 'mp_%'
                                    ORDER BY meta_key" );
                                    
        } else {
            $keys = $wpdb->get_col( "SELECT um.meta_key FROM {$wpdb->prefix}users u
                                    JOIN {$wpdb->prefix}usermeta um
                                    ON u.ID = um.user_id
                                    GROUP BY meta_key
                                    HAVING meta_key NOT LIKE '\_%' and meta_key NOT LIKE 'field_%' and meta_key NOT LIKE 'wpcf-%' and meta_key NOT LIKE 'wpcr3_%' and meta_key NOT LIKE '%pods%' and meta_key NOT LIKE '%group_%' and meta_key NOT LIKE '%repeat_%' 
                                    and meta_key NOT LIKE 'closedpostboxes_%' and meta_key NOT LIKE 'metaboxhidden_%' and meta_key NOT LIKE 'billing_%' and meta_key NOT LIKE 'aioseop_%' and meta_key NOT LIKE 'dismissed_%' and meta_key NOT LIKE 'manageedit-%'
                                    and meta_key NOT LIKE 'wp_%' and meta_key NOT LIKE 'wc_%' and meta_key NOT LIKE 'mp_%' and meta_key NOT LIKE 'shipping_%' and meta_key NOT LIKE 'show_%' and meta_key NOT LIKE 'acf_%' and meta_key NOT LIKE 'user_%'
                                    ORDER BY meta_key" );                             
        }

        foreach ($keys as $val) {
            if(!in_array($val , $acf_values) && !empty($val) && !in_array($val , $pods) && !in_array($val , $acf)){
                $commonMetaFields['CORECUSTFIELDS'][$val]['label'] = $val;
                $commonMetaFields['CORECUSTFIELDS'][$val]['name'] = $val;
            }
        }
        
        $wp_custom_value = $this->convert_fields_to_array($commonMetaFields);
        $response['wordpress_custom_fields'] = $wp_custom_value ;
		return $response;	
    
    }

    /**
	* Wordpress Custom extension supported import types
	* @param string $import_type - selected import type
	* @return boolean
	*/
    public function extensionSupportedImportType($import_type){
        $import_type = $this->import_name_as($import_type);
        if($import_type == 'Posts' || $import_type == 'Pages' || $import_type == 'CustomPosts' || $import_type == 'Users' || $import_type == 'WooCommerce') {
			return true;
        }
    }
}
