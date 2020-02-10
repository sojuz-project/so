<?php 
if (!defined('ABSPATH')) { exit; }
if(!class_exists('wpSmartImportCheck')){
	class wpSmartImportCheck {
	
		function __construct() {
	        $this->check_redirect();
		}
	
		private function check_redirect() {
			$pages = wpSmartImport::getVar('pages');
			$wpSmartImportCommon = new wpSmartImportCommon;
			$wpsiQuery = new wpSmartImportQuery;
			$request = wpsi_helper::recursive_sanitize_text_field($_POST);
			if(!empty($request)) {
				$page = $pages[0];
				if (isset( $request['wpsi_submit'] )) {
					$submit = $request['wpsi_submit'];
					switch ($submit) {
					    case 'upload':
					    	if ($wpSmartImportCommon->update_session($request['wpsi_upload']))
					    	{
					    		if (isset($request['wpsi_upload']['willUse']) && !empty($request['wpsi_upload']['file_name'])) { 
					    			$wpsiQuery->wpsi_saveFile($request['wpsi_upload']);
					    		}
								wpSmartImport::wpsi_redirect(array('page' => $page, 'action' => 'element'));
					    	}
					        break;
					    case 'element':
					    	if ($wpSmartImportCommon->update_session($request['wpsi_element'])) {
								wpSmartImport::wpsi_redirect(array('page' => $page, 'action' => 'template'));
							}
					        break;
					    default:
					     	break;
					}
				}
			}
		}

		public function session_check() {
			$pages = wpSmartImport::getVar('pages');
			global $session;
			/*if (empty($session) && (isset($_GET['action']) && $_GET['action'] !='index') && !isset($_GET['id']) || (isset($_POST['id']) && empty($_POST['id']))) {
				wpSmartImport::wpsi_redirect( array( 'page' => $pages[0], 'action' => 'index' ));
			}*/
		}
	} // End wpSmartImportCheck Class
	new wpSmartImportCheck;	
}