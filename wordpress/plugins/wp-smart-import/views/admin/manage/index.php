<form method="post">
  <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST["page"]); ?>" /> 
<?php $wpsi_manage_controller = new wpsi_manage_controller(); 
	if (isset($_POST['s'])){
 		$wpsi_manage_controller->prepare_items(sanitize_text_field($_POST['s']));
	 } else {
	 	$wpsi_manage_controller->prepare_items();
	 }
	$wpsi_manage_controller->search_box('search', 'search_id');
	$wpsi_manage_controller->display();
?>
</form>