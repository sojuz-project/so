<?php 
if (!defined('ABSPATH')) { exit; }
if(!class_exists('wpSmartImportQuery')) {
	class wpSmartImportQuery  {

		public function wpsi_insert($table, $data, $format = null) {
			global $wpdb;
			$table = $wpdb->prefix.$table;
			$res = $wpdb->insert($table, $data, $format);
			return $res == false ? 0 : $wpdb->insert_id;
		}

		public function wpsi_update($table, $data, $where, $format = null, $where_format = null) {
			global $wpdb;
			$table = $wpdb->prefix.$table;
			$res = $wpdb->update($table, $data, $where, $format, $where_format);
			return $res == false ? 0 : $res;
		}

		public function wpsi_getRow($table_name, $id) {
			global $wpdb;
	        $table = $wpdb->prefix.$table_name;
	        $querystr = "SELECT * FROM $table WHERE id = $id ";
	        $ret = $wpdb->get_row($querystr);
	        if (empty($ret)) {
	        	$ret = array('error'=>"No Data found");
	        }
	        return $ret;
		}

		public function retrieve_posts($import_id) {
			global $wpdb;
		    $table = $wpdb->prefix."wpsi_posts";
		    $querystr = "SELECT * FROM $table WHERE import_id = $import_id ";
		    $posts = $wpdb->get_results($querystr, ARRAY_A);
		    return $posts;
		}

		public function wpsi_saveFile($post_data) {
		    global $wpdb;
			$table = $wpdb->prefix."wpsi_files";
    		$res = 0;
    		$data = array(
    				"name" 			=> $post_data['file_name'],
    				"file_name" 	=> basename($post_data['file_path']),
    				"file_path" 	=> $post_data['file_path'] ,
    				"folder_name" 	=> wpSmartImport::getVar('folder_name'),
    				"created_at" 	=> current_time('mysql'),
    				"updated_at" 	=> current_time('mysql')
				);
    		$format = array('%s', '%s','%s', '%s', '%s');
    		if (self::file_record_exist($post_data['file_path'])) {
    			$res = $wpdb->insert($table, $data, $format);
    			$res = $wpdb->insert_id;
    		}
			return $res;
		}
		
		static public function retrieve_files() {
			global $wpdb;
		    $table = $wpdb->prefix."wpsi_files";
		    $querystr = "SELECT * FROM $table";
		    $files = $wpdb->get_results($querystr, ARRAY_A);
		    return $files;
		}

		public function delete_record($table_name, $where, $format) {
			global $wpdb;
		    $table = $wpdb->prefix.$table_name;
		    return $wpdb->delete($table, $where, $format);
		}

		public function delete_import($import_id) {
			if(empty($import_id) || absint($import_id) < 1 ) return;
			global $wpdb;
		    $table = $wpdb->prefix."wpsi_imports";
		    return $wpdb->delete($table, array('id' => $import_id), array('%d'));
		}

		public function delete_file_by($field, $value) {
			$format = is_numeric($value) ? '%d' : '%s';
			global $wpdb;
		    $table = $wpdb->prefix."wpsi_files";
		    return $wpdb->delete($table, array($field => $value), array($format));
		}

		public function delete_post($post_id, $import_id) {
			global $wpdb;
		    $table = $wpdb->prefix."wpsi_posts";
		    return $wpdb->delete($table, array('post_id' => $post_id,'import_id' => $import_id), array(
		     '%d','%d'));
		}

		public function check_unique_key($value) {
			global $wpdb;
		    $table = $wpdb->prefix."wpsi_imports";
		    $querystr = "SELECT * FROM $table WHERE unique_key = '$value' ";
		    $a = $wpdb->get_results($querystr);
		    if (count($a) > 0) {
		    	return false;
		    } else {
		    	return true;
		    }
		}

		public function check_post_exist($post_id, $unique_key) {
			global $wpdb;
		    $table = $wpdb->prefix."wpsi_posts";
		    $id = 0;
		    $querystr = "SELECT * FROM $table WHERE `post_id` = $post_id AND `unique_key` = '$unique_key' ";
		    $id = $wpdb->get_var($querystr);
		    return empty($id) ? false : true;
		}

		public function file_record_exist($value) {
			global $wpdb;
		    $table = $wpdb->prefix."wpsi_files";
		    $querystr = "SELECT * FROM $table WHERE file_path = '$value' ";
		    $a = $wpdb->get_results($querystr);
		    if (count($a) > 0) {
		    	return false;
		    } else {
		    	return true;
		    }
		}

		public function wpsi_file_name_check() {
			global $wpdb;
		    $table = $wpdb->prefix."wpsi_files";
		    $name = trim(sanitize_text_field($_POST['name']));
		    $querystr = "SELECT * FROM $table WHERE name = '$name'";
		    $result = $wpdb->get_var($querystr);
		    if(empty($result)) {
		    	$msg = "You Can use this name";
		    	$response = 'success';
		    } else {
		    	$msg = "Name Already Exist <br> Note : USE Unique name for finding a file Easily";
		    	$response = 'error';
		    }
			echo json_encode(array('msg' => $msg, 'response' => $response));
			wp_die();
		}
	}
	new wpSmartImportQuery;
}