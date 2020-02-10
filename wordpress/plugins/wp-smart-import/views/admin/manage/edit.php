<?php  
$data = array();
$wpsiQuery = new wpSmartImportQuery;
$id = isset($_GET['id']) ? sanitize_text_field($_GET['id']) : 0 ;
$id = wpsi_helper::check_schar($id);
if ($id) {
	$data = $wpsiQuery->wpsi_getRow('wpsi_imports', $id);
}
if (empty($data) || wpsi_helper::array_key_exists_r('error', $data)) {
	echo "<h1 class='text-center error-text'> Error Data Not Found ! </h1>";
	exit();
}
$post_data = unserialize($data->post_data);
?>
<h1 class="page-title"><?php wpsi_helper::_e("#Id : ".$id); ?></h1><br/>
<?php include_once wpSmartImport::getVar('admin_view', 'path').'import/template.php'; ?>