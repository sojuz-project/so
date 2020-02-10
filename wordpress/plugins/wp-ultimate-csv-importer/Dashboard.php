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
    
class Dashboard {
    private static $dashboard_instance = null;
    private static $extension_instance = null;

    private function __construct(){
		add_action('wp_ajax_LineChart',array($this,'fetch_LineChart_data'));
		add_action('wp_ajax_PieChart',array($this,'fetch_PieChart_data'));
		add_action('wp_ajax_BarChart',array($this,'fetch_BarStackedChart_data'));

		add_action('wp_ajax_checkExtensions',array($this,'check_extensions'));
		add_action('wp_ajax_listuploads',array($this,'list_upload_function'));
		add_action('wp_ajax_locklist',array($this,'lock_list_function'));
    }
    
    public static function getInstance() {
            
        if (Dashboard::$dashboard_instance == null) {
            Dashboard::$dashboard_instance = new Dashboard;
            Dashboard::$extension_instance = new ExtensionHandler;
            
            return Dashboard::$dashboard_instance;
        }
        return Dashboard::$dashboard_instance;
    }

    public function fetch_LineChart_data() {
		global $wpdb;
		$available_types = array();
		$import_type_data = array();
		$data_arr = array();

		foreach(Dashboard::$extension_instance->get_import_post_types() as $name => $type) {
			$available_types[$name] = $type;
		}
		foreach (get_taxonomies() as $item => $taxonomy_name) {
			$available_types[$item] = $taxonomy_name;
		}
		$available_types = array_flip($available_types);
		$myarr = array();
		$today = date("Y-m-d H:i:s");
		$j = 0;
		for($i = 11; $i >= 0; $i--) {
			$month[$j] = date("M", strtotime( $today." -$i months"));
			$year[$j]  = date("Y", strtotime( $today." -$i months"));
			$j++;
		}
		$get_list_of_imported_types = $wpdb->get_col($wpdb->prepare("select distinct( import_type ) from smackuci_events", array()));
		foreach($get_list_of_imported_types as $import_type) {
			
			$data = array();
			for($i = 0; $i <= 11; $i++) {
				$count = 0;
				$get_chart_data = $wpdb->get_results($wpdb->prepare( "select sum(created) as %s from smackuci_events where import_type = %s and month = %s and year = %d", $import_type, $import_type, $month[$i], $year[$i] ) );
				
				if($get_chart_data[0]->$import_type) {
					$data[] = $get_chart_data[0]->$import_type;
				} else {
					$data[] = $count;
				}
			}
			if(array_key_exists($import_type,$available_types)){
				$import_type_data[] = $available_types[$import_type];
			} else {
				$import_type_data[] = $import_type;
			}
			array_push($data_arr , $data);				
		}
		$myarr['success'] = true;
		$myarr['label'] = $import_type_data;
		$myarr['data'] = $data_arr;
		echo wp_json_encode($myarr);
		wp_die();
    }
    
    public function fetch_PieChart_data() {
		global $wpdb;
		$available_types = array();
		foreach(Dashboard::$extension_instance->get_import_post_types() as $name => $type) {
			$available_types[$name] = $type;
		}
		foreach (get_taxonomies() as $item => $taxonomy_name) {
			$available_types[$item] = $taxonomy_name;
		}
		$available_types = array_flip($available_types);
		$returnArray = array();
		$today = date("Y-m-d H:i:s");
		$j = 0;
		for($i = 11; $i >= 0; $i--) {
			$month[$j] = date("M", strtotime( $today." -$i months"));
			$year[$j]  = date("Y", strtotime( $today." -$i months"));
			$j++;
		}
		$get_list_of_imported_types = $wpdb->get_col($wpdb->prepare("select distinct( import_type ) from smackuci_events", array()));
		$count = 1;
		foreach($get_list_of_imported_types as $import_type) {
			$get_chart_data = $wpdb->get_results( $wpdb->prepare( "select sum(created) as %s from smackuci_events where import_type = %s", $import_type, $import_type ) );
			if(array_key_exists($import_type,$available_types)){
				$import_type_data = $available_types[$import_type];
			} else {
				$import_type_data = $import_type;
			}
			if($get_chart_data[0]->$import_type) {
                $data = $get_chart_data[0]->$import_type;
                $returnArray['success'] = true;
				$returnArray[ $count ][ $import_type_data ] = $data;
			} else {
                $returnArray['success'] = true;
				$returnArray[ $count ][ $import_type_data ] = 0;
			}
			$count++;
		}
		echo wp_json_encode($returnArray);
		wp_die();
    }
    
    public function fetch_BarStackedChart_data() {
		global $wpdb;
		$available_types = array();
		foreach(Dashboard::$extension_instance->get_import_post_types() as $name => $type) {
			$available_types[$name] = $type;
		}
		foreach (get_taxonomies() as $item => $taxonomy_name) {
			$available_types[$item] = $taxonomy_name;
		}
		$available_types = array_flip($available_types);
		$returnArray = array();
		$today = date("Y-m-d H:i:s");
		$j = 0;
		for($i = 11; $i >= 0; $i--) {
			$month[$j] = date("M", strtotime( $today." -$i months"));
			$year[$j]  = date("Y", strtotime( $today." -$i months"));
			$j++;
		}
		$get_list_of_imported_types = $wpdb->get_col($wpdb->prepare("select distinct( import_type ) from smackuci_events", array()));
        
        $count = 1;
		foreach($get_list_of_imported_types as $import_type) {
            
			$get_chart_data = $wpdb->get_results( $wpdb->prepare( "select sum(created) as created, sum(updated) as updated, sum(skipped) as skipped from smackuci_events where import_type = %s", $import_type, $import_type ) );
            
            if(array_key_exists($import_type,$available_types)){
				$import_type_data = $available_types[$import_type];
			} else {
				$import_type_data = $import_type;
			}
            $returnArray['success'] = true;
			if($get_chart_data[0]->created) {
				$returnArray[ $import_type_data ]['created'] = $get_chart_data[0]->created;
			} else {
				$returnArray[ $import_type_data ]['created'] = 0;
			}
			if($get_chart_data[0]->updated) {
				$returnArray[ $import_type_data ]['updated'] = $get_chart_data[0]->updated;
			} else {
				$returnArray[ $import_type_data ]['updated'] = 0;
			}
			if($get_chart_data[0]->skipped) {
				$returnArray[ $import_type_data ]['skipped'] = $get_chart_data[0]->skipped;
			} else {
				$returnArray[ $import_type_data ]['skipped'] = 0;
			}
			$count++;
		}
		echo wp_json_encode($returnArray);
		wp_die();
	}
	
	public function list_upload_function(){
		global $wpdb;
		$response =[];
		
		$last_data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}smackcsv_file_events order by id desc limit 10",ARRAY_A); 
		if(!$last_data){
			$response['success'] = false;
			$response['message'] = 'not exists';
			echo wp_json_encode($response);
		}else{
			$response['success'] = true;
			$response['message'] = $last_data;
			echo wp_json_encode($response);
		}
	
		wp_die();
	}

	public function get_config_bytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        switch ($last) {
                case 'g':
                        $val *= 1024;
                case 'm':
                        $val *= 1024;
                case 'k':
                        $val *= 1024;
        }
        return $val;
    }
	
	public function lock_list_function(){
		$response=[];
		global $wpdb;
		$lock_data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}smackcsv_file_events WHERE `lock`=true");
		if(!$lock_data){
			$response['success'] = false;
			$response['message'] = 'No lock exists';
			echo wp_json_encode($response);
		}else{
			$response['success'] = true;
			$response['mesage'] = $lock_data;
			echo wp_json_encode($response);
		}
		wp_die();
	}
}