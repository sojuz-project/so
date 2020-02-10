<?php
/**
 * wpSmartImportView Class Doc Comment
 *
 * @category Class
 * @package  wpSmartImportView
 * @author   phxsolution
 */
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly
if(!class_exists('wpSmartImportView')){
	class wpSmartImportView {
		
		public static function load_menu_page($filename) {
			ob_start();
			include_once wpSmartImport::getVar('admin_view','path').$filename.'.php';
			$ret = ob_get_contents();
			ob_end_clean();
			return $ret;
		}

		public static function load($page, $action, $data = array()) {
			ob_start();
			global $session;
			extract($data);
			$pages = wpSmartImport::getVar('pages');
			if (!empty($page)) {
				if ($page == $pages[0]) {
					if ($action == '') {
						include_once wpSmartImport::getVar('admin_view', 'path').'import/index.php';
					} else {
						require_once wpSmartImport::getVar('admin_view', 'path').'import/'.$action.'.php';
					}
					
				} elseif ($page == $pages[1]) {
					if ($action == '') {
						include_once wpSmartImport::getVar('admin_view', 'path').'manage/index.php';
					} else {
						require_once wpSmartImport::getVar('admin_view', 'path').'manage/'.$action.'.php';
					}
				} elseif ($page == $pages[2]) {
					if ($action == '') {
						include_once wpSmartImport::getVar('admin_view', 'path').'files/index.php';
					} else {
						require_once wpSmartImport::getVar('admin_view', 'path').'files/'.$action.'.php';
					}
				}
				$ret = ob_get_contents();
				ob_end_clean();
				return $ret;
			}
			return '';
		}
	}
}