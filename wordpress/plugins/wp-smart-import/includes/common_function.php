<?php 
if (!defined('ABSPATH')) { exit; }
if (!class_exists('wpSmartImportCommon')){
	class wpSmartImportCommon {
		
    	public function wpsi_get_option($option_name = '' , $default = false) {
    		$option_name = empty($option_name) ? wpSmartImport::getVar('wpsi_session') : $option_name ;
    		return get_option($option_name,$default);
    	}

    	public function update_session($array) {
 			if (!is_array($array) || empty($array)) {
 				return false;
 			} 
 			$option = $this->wpsi_get_option();
 			if (is_string($option) || empty($option)) {
 				$option = array();
 			}
 			foreach ($array as $key => $value) {
 				(array)$option[$key] = $value;
 			}
 			update_option(wpSmartImport::getVar('wpsi_session'), $option);
 			return true;
    	}

    	public function unset_session() {
    		update_option(wpSmartImport::getVar('wpsi_session'), "");
    	}

    	static public function woocommerce_exist() {
    		$active_plugins = apply_filters('active_plugins', get_option('active_plugins')); 
    		if (in_array("woocommerce/woocommerce.php", $active_plugins)) {
	    		return true;
			}
			return false;
    	}

    	static public function verify_ajax($_nonce = '') {
    		if (!wp_doing_ajax() || !wp_verify_nonce(sanitize_text_field($_nonce), 'wpsi_nonce')) {
				echo 'Invalid HTTP Request';
				wp_die();
			}
    	}

		public function rec_search_file_path($path, $fdata) {
		    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path["basedir"]));
		    $basename = basename($path["basedir"]);
		    $files = array();
		    foreach ($rii as $file):
		        if (!$file->isDir()) {
					if (array_key_exists('extension', $fdata)) {
						$pattern = $fdata['basename'];
					} else {
						$pattern = $fdata['basename'].'.*';
					} 
					$result = fnmatch($pattern, $file->getFilename());
					if ($result) {
						$exp = explode($basename, $file->getPathname());
						$files[] = $path["baseurl"].$exp[1];
					}
		        }
		    endforeach;
		    return !empty($files) ? $files[0] :'';
		}
		static public function current_xmlfile_path() {
			$upload_dir = wp_upload_dir();
			global $session;
			$file_path = $session['file_path'];
			return $upload_dir['basedir']. DIRECTORY_SEPARATOR . wpSmartImport::getVar('folder_name') . DIRECTORY_SEPARATOR .$file_path;
		}	
		public function wpsi_currentxmlData($str, $arg = '') {
			$upload_dir = wp_upload_dir();
			global $session;
			$file_path = wpSmartImport::getVar('file_path', 'session');
			$file_name = pathinfo($file_path);
			if (isset($file_path)) {
				if ($str == "folder")	{ 
					return $file_name['dirname'];
				} elseif ($str == "file") { 
					return $upload_dir['basedir']. "/". wpSmartImport::getVar('folder_name') . "/".$file_path;
				} elseif ($str == "extension") { 
					return $file_name['extension'];
				} elseif ($str == "file_name") { 
					return $file_name['basename'];
				} elseif ($str == "node") { 
					$node = $session['node'];
					if (isset($node)) {
						return $node;
					}
				} elseif ( $str == "FilePath") {
					if ($arg == "url")  {
						return $upload_dir['baseurl']."/". wpSmartImport::getVar('folder_name')."/".$file_name['dirname'].'/';
					} else {
						return $upload_dir['basedir']."/". wpSmartImport::getVar('folder_name') . "/".$file_name['dirname'].'/';
					}
				}
			}
			return '';
		}

		static public function parse_post_data($pdata, $batchs) {
		   	$pdata_temp = array(); 
	    	foreach ($batchs as $bn) {
	    		$array = $pdata;
	    		array_walk_recursive($array, function (&$value, $key) use ($bn) {
	    			$value = self::parse_string($value, $bn);
				});
	    		$pdata_temp[] = $array;
	    	}
		    return $pdata_temp;		    	   
		}

		static public function parse_post_array($array, $node_num = 0) {
	    	array_walk_recursive($array, function (&$value, $key) use ($node_num){
    			$value = self::parse_string($value, $node_num);
			});
			return $array;
		}

		static public function parse_string($string, $node_num = 0, $allow_default = false, $default = '')
		{
			global $session;
			preg_match_all('/{(.*?)}/', $string, $matches);
			$node = $session['node'];
			$res = wpsiAjaxController::checkfile();
			$parse_string = '';
			if ($res){
				$dom = $res['dom_obj']; // DomDocument Object
				$xpath = new DOMXPath($dom);
				$element = $dom->getElementsByTagName($node)->item($node_num);
				// our query is relative to the tbody node
				if (!empty($matches[1])) {
					foreach ($matches[1] as $midx => $mval) {
						$query = $mval;
						$entries = $xpath->query($query,$element);
						if($entries->length>0){
							foreach ($entries as $entry) {
							    $matches[1][$midx] = trim($entry->nodeValue);
							}
						} else if($allow_default){
							$matches[1][$midx] = trim($entry->nodeValue);
						}
					}
					$temp_str =  $string;
					foreach ($matches[0] as $midx => $mvalue) {
						$temp_str = str_replace($mvalue, $matches[1][$midx], $temp_str);
					}
					$parse_string = $temp_str;

				} else {
					$parse_string = $string;
				}
			}
			return $parse_string;
		}

		// generate_featured_image  Wordpress
		static public function generate_featured_image($image_url, $post_id = 0) {
			$upload_dir = wp_upload_dir();
		   	$request = wp_remote_get($image_url);
			$response = @wp_remote_retrieve_body($request);
		   	$image_url_not_querystr = substr($image_url, 0, strrpos($image_url, "?"));
		    $filename = basename($image_url_not_querystr);
		    $ext = pathinfo($image_url_not_querystr, PATHINFO_EXTENSION);
		    if (empty($ext)) {
		    	$filename.='.jpeg';
		    }
		   	$filename = wp_unique_filename($upload_dir['path'], sanitize_file_name($filename));
		    $upload_file = wp_upload_bits($filename, null, $response);
		    if (wp_mkdir_p($upload_dir['path'])) 
		     	$file = $upload_dir['path'].'/'.$filename;
    		else
    			$file = $upload_dir['basedir'].'/'.$filename;
		   
		    $wp_filetype = wp_check_filetype($filename, null);
		    $attachment = array(
		        'post_mime_type' 	=> $wp_filetype['type'],
		        'post_title' 		=> $filename,
		        'post_content' 		=> '',
		        'post_status' 		=> 'inherit'
		    );
		    $attach_id = wp_insert_attachment($attachment, $file, $post_id);
		    require_once(ABSPATH . 'wp-admin/includes/image.php');
		    $attach_data = wp_generate_attachment_metadata($attach_id, $file);
		    wp_update_attachment_metadata($attach_id, $attach_data);
		    return $attach_id;
		}

		public function sort_and_get_images($files, $mode) {
			if (is_array($files)) {
				foreach ($files as $file_string) {
					$file_string = explode(',',$file_string);
					$file_string = array_filter( $file_string );
					foreach ($file_string as $key => $fname) {
						$images[] = trim($fname);
					}
				}
			} else {
				$files = explode(',',$files);
				$files = array_filter( $files );
				if(is_array($files)) {
					foreach ($files as $idx => $fname) {
						$images[] = trim($fname);
					}
				}
			}
			$images = array_filter(array_unique($images));
			if (is_array($images)) {
				$upload_dir = wp_upload_dir();
				foreach ($images as  $fname) {
					$file_names[] = $fname;
					if ($mode == 'media') {
						$path = explode("?", $fname);
						$file = pathinfo(trim($path[0]));
						$fpath = $this->rec_search_file_path($upload_dir, $file);
						$srcs[] = $fpath;
					} else {
						$srcs[] = $fname;
					}
				}
			}
			return array('file_names' => $file_names, 'srcs' => $srcs);
		}
		
		public function get_random_dateTime($startDate, $endDate) {
			$startDate = strtolower(trim($startDate)); 
			$endDat = strtolower(trim($endDate));
		    $randomTime = mt_rand(strtotime($startDate), strtotime($endDate));
		    return date('Y-m-d H:i:s', $randomTime);
		}
	}
}