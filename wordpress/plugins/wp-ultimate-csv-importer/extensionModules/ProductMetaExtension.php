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

class ProductMetaExtension extends ExtensionHandler{
    private static $instance = null;

    public static function getInstance() {
		
		if (ProductMetaExtension::$instance == null) {
            ProductMetaExtension::$instance = new ProductMetaExtension;
		}
		return ProductMetaExtension::$instance;
    }

    /**
	* Provides Product Meta fields for specific post type
	* @param string $data - selected import type
	* @return array - mapping fields
	*/
    public function processExtension($data){
        $import_type = $data;
        $response = [];
        $import_type = $this->import_type_as($import_type);
        if(is_plugin_active('woocommerce/woocommerce.php')){    
            if($import_type == 'WooCommerce'){
                $pro_meta_fields = array(
                            'Product Shipping Class' => 'product_shipping_class',
                            'Visibility' => 'visibility',
                            'Tax Status' => 'tax_status',
                            'Product Type' => 'product_type',
                            'Product Attribute Name' => 'product_attribute_name',
                            'Product Attribute Value' => 'product_attribute_value',
                            'Product Attribute Visible' => 'product_attribute_visible',
                            'Product Attribute Variation' => 'product_attribute_variation',
                            'Product Attribute Position' => 'product_attribute_position',
                            'Featured Product' => 'featured_product',
                            'Product Attribute Taxonomy' => 'product_attribute_taxonomy',
                            'Tax Class' => 'tax_class',
                            'File Paths' => 'file_paths',
                            'Edit Last' => 'edit_last',
                            'Edit Lock' => 'edit_lock',
                            'Thumbnail Id' => 'thumbnail_id',
                            'Manage Stock' => 'manage_stock',
                            'Stock' => 'stock',
                            'Stock Status' => 'stock_status',
                            'Low Stock Threshold' => 'low_stock_threshold',
                            'Stock Quantity' => 'stock_qty',
                            'Total Sales' => 'total_sales',
                            'Downloadable' => 'downloadable',
                            'Virtual' => 'virtual',
                            'Regular Price' => 'regular_price',
                            'Sale Price' => 'sale_price',
                            'Purchase Note' => 'purchase_note',
                            'Menu Order' => 'menu_order',
                            'Enable Reviews' => 'comment_status',
                            'Weight' => 'weight',
                            'Length' => 'length',
                            'Width' => 'width',
                            'Height' => 'height',
                            'SKU' => 'sku',
                            'UpSells ID' => 'upsell_ids',
                            'CrossSells ID' => 'crosssell_ids',
                            'Grouping ID' => 'grouping_product',
                            'Sales Price Date From' => 'sale_price_dates_from',
                            'Sales Price Date To' => 'sale_price_dates_to',
                            'Price' => 'price',
                            'Sold Individually' => 'sold_individually',
                            'Backorders' => 'backorders',
                            'Product Image Gallery' => 'product_image_gallery',
                            'Product URL' => 'product_url',
                            'Button Text' => 'button_text',
                            'Featured' => 'featured',
                            'Downloadable Files' => 'downloadable_files',
                            'Download Limit' => 'download_limit',
                            'Download Expiry' => 'download_expiry',
                            'Download Type' => 'download_type',
                            '_subscription_period' => '_subscription_period',
                            '_subscription_period_interval' => '_subscription_period_interval',
                            '_subscription_length' => '_subscription_length',
                            '_subscription_trial_period' => '_subscription_trial_period',
                            '_subscription_trial_length' => '_subscription_trial_length',
                            '_subscription_price' => '_subscription_price',
                            '_subscription_sign_up_fee' => '_subscription_sign_up_fee',
                );
            }
            if(is_plugin_active('woocommerce-chained-products/woocommerce-chained-products.php') && $import_type == 'WooCommerce') {
                        $chain_product = array(
                            'Chained Product Detail' => 'chained_product_detail',
                            'Chained Product Manage Stock' => 'chained_product_manage_stock',
                        );
                        foreach($chain_product as $key => $value){
                            $pro_meta_fields[$key] = $value;
                        }
            }
            if(is_plugin_active('woocommerce-product-retailers/woocommerce-product-retailers.php') && $import_type == 'WooCommerce') {
                        $retailers = array(
                            'Retailers Only Purchase' => 'wc_product_retailers_retailer_only_purchase',
                            'Retailers Use Buttons' => 'wc_product_retailers_use_buttons',
                            'Retailers Product Button Text' => 'wc_product_retailers_product_button_text',
                            'Retailers Catalog Button Text' => 'wc_product_retailers_catalog_button_text',
                            'Retailers Id' => 'wc_product_retailers_id',
                            'Retailers Price' => 'wc_product_retailers_price',
                            'Retailers URL' => 'wc_product_retailers_url',
                        );
                        foreach($retailers as $key => $value){
                            $pro_meta_fields[$key] = $value;
                        }
            }
            if(is_plugin_active('woocommerce-product-addons/woocommerce-product-addons.php') && $import_type == 'WooCommerce') {
                        $product_Addons = array(
                            'Product Addons Exclude Global' => 'product_addons_exclude_global',
                            'Product Addons Group Name' => 'product_addons_group_name',
                            'Product Addons Group Description' => 'product_addons_group_description',
                            'Product Addons Type' => 'product_addons_type',
                            'Product Addons Position' => 'product_addons_position',
                            'Product Addons Required' => 'product_addons_required',
                            'Product Addons Label Name' => 'product_addons_label_name',
                            'Product Addons Price' => 'product_addons_price',
                            'Product Addons Minimum' => 'product_addons_minimum',
                            'Product Addons Maximum' => 'product_addons_maximum',
                        );
                        foreach($product_Addons as $key => $value){
                            $pro_meta_fields[$key] = $value;
                        }
            }
            if(is_plugin_active('woocommerce-warranty/woocommerce-warranty.php') && $import_type == 'WooCommerce' ) {
                        $warranty = array(
                            'Warranty Label' => 'warranty_label',
                            'Warranty Type' => 'warranty_type',
                            'Warranty Length' => 'warranty_length',
                            'Warranty Value' => 'warranty_value',
                            'Warranty Duration' => 'warranty_duration',
                            'Warranty Addons Amount' => 'warranty_addons_amount',
                            'Warranty Addons Value' => 'warranty_addons_value',
                            'Warranty Addons Duration' => 'warranty_addons_duration',
                            'No Warranty Option' => 'no_warranty_option',
                        );
                        foreach($warranty as $key => $value){
                            $pro_meta_fields[$key] = $value;
                        }
            }
            if(is_plugin_active('woocommerce-pre-orders/woocommerce-pre-orders.php') && $import_type == 'WooCommerce' ) {
                        $pre_orders = array(
                            'Pre-Orders Enabled' => 'preorders_enabled',
                            'Pre-Orders Fee' => 'preorders_fee',
                            'Pre-Orders When to Charge' => 'preorders_when_to_charge',
                            'Pre-Orders Availabilty Datetime' => 'preorders_availability_datetime'
                        );
                        foreach($pre_orders as $key => $value){
                            $pro_meta_fields[$key] = $value;
                        }
            }   
        }

        $pro_meta_fields_line = $this->convert_static_fields_to_array($pro_meta_fields);
        $response['product_meta_fields'] = $pro_meta_fields_line;
		return $response;		
    }

    /**
	* Product Meta extension supported import types
	* @param string $import_type - selected import type
	* @return boolean
	*/
    public function extensionSupportedImportType($import_type ){
        if(is_plugin_active('woocommerce/woocommerce.php')){
            $import_type = $this->import_name_as($import_type);
            if($import_type == 'WooCommerce') { 
                return true;
            }else{
                return false;
            }
        }
	}

}