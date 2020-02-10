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

class MappingExtension{
	private static $instance = null;
	private static $extension = [];
	private static $validatefile = [];

	private function __construct(){
		add_action('wp_ajax_mappingfields',array($this,'mapping_field_function'));
		add_action('wp_ajax_getfields',array($this,'get_fields'));
	}

	public static function getInstance() {
		if (MappingExtension::$instance == null) {
			MappingExtension::$instance = new MappingExtension;
			MappingExtension::$validatefile = new ValidateFile;
			foreach(get_declared_classes() as $class){
				if(is_subclass_of($class, 'Smackcoders\FCSV\ExtensionHandler')){ 
					array_push(MappingExtension::$extension ,$class::getInstance() );	
				}
			}
			return MappingExtension::$instance;
		}
		return MappingExtension::$instance;
	}

	/**
	* Ajax Call 
	* Provides all Widget Fields for Mapping Section
	* @return array - mapping fields
	*/
	public function mapping_field_function(){

		$import_type = $_POST['Types'];
		$hash_key = $_POST['HashKey'];
		$mode = $_POST['Mode'];
		global $wpdb;

		$response = [];
		$details = [];
		$info = [];

		$table_name = $wpdb->prefix."smackcsv_file_events";
		$fields = $wpdb->get_results("UPDATE $table_name SET mode ='$mode' WHERE hash_key = '$hash_key'");

		$get_result = $wpdb->get_results("SELECT file_name FROM $table_name WHERE hash_key = '$hash_key' ");
		$filename = $get_result[0]->file_name;
		$file_extension = pathinfo($filename, PATHINFO_EXTENSION);

		$template_table_name = $wpdb->prefix."ultimate_csv_importer_mappingtemplate";
			$smackcsv_instance = SmackCSV::getInstance();
			$upload_dir = $smackcsv_instance->create_upload_dir();
			$response = [];
			if($file_extension == 'csv' || $file_extension == 'txt'){
			ini_set("auto_detect_line_endings", true);
			$info = [];
			if (($h = fopen($upload_dir.$hash_key.'/'.$hash_key, "r")) !== FALSE) 
			{
			// Convert each line into the local $data variable
			
				$delimiters = array( ',','\t',';','|',':','&nbsp');
				$file_path = $upload_dir . $hash_key . '/' . $hash_key;
				$delimiter = MappingExtension::$validatefile->getFileDelimiter($file_path, 5);
				$array_index = array_search($delimiter,$delimiters);
				if($array_index == 5){
					$delimiters[$array_index] = ' ';
				}
				while (($data = fgetcsv($h, 0, $delimiters[$array_index])) !== FALSE) 
				{		
				// Read the data from a single line
				array_push($info , $data);
				$exp_line = $info[0];

					$response['success'] = true;
					$response['show_template'] = false;
					$response['csv_fields'] = $exp_line;

					$value = $this->mapping_fields($import_type);
					$response['fields'] = $value;
					echo wp_json_encode($response);
					wp_die();  			
			}	
			// Close the file
			fclose($h);
			}
		}
		if($file_extension == 'xml'){
			$xml_class = new XmlHandler();
			$upload_dir_path = $upload_dir. $hash_key;
			if (!is_dir($upload_dir_path)) {
				wp_mkdir_p( $upload_dir_path);
			}
			chmod($upload_dir_path, 0777);   
			$path = $upload_dir . $hash_key . '/' . $hash_key;   

			$xml = simplexml_load_file($path);
			$xml_arr = json_decode( json_encode($xml) , 1);
			
			foreach($xml->children() as $child){   
				$child_name = $child->getName();    
			}
			$parse_xml = $xml_class->parse_xmls($hash_key);
			$i = 0;
			foreach($parse_xml as $xml_key => $xml_value){
				if(is_array($xml_value)){
					foreach ($xml_value as $e_key => $e_value){
						$headers[$i] = $e_value['name'];
						$i++;
					}
				}
			}
			$response['success'] = true;
			$response['show_template'] = false;
			$response['csv_fields'] = $headers;

			$value = $this->mapping_fields($import_type);

			$response['fields'] = $value;
			echo wp_json_encode($response);
			wp_die();  			
		}
	}

	/**
	* Provides active plugins
	* @return array - active plugins
	*/
	public function get_active_plugins() {
		$active_plugins = get_option('active_plugins');
		return $active_plugins;
	}

	/**
	* Ajax Call 
	* Provides all Widget Fields for Export Section
	* @return array - mapping fields
	*/
	public function get_export_fields(){
		$import_type = $_POST['Types'];
		$response = [];

		$value = $this->mapping_fields($import_type);
		$response['success'] = true;
		$response['fields'] = $value;
	
		echo wp_json_encode($response);
		wp_die();  
	}

	/**
	* Provides all Widget Fields for Export Section
	* @return array - mapping fields
	*/
	public function get_fields($module){ 
		$import_type = $module;
		$response = [];
		$value = $this->mapping_fields($import_type);	
		$response['fields'] = $value;
		return $response;
	}

	public function mapping_fields($import_type){
		$support_instance = [];
		$value = [];
		for($i = 0 ; $i < count(MappingExtension::$extension) ; $i++){
			$extension_instance = MappingExtension::$extension[$i];
			if($extension_instance->extensionSupportedImportType($import_type)){
				array_push($support_instance , $extension_instance);		
			}	
		}		
		for($i = 0 ;$i < count($support_instance) ; $i++){	
			$supporting_instance = $support_instance[$i];
			$fields = $supporting_instance->processExtension($import_type);
			array_push($value , $fields);			
		}
		return $value;
	}
}		