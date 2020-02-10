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

class XmlHandler {
	private static $xml_instance = null;
	private $result_xml = [];
	public function __construct(){
		add_action('wp_ajax_get_parse_xml',array($this,'parse_xml'));
	}

	public static function getInstance() {

		if (XmlHandler::$xml_instance == null) {
			XmlHandler::$xml_instance = new XmlHandler;
			return XmlHandler::$xml_instance;
		}
		return XmlHandler::$xml_instance;
	}


	public function parse_xml(){

		$row_count = $_POST['row'];
		$hash_key = $_POST['HashKey'];

		$smack_csv_instance = SmackCSV::getInstance();
		$upload_dir = $smack_csv_instance->create_upload_dir();

		$upload_dir_path = $upload_dir. $hash_key;
		if (!is_dir($upload_dir_path)) {
			wp_mkdir_p( $upload_dir_path);
		}
		chmod($upload_dir_path, 0777);   
		$path = $upload_dir . $hash_key . '/' . $hash_key;    

		$response = [];
		$xml = simplexml_load_file($path);
		foreach($xml->children() as $child){   
			$child_name = $child->getName();     
		}
		$total_xml_count = $this->get_xml_count($path , $child_name);
		if($total_xml_count == 0 ){
			$sub_child = $this->get_child($child,$path);
			$child_name = $sub_child['child_name'];
			$total_xml_count = $sub_child['total_count'];
		}
		$doc = new \DOMDocument();
		$doc->load($path);
		$row = $row_count - 1;
		$node = $doc->getElementsByTagName($child_name)->item($row);
		$this->tableNodes($node);
		$response['xml_array'] = $this->result_xml;
		$response['success'] = true;
		$response['total_rows'] = $total_xml_count;
		echo  wp_json_encode($response);
		wp_die();
	}

	public function parse_xmls($hash_key,$line_number = null){


		$smack_csv_instance = SmackCSV::getInstance();
		$upload_dir = $smack_csv_instance->create_upload_dir();

		$upload_dir_path = $upload_dir. $hash_key;
		if (!is_dir($upload_dir_path)) {
			wp_mkdir_p( $upload_dir_path);
		}
		chmod($upload_dir_path, 0777);   
		$path = $upload_dir . $hash_key . '/' . $hash_key;    

		$response = [];
		$xml = simplexml_load_file($path);
		foreach($xml->children() as $child){   
			$child_name =  $child->getName();    
		}
		$total_xml_count = $this->get_xml_count($path , $child_name);
		if($total_xml_count == 0){
			$sub_child = $this->get_child($child,$path);
			$child_name = $sub_child['child_name'];
			$total_xml_count = $sub_child['total_count'];
		}
		$total_xml_count = $this->get_xml_count($path , $child_name);
		$doc = new \DOMDocument();
		$doc->load($path);
		$node = $doc->getElementsByTagName($child_name)->item($line_number);
		$this->tableNodes($node);
		$response['xml_array'] = $this->result_xml;
		$response['success'] = true;
		$response['total_rows'] = $total_xml_count;
		return $response;
	}

	public function get_child($child,$path){
		foreach($child->children() as $sub_child){
			$sub_child_name = $sub_child->getName();
		}
		$total_xml_count = $this->get_xml_count($path , $sub_child_name);
		if($total_xml_count == 0 ){
			$this->get_child($sub_child,$path);
		}
		else{
			$result['child_name'] = $sub_child_name;
			$result['total_count'] = $total_xml_count;
			return $result;
		}
	}

	/**
	 * Parse xml file.
	 */
	public function parsing_xmls(){

		$hash_key = $_POST['HashKey'];
		$treetype = $_POST['treetype'];	

		$smack_csv_instance = SmackCSV::getInstance();
		$upload_dir = $smack_csv_instance->create_upload_dir();

		$upload_dir_path = $upload_dir. $hash_key;
		if (!is_dir($upload_dir_path)) {
			wp_mkdir_p( $upload_dir_path);
		}
		chmod($upload_dir_path, 0777);

		$file = $upload_dir . $hash_key . '/' . $hash_key;    
		$id = "item";

		$namespace = explode(":", $id);

		if(isset($namespace[1]))
			$n = $namespace[1];
		else
			$n = $id;


		$doc = new \DOMDocument();
		$doc->load($file);

		$nodes=$doc->getElementsByTagName($n);

		if($nodes->length < $_POST['pag'])
			$response['message'] = "Maximum Limit Exceed!";

		if(isset($_POST['pag']))
			$i = $_POST['pag'] - 1;
		else
			$i = 0;
		if($i < 0)
			$response['message'] = "Node not available!";
		while (is_object($finance = $doc->getElementsByTagName($n)->item($i))) {

			if($treetype == 'table'){
				$result = $this->tableNode($finance);

			}
			else{
				$result = $this->treeNode($finance);
			}
			$i++;
		}
	}


	public function tableNodes($node)
	{
		if($node->nodeName != '#text'){ 
			if($node->childNodes->length != 1 && $node->nodeName != '#cdata-section'){ 
				
			} 
			if ($node->hasChildNodes()) {
				foreach ($node->childNodes as $child){
					$this->tableNodes($child);   
				}
				if($node->hasAttributes()){
					for ($i = 0; $i <= $node->attributes->length; ++$i) {
						$attr_nodes = $node->attributes->item($i);
						if($attr_nodes->nodeName && $attr_nodes->nodeValue) 
							$attrs[$node->nodeName][$attr_nodes->nodeName] = $attr_nodes->nodeValue;
					}
				}    
				if($node->nodeValue || $node->nodeValue == 0){ 
					if($node->childNodes->length == 1){
						$xml_array = array();
						$xml_array['name'] = $node->nodeName;
						$xml_array['node_path'] = $node->getNodePath();
						$xml_array['value'] = $node->nodeValue;
						array_push($this->result_xml,$xml_array);          
					}
				}
			}  
		}
	}




	/**
	 * Get xml rows count.
	 * @param  string $eventFile - path to file
	 * @return int
	 */
	public function get_xml_count($eventFile , $child_name){
		$doc = new \DOMDocument();
		$doc->load($eventFile);
		$nodes=$doc->getElementsByTagName($child_name);
		$total_row_count = $nodes->length;
		return $total_row_count;	
	}
}

