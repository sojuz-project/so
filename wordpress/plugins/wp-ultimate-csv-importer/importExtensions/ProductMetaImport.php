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

class ProductMetaImport {
    private static $product_meta_instance = null;

    public static function getInstance() {
		
		if (ProductMetaImport::$product_meta_instance == null) {
			ProductMetaImport::$product_meta_instance = new ProductMetaImport;
			return ProductMetaImport::$product_meta_instance;
		}
		return ProductMetaImport::$product_meta_instance;
    }

    function set_product_meta_values($header_array ,$value_array , $map , $post_id ,$type , $line_number , $mode){
        global $wpdb;

        $woocommerce_meta_instance = WooCommerceMetaImport::getInstance();
		$helpers_instance = ImportHelpers::getInstance();
		$data_array = [];
			
		$data_array = $helpers_instance->get_header_values($map , $header_array , $value_array);
		
        if(($type == 'WooCommerce Product')){
            $woocommerce_meta_instance->woocommerce_meta_import_function($data_array, $post_id , $type , $line_number , $mode , $header_array, $value_array);
        }
    }

}