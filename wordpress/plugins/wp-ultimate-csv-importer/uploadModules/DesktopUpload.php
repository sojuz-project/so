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

class DesktopUpload implements Uploads{

    private static $instance = null;
    private static $smack_csv_instance = null;

    private function __construct(){
		add_action('wp_ajax_get_desktop',array($this,'upload_function'));
    }

    public static function getInstance() {
		if (DesktopUpload::$instance == null) {
			DesktopUpload::$instance = new DesktopUpload;
            DesktopUpload::$smack_csv_instance = SmackCSV::getInstance();
			return DesktopUpload::$instance;
		}
		return DesktopUpload::$instance;
    }


    /**
	 * Upload file from desktop.
	 */
    public function upload_function(){ 
        $validate_instance = ValidateFile::getInstance();
        $zip_instance = ZipHandler::getInstance();
        global $wpdb;
        $file_table_name = $wpdb->prefix ."smackcsv_file_events";
          
        $file_name = $_FILES['csvFile']['name'];    
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $validate_format = $validate_instance->validate_file_format($file_name);
        
        $response =[];
        if($validate_format == 'yes'){
        
            $upload_dir = DesktopUpload::$smack_csv_instance->create_upload_dir();
            
            if($upload_dir){
                $event_key = DesktopUpload::$smack_csv_instance->convert_string2hash_key($file_name);
                
                if($file_extension == 'zip'){
                    $zip_response = [];    
                    $path = $upload_dir . $event_key . '.zip';
                    $extract_path = $upload_dir . $event_key;
                    
                    if(move_uploaded_file($_FILES['csvFile']['tmp_name'], $path)){
                        chmod($path, 0777);

                        $zip_response['success'] = true;
                        $zip_response['filename'] = $file_name;
                        $zip_response['file_type'] = 'zip'; 
                        $zip_response['info'] = $zip_instance->zip_upload($path , $extract_path);
                    }else{
                        $zip_response['success'] = false;
                        $zip_response['message'] = "Cannot download zip file";
                    }   
                    echo wp_json_encode($zip_response); 
                    wp_die();
                }
                

                $upload_dir_path = $upload_dir. $event_key;
                if (!is_dir($upload_dir_path)) {
                    wp_mkdir_p( $upload_dir_path);
                }
                chmod($upload_dir_path, 0777);	

                $fields = $wpdb->insert( $file_table_name , array('file_name' => $file_name , 'hash_key' => $event_key , 'status' => 'Downloading', 'lock' => true) );
                $last_id = $wpdb->get_results("SELECT id FROM $file_table_name ORDER BY id DESC LIMIT 1",ARRAY_A);
                $lastid = $last_id[0]['id'];
                switch($_FILES['csvFile']['error']){
                    case UPLOAD_ERR_OK:
                        $path = $upload_dir. $event_key. '/' . $event_key;
                       
                        if(move_uploaded_file($_FILES['csvFile']['tmp_name'], $path)){
                            chmod($path, 0777);

                            $validate_file = $validate_instance->file_validation($path , $file_extension);

                            $file_size = filesize($path);
		                    $filesize = $validate_instance->formatSizeUnits($file_size);
                            
                            if($validate_file == "yes"){
                                $fields = $wpdb->get_results("UPDATE $file_table_name SET status='Downloaded',`lock`=false WHERE id = '$lastid'");
                                
                                $get_result = $validate_instance->import_record_function($event_key , $file_name);
                                $response['success'] = true;
                                $response['filename'] = $file_name;
                                $response['hashkey'] = $event_key;
                                $response['posttype'] = $get_result['Post Type'];
                                $response['selectedtype'] = $get_result['selected type'];
                                $response['file_type'] = $file_extension;
                                $response['file_size'] = $filesize;
                                $response['message'] = 'success';
                                echo wp_json_encode($response); 

                            }else{
                                $response['success'] = false;
                                $response['message'] = $validate_file;
                                echo wp_json_encode($response); 
                                unlink($path);
                                $fields = $wpdb->get_results("UPDATE $file_table_name SET status='Download_Failed' WHERE id = '$lastid'");
                            }
            
                        }else{
                            $response['success'] = false;
                            $response['message'] = "Cannot download the file";
                            echo wp_json_encode($response); 
                            $fields = $wpdb->get_results("UPDATE $file_table_name SET status='Download_Failed' WHERE id = '$lastid'");
                        }
                        break;

                    case UPLOAD_ERR_INI_SIZE:
                        $response['success'] = false;
                        $response['message'] = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                        echo wp_json_encode($response); 
                        $fields = $wpdb->get_results("UPDATE $file_table_name SET status='Download_Failed' WHERE id = '$lastid'");
                        break;
                        
                    default:
                        $response['success'] = false;
                        $response['message'] = "Cannot download the file";
                        echo wp_json_encode($response); 
                        $fields = $wpdb->get_results("UPDATE $file_table_name SET status='Download_Failed' WHERE id = '$lastid'");
                        break;
                }
            }else{
                $response['success'] = false;
                $response['message'] = "Please create Upload folder with writable permission";
                echo wp_json_encode($response); 
            }

        }else{
            $response['success'] = false;
            $response['message'] = $validate_format;
            echo wp_json_encode($response); 
        }
        wp_die();

    }

}
