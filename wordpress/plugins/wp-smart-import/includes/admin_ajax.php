<?php 
/**
 * wpsiAjaxController Class Doc Comment
 *
 * @category Class
 * @package  wpsiAjaxController
 * @author   phxsolution
 */
if (!defined('ABSPATH')) { exit; }
if (!class_exists('wpsiAjaxController')) {
	class wpsiAjaxController {
		
	    static public $xmlcnt = array();
	    static public $xml = array();
	    static public $limit = 5;
		public static function wpsi_recurse_xml($xml, $parent = "") {
		    $child_count = 0;
		    foreach ($xml as $key => $value):
		        $child_count++;
		        // No childern, aka "leaf node".
		        self::$xmlcnt[] = $key;
		        if (self::wpsi_recurse_xml($value , $parent."/".$key) == 0 ) {}
		    endforeach;
		   	return $child_count;
	    }

    	public function xmlDomElement($dom, $element='', $node_num = '') {
	        $i = 0;
	        $i = $node_num !=='' ? 0 : $node_num;
	        $array = array();
	        while(is_object($doc = $dom->getElementsByTagName("$element")->item($i)))
	        {
	            foreach ($doc->childNodes as $nodename) {
	            	$nv = trim($nodename->nodeValue);
					if ($nodename->nodeName == '#text' && !empty($nv)) {
	                    $xml_elem[$i][$element]  = $nv;
	                    $xml_keys[$element] = $element;
	                } elseif($nodename->nodeName != '#text') {
	                    $xml_elem[$i][$nodename->nodeName] = $nv;
	                    $xml_keys[$nodename->nodeName] = $nodename->nodeName;
	                }
	            }
	        	$i++;
	     	}
	     	$array['xml_elem'] = $xml_elem;
	     	$array['xml_keys'] = $xml_keys;
	        return $array;
	    }

	    static public function checkfile() { // Check Session Or File Exist  
	    	$res = array('status' => 'error', 'dom_obj'=> '');
	    	$file_path = wpSmartImport::getVar('file_path', 'session');
	    	if (isset($file_path)) {
	            $upload_dir = wp_upload_dir();
	            $upload_path = $upload_dir["basedir"]."/".wpSmartImport::getVar('folder_name')."/".$file_path;
	            $pathinfo = pathinfo($upload_path);
	            $extension = $pathinfo['extension'];
	            if (file_exists($upload_path)) {
	                if ($extension == 'xml') {
	                	$dom = new DomDocument();
	                    $dom->load($upload_path) or die("error");
	                    $res = array('status' => 'success', 'dom_obj' => $dom);
	                }
	            }
	        	return $res;
	        }
	        return false;
	    }

	    public function get_xml_element($elements, $node_num = '') {
	    	$res = self::checkfile();
	    	if ($res['status'] == 'success') {
	    		$dom = $res['dom_obj']; // DomDocument Object
	    		$xml_elem = self::xmlDomElement($dom,$elements,$node_num);
	    		$xml_elem['total_page'] = $dom->getElementsByTagName("$elements")->length;
	    		return $xml_elem;
	    	}
	    	return '';
	    }

	    /*--------------------XML Functions ---------------------*/
	    
	    public static function xml_find_repeating(DOMElement $el, $path = '/') {
			$path .= $el->nodeName;
			if (!$el->parentNode instanceof DOMDocument) {
				$path .= '[1]';
			}
			$children = array();
			foreach ($el->childNodes as $child) {
				if ($child instanceof DOMElement) {
					if (!empty($children[$child->nodeName])) {
						return $path.'/'.$child->nodeName;
					} else {
						$children[$child->nodeName] = true;
					}
				}
			}
			// reaching this point means we didn't find anything among current element children, so recursively ask children to find something in them
			foreach ($el->childNodes as $child) {
				if ($child instanceof DOMElement) {
					$result = self::xml_find_repeating($child, $path.'/');
					if ($result) {
						return $result;
					}
				}
			}
			// reaching this point means we didn't find anything, so return element itself if the function was called for it
			if ('/'.$el->nodeName == $path) {
				return $path;
			}
			return NULL;		
		}	

		public static $option_paths = array();
		public static function render_xml_elements_for_filtring(DOMElement $el, $path ='', $lvl = 0) {	
			if ("" != $path) { 
				if ($lvl > 1) $path .= "->" . $el->nodeName; else $path = $el->nodeName; 
				if (empty(self::$option_paths[$path])) 
					self::$option_paths[$path] = 1;
				else
					self::$option_paths[$path]++;
				echo '<option value="'.$path.'['. self::$option_paths[$path] .']">'.$path.'['. self::$option_paths[$path].']</option>';
			}
			else $path = $el->nodeName;		
					
			foreach ($el->attributes as $attr) {
				echo '<option value="'.$path .'['. self::$option_paths[$path] .']'. '/@' . $attr->nodeName.'">'. $path .'['. self::$option_paths[$path] .']'. '@' . $attr->nodeName . '</option>';
			}
			if ($el->hasChildNodes()) {
				foreach ($el->childNodes as $child) {
					if ($child instanceof DOMElement) 
						self::render_xml_elements_for_filtring($child, $path, $lvl + 1);
				}
			}		
		}

		public static function render_xml_element(DOMElement $el, $shorten = false, $path = '/', $ind = 1, $lvl = 0)
		{
			$path .= $el->nodeName;	
			$alternativePath = $path;	
			if (!$el->parentNode instanceof DOMDocument and $ind > 0) {
				$path .= "[$ind]";
			}		
			
			echo '<div class="xml-element lvl-'.$lvl.' lvl-mod4-'.($lvl % 4).'" title="'.$path . '">';
			//if ($el->hasAttributes()){
				echo '<div class="xml-element-xpaths">'; self::render_element_xpaths($el, $alternativePath, $ind, $lvl); echo '</div>';
			//}
			if ($el->hasChildNodes()) {
				$is_render_collapsed = $ind > 1;
				if ($el->childNodes->length > 1 or ! $el->childNodes->item(0) instanceof DOMText or strlen(trim($el->childNodes->item(0)->wholeText)) > 40) {
					echo '<div class="xml-expander">' . ($is_render_collapsed ? '+' : '-') . '</div>';
				}
				echo '<div class="xml-tag opening">&lt;<span class="xml-tag-name">' . $el->nodeName . '</span>'; self::render_xml_attributes($el, $path . '/'); echo '&gt;</div>';
				if (1 == $el->childNodes->length and $el->childNodes->item(0) instanceof DOMText) {
					self::render_xml_text(trim($el->childNodes->item(0)->wholeText), $shorten, $is_render_collapsed);
				} else {
					echo '<div class="xml-content' . ($is_render_collapsed ? ' collapsed' : '') . '">';
					$indexes = array();										
					foreach ($el->childNodes as $eli => $child) {
						if ($child instanceof DOMElement) {
							empty($indexes[$child->nodeName]) and $indexes[$child->nodeName] = 0; $indexes[$child->nodeName]++;
							self::render_xml_element($child, $shorten, $path . '/', $indexes[$child->nodeName], $lvl + 1); 
						} elseif ($child instanceof DOMCdataSection) {
							self::render_xml_text(trim($child->wholeText), $shorten, false, true); 
						} elseif ($child instanceof DOMText) {
							if ( $el->childNodes->item($eli - 1) and ($el->childNodes->item($eli - 1) instanceof DOMCdataSection) ){

							}
							elseif( $el->childNodes->item($eli + 1) and ($el->childNodes->item($eli + 1) instanceof DOMCdataSection) ){

							}
							else{								
								self::render_xml_text(trim($child->wholeText), $shorten); 
							}
						} elseif ($child instanceof DOMComment) {
							if (preg_match('%\[pmxi_more:(\d+)\]%', $child->nodeValue, $mtch)) {
								$no = intval($mtch[1]);
								echo '<div class="xml-more">[ &dArr; ' . sprintf(__('<strong>%s</strong> %s more', 'wp_smart_import'), $no, _n('element', 'elements', $no, 'wp_smart_import')) . ' &dArr; ]</div>';
							}
						}
					}
					echo '</div>';
				}
				echo '<div class="xml-tag closing">&lt;/<span class="xml-tag-name">' . $el->nodeName . '</span>&gt;</div>';
			} else {
				echo '<div class="xml-tag opening empty">&lt;<span class="xml-tag-name">' . $el->nodeName . '</span>'; self::render_xml_attributes($el); echo '/&gt;</div>';
			}
			echo '</div>';
		}

		protected static function render_xml_text($text, $shorten = false, $is_render_collapsed = false, $is_cdata = false)
		{
			if (empty($text) and 0 !== (int)$text) {
				return; // do not display empty text nodes
			}
			if (preg_match('%\[more:(\d+)\]%', $text, $mtch)) {
				$no = intval($mtch[1]);
				echo '<div class="xml-more">[ &dArr; ' . sprintf(__('<strong>%s</strong> %s more', 'wp_all_import_plugin'), $no, _n('element', 'elements', $no, 'wp_all_import_plugin')) . ' &dArr; ]</div>';
				return;
			}
			$more = '';
			if ($shorten and preg_match('%^(.*?\s+){20}(?=\S)%', $text, $mtch)) {
				$text = $mtch[0];
				$more = '<span class="xml-more">[' . __('more', 'wp_all_import_plugin') . ']</span>';
			}			
			$is_short = strlen($text) <= 40;			
			$text = htmlspecialchars($text);
			if ($is_cdata){
				$text = "<span class='wpallimport-cdata'>" . htmlspecialchars("<![CDATA[") . "</span> " . $text . " <span class='wpallimport-cdata'>" . htmlspecialchars("]]>") . "</span>";
			}			
			//$text = preg_replace('%(?<!\s)\b(?!\s|\W[\w\s])|\w{20}%', '$0&#8203;', $text); // put explicit breaks for xml content to wrap
			echo '<div class="xml-content textonly' . ($is_short ? ' short' : '') . ($is_render_collapsed ? ' collapsed' : '') . '">' . $text . $more . '</div>';
		}

		public static function get_xml_path(DOMElement $el, DOMXPath $xpath)
		{
			for ($p = '', $doc = $el; $doc and ! $doc instanceof DOMDocument; $doc = $doc->parentNode) {
				if (($ind = $xpath->query('preceding-sibling::' . $doc->nodeName, $doc)->length)) {
					$p = '[' . ++$ind . ']' . $p;
				} elseif (!$doc->parentNode instanceof DOMDocument) {
					$p = '[' . ($ind = 1) . ']' . $p;
				}
				$p = '/' . $doc->nodeName . $p;
			}
			return $p;
		}

		protected static function render_xml_attributes(DOMElement $el, $path = '/')
		{
			foreach ($el->attributes as $attr) {
				echo ' <span class="xml-attr" title="' . $path . '@' . $attr->nodeName . '"><span class="xml-attr-name">' . $attr->nodeName . '</span>=<span class="xml-attr-value">"'. esc_attr($attr->value) . '"</span></span>';
			}
		}	

		protected static function render_element_xpaths(DOMElement $el, $path = '/', $ind = 1, $lvl = 0)
		{ ?>
			<ul id="menu-<?php echo str_replace('/', '-', esc_attr($path)); ?>" class="ui-helper-hidden">
				<?php foreach ($el->attributes as $attr) : if ( empty($attr->value) ) continue; ?>
			    <li data-command="action1" title="<?php echo esc_attr($path . '[@'. $attr->nodeName .' = "' . esc_attr($attr->value) . '"]'); ?>">
			    	<a href="#"><?php echo $path . '[@'. $attr->nodeName .' = "' . esc_attr($attr->value) . '"]'; ?></a>
			    </li>
			    <li data-command="action2" title="<?php echo esc_attr($path . '[@'. $attr->nodeName .'[contains(.,"' . esc_attr($attr->value) . '")]]'); ?>">
			    	<a href="#"><?php echo $path . '[@'. $attr->nodeName .'[contains(.,"' . esc_attr($attr->value) . '")]]'; ?></a>
			    </li>
				<?php endforeach; ?>
				<?php 
				$altNode = null;
				$altNodeText = null;
				$parentNode = $el->parentNode;
				$grandNode = $parentNode->parentNode;
				
				if (!$grandNode instanceof DOMDocument and $grandNode instanceof DOMElement){		
					$equalsElements = 0;
					foreach ($grandNode->childNodes as $child) {
						if ($child instanceof DOMElement) {
							if ($child->nodeName == $parentNode->nodeName) {
								$equalsElements++;
								if ($equalsElements > 1)
									break;
							}
						}
					}													
					if ($equalsElements > 1) {
						if ($parentNode->hasChildNodes()) {
							foreach ($parentNode->childNodes as $i => $child) {
								if ($child instanceof DOMElement) {
									if ($child->nodeName != $el->nodeName) {
										$altNode = $child;
										if ($child->hasChildNodes()) {
											foreach ($child->childNodes as $i => $txtChild) {
												if ($txtChild instanceof DOMText) {
													$altNodeText = $txtChild;
													break;
												}
											}
										}
										break;
									}
								}
							}
						}
						if (!empty($altNode) and !empty($altNodeText)) {
							$pathArgs = explode('/', $path);
							array_pop($pathArgs);						
							array_pop($pathArgs);		
							$vpath = esc_attr(implode('/', $pathArgs) . '/' . $parentNode->nodeName . '[contains('. $altNode->nodeName .',"' . esc_attr($altNodeText->wholeText) . '")]/' . $el->nodeName);				
							?>
							<li data-command="action3" title="<?php echo $vpath; ?>">
						    	<a href="#">
						    		<?php echo $vpath; ?>
						    	</a>
						    </li>						
						    <?php
						}
					}					
				}				
				?>								
			</ul>
			<?php			
		}

	   /*--------------------END ---------------------*/

		function wpsi_xml_preview() {
			wpSmartImportCommon::verify_ajax($_REQUEST['_nonce']);
	  		$res = self::checkfile();	
	    	if ($res['status'] == 'error') {
	    		echo json_encode(array('content' => '<h1 class="wpsi-error-color"> Error ! </h1>'));
	    		wp_die();
	    	}
	    	$request = wpsi_helper::recursive_sanitize_text_field($_REQUEST);
        	$selected_node = $request['node'];
        	$node_num = $request['node_num'];
        	$show_element = $request['node_num'];
        	$dom = $res['dom_obj'];
          	$elements = $dom->getElementsByTagName("$selected_node");
            $total_node = $dom->getElementsByTagName("$selected_node")->length;
            ob_start();
            ?>
    		<table class="wpsi-table wpsi-pagination table-fixheader elements-preview-table">
             	<thead>
                 	<tr>
                 	 	<td colspan="3" class="node-title">
                 	 		<h3 style="color:#e14d43;margin-top: 0;">
                 	 			<?php wpsi_helper::_e("Element :".$selected_node); ?>
                 	 		</h3><hr>		
                 	 	</td>
                 	</tr>
                 	<?php if ($total_node > 1): ?>
                    <tr>
                        <td> 
                            <button type="button" class="wpsi-xml-preview-pagination button-previous 
                            <?php echo $node_num == 1 ? "button-disabled":''; ?>" data-val="<?php echo esc_url($node_num-1) ?>"
                            	<?php echo $node_num == 1 ? "disabled":''; ?> > 	
                            	<span class="dashicons dashicons-arrow-left-alt2"></span>
                           	</button> 
                        </td>
                        <td>
                            <input type="number" name="wpsi_element[node_num]" class="node_num" min="1" max="<?php echo esc_attr($total_node); ?>" value="<?php echo esc_attr($node_num); ?>" id="node_num" /> &nbsp; / &nbsp;
                            <input type="hidden" name="node_num" value="<?php echo esc_attr($node_num); ?>" />
                           	<span class="total-element"><?php echo $total_node; ?></span>
                        </td>
                        <td>
                            <button type="button" class="wpsi-xml-preview-pagination button-next 
                            <?php echo $node_num == $total_node ? "button-disabled":''; ?>"" 
                            data-val="<?php echo esc_attr($node_num+1) ?>" <?php echo $node_num == $total_node ? "disabled":''; ?>>
                                <span class="dashicons dashicons-arrow-right-alt2"></span>
                            </button>
                        </td>
                    </tr>
                  <?php endif; ?>
             	</thead>
                <tbody>
                    <tr>
                        <td colspan="3" style="text-align: left;" class="row-data">
                    	<?php self::render_xml_element($elements->item($elements->length > 1 ? $show_element-1 : 0), false, '/'); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php echo  json_encode(array(
                    		'content' 	=> ob_get_clean(),
                    		'response' 	=> 'true',
                    		'node_num' 	=> $node_num,
                    		'element' 	=> $selected_node
                    	));
            		wp_die();
		}

		static public function wpsi_insertImport($postdata, $task) {
            global $session;
			$wpsiQuery = new wpSmartImportQuery;
			$wpSmartImportCommon = new wpSmartImportCommon;
			$admin_url = admin_url('admin.php');
            $pages = wpSmartImport::getVar('pages');
            $folder_name = wpSmartImport::getVar('folder_name');
			$file_path 	= $session['file_path'];
			$options   	= $session;
			$pathinfo 	= pathinfo($file_path);
			$file_name 	= $pathinfo['basename'];
			$extension 	= $pathinfo['extension'];
			$node      	= $session['node'];
			$res = array('status' => "error", 'msg' =>'Import Could Not Be Save');
			$data = array(
						"name" 			=> $file_name,
						"feed_type"		=> $extension,
						"file_path"		=> $folder_name. "/".$file_path,
						"options" 		=> serialize($options),
						"root_element" 	=> $node,
						"post_type" 	=> $postdata['post_type'],
						"unique_key" 	=> $postdata['unique_key'],
						"post_data" 	=> serialize($postdata),
						"last_activity" => current_time('mysql'),
	    				'count' 		=> 0,
						'created' 		=> 0,
						'updated' 		=> 0,
						'failed' 		=> 0
					);
			$format = array('%s', '%s', '%s', '%s', '%s', '%s', '%s','%s','%s','%s','%d', '%d', '%d', '%d');
			if (empty($postdata['id'])) {
				$data["created_at"] = current_time('mysql');
				$id = $wpsiQuery->wpsi_insert('wpsi_imports', $data, $format);
			} else {
				$where = array('id' => $postdata['id']);
				$id = $wpsiQuery->wpsi_update('wpsi_imports', $data, $where, $format);
				$id = $postdata['id'];
			}
			if (empty($id)) { return $res; }
			else {
		    	$status = empty($postdata['id']) ? "Created" : "Updated";
		    	$wpSmartImportCommon->unset_session();
		    	$res['status'] = "success";
		    	$res['msg'] = "1 Import ".$status." <a href='". esc_url("$admin_url?page=$pages[1]&action=edit&id=$id") ."'>Edit</a> ";
		    	$_SESSION['res'] = $res;
		    	if ($task == "save") {
		    		wpSmartImport::wpsi_redirect(array('page' => $pages[1]));
	    		} else {
	    			wpSmartImport::wpsi_redirect(array('page' => $pages[1], 'action' => 'update', 'id' => $id));
	    		}
		    }
		}

		public function wpsi_runImport() {
			wpSmartImportCommon::verify_ajax($_REQUEST['_nonce']);
			global $wpdb;
			$wpsiQuery = new wpSmartImportQuery;
			$request = wpsi_helper::recursive_sanitize_text_field($_POST);
			$res = array('status' => 'error', 'msg' => 'Error Try Again !');
			$import_id = isset($request['id']) && !empty($request['id']) ? absint($request['id']) : 0 ;
			if ($import_id !== 0) {
				$data = $wpsiQuery->wpsi_getRow('wpsi_imports', $import_id);
				if (empty($data)) {
					wpsi_helper::wp_die_request('Import Not Found');
				}
			} else {
				wpsi_helper::wp_die_request('Invalid Import Id');
			}
			$batch_no = intval($request['batch_no']);
			$batchs	= $request['batches'][$batch_no];
		    $total_batch = (int)$request['total_batch'];
		    if ($total_batch < 1 || !is_array($batchs) || empty($batchs)) {
		    	wpsi_helper::wp_die_request('Something Wrong Try After Some Time!');
		    }
		    if (!post_type_exists($data->post_type)) {
		    	wpsi_helper::wp_die_request('Post Type \''. $data->post_type .'\' Not Exist');
		    }
			$post_data = wpsi_helper::TrimArray(unserialize($data->post_data));
	    	$pdata = wpSmartImportCommon::parse_post_data($post_data, $batchs);
	    	$end_key = end(array_keys($request['batches']));
	    	$data = self::insert_post(wpsi_helper::recursive_sanitize_text_field($pdata), $import_id, $batchs);
	    	$count = $count_created = $count_updated = $count_failed = 0;
	    	$count_created = $data['count_created'];
	    	$count_updated = $data['count_updated'];
	    	$count_failed = $data['count_failed'];
	    	$count = $data['count'];
			$next_batch = (int)$batch_no + 1;// next batch
			$progress = ($next_batch / $total_batch) * 100; // progressBar
			$proccessing = $batch_no == $end_key ? 'stop':'run'; // stop recursive ajax function
	    	// Update Import Record
			if (!empty($import_id)) {
	    		$table = $wpdb->prefix."wpsi_imports";
				$querystr = "UPDATE $table SET 
					created = $table.created + $count_created,
					updated = $table.updated + $count_updated,
					failed = $table.failed + $count_failed,
					count = $table.count + $count
				 WHERE $table.id = $import_id";
				$wpdb->query($querystr);
			}
			$res = array(
				'status' 		=> 'success', 
				'batch_no' 		=> $next_batch,
				'total_batch' 	=> $total_batch, 	
				'proccessing' 	=> $proccessing, 
				'new_width' 	=> (int)$progress,
				'post_type' 	=> $post_data['post_type'], 
				'created'		=> $count_created,
				'updated' 		=> $count_updated,
				'failed' 		=> $count_failed,
				'count' 		=> count($pdata),
			); 
			echo json_encode($res);
       		wp_die();
		}
		
		public function insert_post($pdata, $import_id, $batchs) {
			$wpSmartImportCommon = new wpSmartImportCommon;
			$wpsiQuery = new wpSmartImportQuery;
			global $session;
			$res = array('status' => 'error', 'msg' => 'Error Try Again !');
			$post_data = $pdata;
	    	$generate_single_check_featured_Img = true;	
	    	$count_created = 0;
	    	$count_updated = 0;
	    	$count_failed = 0;
	    	$count = 0;
			if (!empty($pdata)) {
				$res = array();
				foreach ($pdata as $indx => $pvalue) {
					$count++;
					if (empty($pvalue['post_title'])) {
						$pvalue['post_title'] = 'No Title';
					}
					$new_post = array(
					  'post_author'    => (int)$pvalue['post_auth'],
					  'post_title'     => wp_strip_all_tags($pvalue['post_title']),
					  'post_content'   => $pvalue['post_des'],
					  'post_status'    => $pvalue['post_status'],
					  'post_password'  => $pvalue['post_password'],
					  'comment_status' => $pvalue['comment_status'],
					  'post_type'      => $pvalue['post_type'],
					  'ping_status'    => $pvalue['ping_status'],
					  'post_name'      => $pvalue['slug'],
					  'menu_order'     => empty($pvalue['menu_order']) ? 0 : intval($pvalue['menu_order']),
					);
					if (isset($pvalue['update_post'])) {
						$data = get_page_by_title($new_post['post_title'], "OBJECT", $new_post['post_type']);
						if (is_object($data)) {
							if (FALSE === get_post_status($data->ID)) {
							  	$post_id = wp_insert_post($new_post, true);
							  	$count_created++;
							  	$pfrom ='create';
							} else {
								$post_id = $data->ID;
							  	$new_post['ID'] = $data->ID;
								wp_update_post($new_post, true);
								$count_updated++;
								$pfrom ='update';
							}
						} else {
							$post_id = wp_insert_post($new_post, true);
							$count_created++;
							$pfrom ='create';
						}
					} else {
						$post_id = wp_insert_post($new_post, true);
						$count_created++;
						$pfrom ='create';
					}
		        	if (is_wp_error($post_id)) {
						$res = array('status' =>'error','msg'=> $post_id->get_error_message());
						$count_failed++;
						if ($pfrom == "create") {
							$count_created--;
						} else {
							$count_updated--;
						}
					} else {
						$res['status'] = 'success';
						if (empty($pvalue['id'])) {
							$res['msg'] = 'Post Type : "'.ucfirst($pvalue['post_type']).'" '.'Successfully Created';
						} else {
							$res['msg'] = 'Post Type : "'.ucfirst($pvalue['post_type']).'" '.'Successfully Updated';
						}
					}
		        	$post_ids[] = $post_id;
					if ($res['status'] == 'success') {

						/*****************************************
						*              Image Section             *
						******************************************/
						include wpSmartImport::getVar('inc', 'path').'save_post/image.php';

						/*****************************************
						* Taxonomies , Category , Tags Section   *
						******************************************/
						include wpSmartImport::getVar('inc', 'path').'save_post/tax_cat_tag.php';

						/*****************************************
						*              Date Section              *
						******************************************/
						include wpSmartImport::getVar('inc', 'path').'save_post/date.php';

						/*****************************************
						*           Custom-field Section          *
						******************************************/
						include wpSmartImport::getVar('inc', 'path').'save_post/custom_field.php';

						/*****************************************
						*             Database Action            *
						******************************************/
						if (!empty($post_id) && !empty($import_id)) {
							if(!$wpsiQuery->check_post_exist($post_id, $pvalue['unique_key'])) {
								$data = array(
										"post_id"	=> $post_id,
										"import_id"	=> $import_id,
										"unique_key" => $pvalue['unique_key'],
									);
								$format =  array('%d', '%d', '%s'); 
								$id = $wpsiQuery->wpsi_insert('wpsi_posts', $data, $format);
							}
						}
					}
				}
			}

			// save woocommerce add-ons data
			$woocommerce_postTypse = array('product','shop_order');
        	if (wpSmartImportCommon::woocommerce_exist() && in_array($session['post_type'], $woocommerce_postTypse)) {
        		$data = array(
        			'import_id' => $import_id , 
        			'batchs' 	=> $batchs,
        			'post_ids' 	=> $post_ids,
        			'post_data'	=> $pdata,
        			'post_type' => $session['post_type'],
        		);
        		do_action('wpsi_save_'.$session['post_type'], $data);
        	}
			$res['count_created'] =  $count_created;
			$res['count_updated'] = $count_updated;
			$res['count_failed'] = $count_failed;
			$res['count'] = $count;
			return $res;
		}

		public function wpsi_images_preview() {
			$wpSmartImportCommon = new wpSmartImportCommon;
			wpSmartImportCommon::verify_ajax($_REQUEST['_nonce']);
			parse_str($_POST['formData'], $formData);
			$total_node = sanitize_text_field($_POST['count']);
			$node_num = sanitize_text_field($_POST['node_num']);
			$pdata = wpsi_helper::TrimArray($formData);
			$post_images = array();
			if ($pdata['img_from'] == "media-file") {
				$post_images = $wpSmartImportCommon->parse_string($pdata['media_imgs'], $node_num-1);
			} else {
				$post_images = $wpSmartImportCommon->parse_string($pdata['download_imgs'], $node_num -1);
			}
			$mode = $pdata['img_from'] == 'media-file' ? 'media' : 'download';
			$res = $wpSmartImportCommon->sort_and_get_images($post_images, $mode);
			$file_names = $res['file_names'];
			$srcs = $res['srcs'];
			ob_start();
			include_once wpSmartImport::getVar('admin_view', 'path').'templates/image_preview.php';
			echo json_encode(array('content' => ob_get_clean()));
			wp_die();
		}

		public function insert_term() {
			wpSmartImportCommon::verify_ajax($_REQUEST['_nonce']);
			$wpSmartImportCommon = new wpSmartImportCommon;
			$_POST = wpsi_helper::recursive_sanitize_text_field($_POST);
			$term_names =  wpsi_helper::_d($_POST, 'term_names');
			$taxonomy_name =  wpsi_helper::_d($_POST, 'taxonomy_name');
			$term_names = $wpSmartImportCommon->parse_string($term_names);
			$response = array();
			if (!empty($term_names)) {
				$term_name_list = explode(',', $term_names);
				if (!empty($term_name_list)) {
					foreach ($term_name_list as $term_name) {
						$term = term_exists( $term_name, $taxonomy_name);
						if ($term !== 0 && $term !== null) {
							$response[] = array('msg' => 'Already Exist','status' =>'error','title' => $term_name );
						} else {
							$data = wp_insert_term($term_name, $taxonomy_name);
							$response[] = array('msg' => 'Add Successfully', 'status' =>'success','title' => $term_name);
						}
					}
				} else {
					$response[] = array('msg' => 'Not Valid Input Try Again ','status' =>'error','title' => '');
				}
				echo json_encode(array('content' => $response));
				wp_die();
			}
		}

		public function get_total_batch_for_import() {
			wpSmartImportCommon::verify_ajax($_REQUEST['_nonce']);
			$wpsiQuery = new wpSmartImportQuery;
			$wpsi_posts = $wpsiQuery->retrieve_posts($_POST['id']);
			$total_batch = 0;
			if (!empty($wpsi_posts)) {
				$batchs = array_chunk($wpsi_posts, self::$limit);
				$total_batch = count($batchs);
			}
			echo json_encode(array('total_batch' => $total_batch));
       		wp_die();
		}

		public function get_total_batch_for_file() {
			wpSmartImportCommon::verify_ajax($_REQUEST['_nonce']);
			$folder_name = wpSmartImport::getVar('folder_name');
			global $wpdb;
			$table = $wpdb->prefix.'wpsi_imports';
			$wpsiQuery = new wpSmartImportQuery;
			$data = $wpsiQuery->wpsi_getRow("wpsi_files", $_POST['id']);
			$querystr = "SELECT * FROM $table WHERE file_path = '$folder_name/$data->file_path' ";
			$result = $wpdb->get_results($querystr);
			$post_array = array();
			if (!empty($result)) {
				foreach ($result as $idx => $array) {
					$wpsi_posts = $wpsiQuery->retrieve_posts($array->id);
					if (!empty($wpsi_posts)) {
						array_walk( $wpsi_posts, function($item) use (&$post_array) {
							 $post_array[] = $item;
						});
					}
				}
			}
			if (!empty($post_array)) {
				$batchs = array_chunk($post_array, self::$limit);
				$total_batch = count($batchs);
			}
			echo json_encode(array(
					'total_batch' 	=> $total_batch,
					'batchs' 		=> $batchs,
					'post' 			=> count($post_array),
					'import' 		=> count($result)
				));
       		wp_die();
		}

		public function manage_import_files() {
			$request = wpsi_helper::recursive_sanitize_text_field($_POST);
			wpSmartImportCommon::verify_ajax($request['_nonce']);
			$folder_name = wpSmartImport::getVar('folder_name');
			$fileCount = 0;
			$postCount = 0;
			$importCount = 0;
			$proccessing = 'stop';
			$batchs = array();
			$statusCode = intval($request['statusCode']);
			$idx = isset($request['idx']) ? (int)$request['idx'] : 0;
			$ID = isset($request['id']) && !empty($request['id']) ? absint($request['id']) : 0;
			if (empty($ID))
				wpsi_helper::wp_die_request('Invalid Request');

			global $wpdb;
			$table = $wpdb->prefix.'wpsi_imports';
			$wpsiQuery = new wpSmartImportQuery;
			$data_row = $wpsiQuery->wpsi_getRow("wpsi_files", $ID);
			if (!empty($data_row)) {
				$querystr = "SELECT * FROM $table WHERE file_path = '$folder_name/$data_row->file_path'";
				$result = $wpdb->get_results($querystr);
				if ($statusCode == 1) { //Delete File Record
					$response = $wpsiQuery->delete_file_by('id', $ID);
					if ($response) {
						$fileCount++;
					}
				} else if ($statusCode == 2 || $statusCode == 4) {
				 	//Delete Import or Delete Post and Import
					$hard_delete = $statusCode == 4 ? true : false; 
					$result_count = count( $result );
					if (!empty($result)) {
						$wpsi_posts = $wpsiQuery->retrieve_posts($result[0]->id);
						if (!empty($wpsi_posts)) {
							$batchs = array_chunk($wpsi_posts, self::$limit);
						}
					}
					if (!empty($batchs)) {
						$postCount = self::delete_post($batchs[0], $hard_delete); 
						if (isset($batchs[1])) {
							$proccessing = 'run';
						}
					}
					if ($proccessing == 'stop' && isset($result[0])) {
						$del_imp = $wpsiQuery->delete_import($result[0]->id);
						if ($del_imp) {
							$importCount++;
						}
						if (isset($result[1]))
							$proccessing = 'run';
					}
				} else if ($statusCode == 3) { //Delete Post
					if (!empty($result)) {
						$wpsi_posts = $wpsiQuery->retrieve_posts($result[$idx]->id);
						if (!empty($wpsi_posts)) {
							$batchs = array_chunk($wpsi_posts, self::$limit);
						}
					}
					if (!empty($batchs)) {
						$postCount = self::delete_post($batchs[0], true);
						if (isset($batchs[1])) {
							$proccessing = 'run';
						}
					}
					$total_batch = count($batchs) > 0 ? count($batchs)-1 : 0;
					if ($total_batch == 0){
						$data = array(
								'count' => 0,
								'created' => 0,
								'updated' => 0,
								'failed' => 0
							);
						$where = array('id' => $result[$idx]->id);
						$format =  array('%d', '%d', '%d', '%d');
						$wpsiQuery->wpsi_update('wpsi_imports', $data, $where, $format);
					}
					if (isset( $result[$idx+1]) && $total_batch == 0) {
						$proccessing = 'run';
						$idx += 1;
					} else if (!isset( $result[$idx+1]) && $total_batch == 0) {
						$proccessing = 'stop';
					}
				}	
				$res = array(
					'status' 			=> 'success',
					'proccessing' 		=> $proccessing,
					'id' 				=> $ID,
					'idx' 				=> $idx,
					'file_name' 		=> $data_row->name,
					'deleted_post' 		=> $postCount,
					'deleted_import' 	=> $importCount,
					'deleted_file' 		=> $fileCount,
					'total_batch' 		=> $total_batch
				);
			} else {
				$res = array('status' => 'error', 'msg' => 'Import Not Found !');
			}
			echo json_encode($res); 
       		wp_die();
		}

		public function manage_imports() {
			$request = wpsi_helper::recursive_sanitize_text_field($_POST);
			wpSmartImportCommon::verify_ajax($request['_nonce']);
			$ID = isset($request['id']) &&  !empty($request['id']) ? absint($request['id']) : 0;
			if (empty($ID))
				wpsi_helper::wp_die_request('Invalid Request');
			
			$wpsiQuery = new wpSmartImportQuery;	
			$deleted_post_count = 0;
			$deleted_import = 0;
			if ($row_data = $wpsiQuery->wpsi_getRow('wpsi_imports', $ID)) {
				$batchs = array();
				$proccessing = 'stop';
				parse_str($request['formData'], $formData);
				$wpsi_posts = $wpsiQuery->retrieve_posts($ID);
				if (array_key_exists('delete_post', $formData)) {
					if (!empty($wpsi_posts)) {
						$batchs = array_chunk($wpsi_posts, self::$limit);
						$deleted_post_count = self::delete_post($batchs[0], true); 
						if (isset($batchs[1])) {
							$proccessing = 'run';
						}
					}
					if ($proccessing == 'stop') {
						if (isset($formData['delete_import'])) {
							$del_imp = $wpsiQuery->delete_import($ID);
							if ($del_imp) {
								$deleted_import++;
							}
						} else {
							$data = array(
								'count' => 0,
								'created' => 0,
								'updated' => 0,
								'failed' => 0
							);
							$where = array('id' => $ID);
							$format = array('%d', '%d', '%d', '%d');
							$wpsiQuery->wpsi_update('wpsi_imports', $data, $where, $format);
						}
					}
				} else {
					if (!empty($wpsi_posts)) {
						$batchs = array_chunk($wpsi_posts, self::$limit);
						$deleted_post_count = self::delete_post($batchs[0]);
						if (isset($batchs[1])) {
							$proccessing = 'run';
						}
					}
					if ($proccessing == 'stop') {
						if (isset($formData['delete_import'])) {
							$del_imp = $wpsiQuery->delete_import($ID);
							if ($del_imp) {
								$deleted_import++;
							}
						}
					}
				}
				$res = array(
					'status' 			=> 'success',
					'proccessing' 		=> $proccessing,
					'id' 				=> $ID,
					'post_type' 		=> $row_data->post_type,
					'total_batch' 		=> count($batchs) > 1 ? count($batchs)-1 : count($batchs),
					'deleted_post' 		=> $deleted_post_count,
					'deleted_import'	=> $deleted_import
				);
			} else {
				$res = array('status' => 'error', 'msg' => 'Import Not Found !');
			}
			echo json_encode($res);
       		wp_die();
		}

		public function delete_post($posts, $hard_delete = false) {
			$wpsiQuery = new wpSmartImportQuery;
			$post_counter = 0;
			foreach ($posts as $key => $array) {
				$import_id = $array['import_id'];
				if (get_post_status($array['post_id'])) {
					if ($hard_delete)
						wp_delete_post($array['post_id'], true);
					if ($wpsiQuery->delete_post($array['post_id'], $import_id)) {
						$post_counter++;
					}
				}
			}
			return $post_counter;
		}
	}
	new wpsiAjaxController;
}