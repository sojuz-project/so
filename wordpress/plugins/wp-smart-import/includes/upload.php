<?php 
if (!defined('ABSPATH')) { exit; }
if(!class_exists('wpSmartImportUpload')){
	class wpSmartImportUpload {
			
		public function wpsi_file_upload() {
			// Check for Security if current request Not from ajax Or nonce is not match  die this request
			wpSmartImportCommon::verify_ajax($_REQUEST['_nonce']);
			$response = array('response' => "ERROR", 'msg' => 'File Not Found');
			$extension_array = array('xml');
			$upload_dir = wp_upload_dir();
			$wpsi_fd_path = $upload_dir["basedir"]. "/". wpSmartImport::getVar('folder_name') . "/";
			$request = wpsi_helper::recursive_sanitize_text_field($_POST);
			if (isset($request['file_from']) && $request['file_from'] == 'download') {
				$file = $request['file'];
				// check remote file is Exist and responce code == 200
				if (wp_remote_retrieve_response_code(wp_remote_get($file)) == 200) {
					$path = explode("?", $file);
					$file_data = pathinfo(trim($path[0]));
					$file_name  = sanitize_file_name(str_replace(" ", "_", basename($path[0])));
					if (isset($file_data['extension']) && in_array($file_data['extension'], $extension_array)) {
						$new_folder = uniqid();
				    	$destination = $wpsi_fd_path. $new_folder ."/"; 
				    	if (!file_exists($destination)) {
							mkdir($destination, 0777, true);
						}
						$destination_path = $destination.$file_name;
						$status = self::download_file($file, $destination_path);
						if ($status) {
							$file_size = filesize($destination_path);
							$response = array(
								'response' 	=> "SUCCESS",
								'msg' 		=> "File is Ready to use",
								'filename' 	=> $file_name,
								'file_size' => self::format_size_units($file_size),
								'type'		=> $file_data['extension'],
								'filepath' 	=> $new_folder.'/'.$file_name
							);
						} else {
							@rmdir($upload_path);
							$response['msg'] = "File Download Error";
						}
					} else {
						$response['msg'] = "File is Not Valid" ;
					}
				}
			} else {
				$fileErrors = array(
				    0 => 'There is no error, the file uploaded with success',
				    1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
				    2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
				    3 => 'The uploaded file was only partially uploaded',
				    4 => 'No file was uploaded',
				    6 => 'Missing a temporary folder',
				    7 => 'Failed to write file to disk.',
				    8 => 'A PHP extension stopped the file upload.',
				);
				$file_data = isset($_FILES) ? $_FILES : array();
				$data = array_merge($_REQUEST, $file_data);
				$f_data = pathinfo($file_data['wpsi_file_upload']['name']);
				$extension = $f_data['extension'];
				if (isset($f_data['extension']) && in_array($f_data['extension'], $extension_array)) {
					$new_folder = uniqid();
			    	$upload_path = $wpsi_fd_path.$new_folder."/";
			    	if (!file_exists($upload_path)) {
						mkdir($upload_path, 0777, true);
					}
					$fileName = sanitize_file_name(str_replace(" ", "_", $data["wpsi_file_upload"]["name"]));
					$temp_name = $data["wpsi_file_upload"]["tmp_name"];
					$file_size = $data["wpsi_file_upload"]["size"];
					$fileError = $data["wpsi_file_upload"]["error"];
					$targetPath = $upload_path;
					$response["filename"] = $fileName;
					$response["filepath"] = $new_folder.'/'.$fileName;
					$full_path = strtr($targetPath.$fileName,"\\","/");
					$response["file_size"] = self::format_size_units($file_size);
					if ($fileError > 0){
						$response["response"] = "ERROR";
			            $response["error"] = $fileErrors[$fileError];
					} else {
						if (file_exists($targetPath . "/" . $fileName)){
							$response["response"] = "ERROR";
					        $response["error"] = "File already exists.";
						} else {
			            	if ($file_res = move_uploaded_file($temp_name, $targetPath."/".$fileName)){
			            		$response['msg'] = "File Ready to run";
			            		$response["response"] = "SUCCESS";
			            		$file = pathinfo($targetPath."/".$fileName);
			            		if($file && isset($file["extension"])) {
			            			$type = $file["extension"];
			            			$response["type"] = $type;	
			            		}
			            	} else {
			            		$response["response"] = "ERROR";
			            		$response["msg"]= "Upload Failed.";
			            	}
				        }
					}
				} else {
					$response['msg'] = "file Not Valid";
				}	
			}
			echo json_encode( $response );
			wp_die();
		}

		/**
		 * Download helper to download files in chunks and save it.
		 * 
		 * @param  string  $srcName      Source Path/URL to the file you want to download
		 * @param  string  $dstName      Destination Path to save your file
		 * @param  integer $chunkSize    (Optional) How many bytes to download per chunk (In MB). Defaults to 1 MB.
		 * @param  boolean $returnbytes  (Optional) Return number of bytes saved. Default: true
		 * 
		 * @return integer               Returns number of bytes delivered.
		 */
		static function download_file($srcName, $dstName, $chunkSize = 1, $returnbytes = true) {
		  $chunksize = $chunkSize*(1024*1024); // How many bytes per chunk
		  $data = '';
		  $bytesCount = 0;
		  $handle = fopen($srcName, 'rb');
		  $fp = fopen($dstName, 'w');
		  if ($handle === false) {
		    return false;
		  }
		  while (!feof($handle)) {
		    $data = fread($handle, $chunksize);
		    if (fwrite($fp, $data, strlen($data)) == false){
		    	return false;
		    }
		    
		    if ($returnbytes) {
		        $bytesCount += strlen($data);
		    }
		  }
		  $status = fclose($handle);
		  fclose($fp);
		  if ($returnbytes && $status) {
		    return $bytesCount; // Return number of bytes delivered like readfile() does.
		  }
		  return $status;
		}

		public function format_size_units($bytes) {
		    if ($bytes >= 1073741824) {
		        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
		    }
		    elseif ($bytes >= 1048576) {
		        $bytes = number_format($bytes / 1048576, 2) . ' MB';
		    }
		    elseif ($bytes >= 1024) {
		        $bytes = number_format($bytes / 1024, 2) . ' KB';
		    }
		    elseif ($bytes > 1) {
		        $bytes = $bytes . ' bytes';
		    }
		    elseif ($bytes == 1) {
		        $bytes = $bytes . ' byte';
		    }
		    else{
		        $bytes = '0 bytes';
		    }
		    return $bytes;
		}
	}
}