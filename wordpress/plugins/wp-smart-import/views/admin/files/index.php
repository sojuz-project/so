<form method="post">
  	<input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" /> 
<?php 
	$wpsi_manage_controller = new wpsi_manage_file_controller(); 
	if (isset($_REQUEST['s'])) {
 		$wpsi_manage_controller->prepare_items($_REQUEST['s']);
	 } else {
	 	$wpsi_manage_controller->prepare_items();
	 }
	$wpsi_manage_controller->search_box('search', 'search_id');
	$wpsi_manage_controller->display(); 
?>
</form>
