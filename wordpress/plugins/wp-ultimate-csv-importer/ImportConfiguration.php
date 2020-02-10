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
    
class ImportConfiguration {
    private static $import_config_instance = null;

    private function __construct(){
		add_action('wp_ajax_updatefields',array($this,'get_update_fields'));
    }
    
    public static function getInstance() {
            
        if (ImportConfiguration::$import_config_instance == null) {
            ImportConfiguration::$import_config_instance = new ImportConfiguration;
            return ImportConfiguration::$import_config_instance;
        }
        return ImportConfiguration::$import_config_instance;
    }

    public function get_update_fields(){
	$import_type = $_POST['Types'];	
        $mode = $_POST['Mode'];
        $response = [];
		$taxonomies = get_taxonomies(); 
		if($mode == 'Update') {
			$fields = array( 'post_title', 'ID', 'post_name' , );
			if($import_type == 'WooCommerce Orders'){
				$fields = array('ORDERID');
			}	
			if($import_type == 'WooCommerce Coupons' || $import_type =='WPeCommerce Coupons'){
				$fields =  array('COUPONID');
			}
			if($import_type == 'WooCommerce Refunds'){
				$fields = array('REFUNDID');
			}
			if($import_type == 'WooCommerce Product Variations'){
				$fields = array('VARIATIONSKU', 'VARIATIONID');
			}
			if($import_type == 'WooCommerce Product'){
				array_push($fields,"PRODUCTSKU");
            }
			if($import_type == 'Customer Reviews'){
				$fields = array('review_id');
			}
			elseif (in_array($import_type, $taxonomies)){
				$fields = array('slug','termid');
			}
			elseif($import_type == 'Users'){
				$fields = array('user_email','ID');
			}
		}
		else {
			if (in_array($import_type, $taxonomies)){
				$fields = array('slug');
			}
			if($import_type == 'WooCommerce Product Variations'){
				if(is_plugin_active('woocommerce/woocommerce.php') && is_plugin_active('import-woocommerce/import-woocommerce.php')){
					$fields = array('VARIATIONSKU');
				}
			}
			elseif($import_type == 'Users'){
				$fields = array('user_email');
			}
			else{
				$fields = array( 'ID', 'post_title', 'post_name' );
		    }
		}
		
        $response['update_fields'] = $fields;
        echo wp_json_encode($response);
        wp_die();
		
    }

    public function get_active_plugins() {
	$active_plugins = get_option('active_plugins');
	return $active_plugins;
    }
}