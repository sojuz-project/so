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
global $session;
$options = unserialize($data->options);
$post_data = unserialize($data->post_data);
?>
<div id="response-content" style="display: none;">
	<div class="meter animate_progress" >
	  <span class="text-center progress-text" style="width: 0%"><span></span></span>
	</div>
	<div class="flex-container"></div>
</div>
<!-- Progress Bar -->
<div class="wpsi-template-container">
	<div class="wpsi-portlet" >
	    <div class="wpsi-portlet-title">
	        <div class="caption">
	            <span class="caption-subject">
	            	<?php wpsi_helper::_e("Run Import"); ?>
	            </span>
	        </div>
	        <div class="actions">
	        <?php
	        	$pages = wpSmartImport::getVar('pages');
	        	$admin_url = admin_url('admin.php');
		      	echo "<a href='". esc_url("$admin_url?page=$pages[1]&action=edit&id=$data->id") ."'>";
		      	wpsi_helper::_e("Edit Import");
		      	echo "</a>";
		      	echo "<a href='". esc_url("$admin_url?page=$pages[1]") ."'>";
		      	wpsi_helper::_e("Manage Import");
		      	echo "</a>";
	        ?>
	        </div>
	    </div>
		<div class="wpsi-portlet-body">
			<h3><?php wpsi_helper::_e( "File is Ready to import Click on below button and Run import" ); ?> </h3>
			<form accept="" method="post">
				<button name="wpsi_submit" class="wpsi-button wpsi-button-big btn-grp btn-next" type="button" value="update" id="run_import">
					<?php wpsi_helper::_e("Run Import"); ?>
				</button>
			</form>
		</div>
	</div>
	<div class="wpsi-portlet" >
	    <div class="wpsi-portlet-title">
	        <div class="caption">
	            <span class="caption-subject">
	            	<?php wpsi_helper::_e("Import Data"); ?>
	            </span>
	        </div>
	    </div>
		<table class="wpsi-table wpsi-table-border wpsi-table-hoverable wpsi-custom-field-tab list-table">
			<thead class="head-color-blue">
				<tr><th><?php wpsi_helper::_e("Field"); ?></th>
				    <th><?php wpsi_helper::_e("Value"); ?></th></tr>
			</thead>
		 	<tbody>
		 		<tr><td><?php wpsi_helper::_e("ID"); ?></td>
				    <td><?php echo $data->id; ?></td>
				</tr>
		 		<tr><td><?php wpsi_helper::_e("File Name"); ?></td>
				    <td><?php echo $data->name; ?></td>
		  		</tr>
		  		<tr><td><?php wpsi_helper::_e("Root Element"); ?></td>
				    <td><?php echo $data->root_element; ?> </td>
		  		</tr>
		  		<tr><td><?php wpsi_helper::_e("Unique Key"); ?></td>
				    <td><?php echo $post_data['unique_key']; ?></td>
		  		</tr>
		  		<tr><td><?php wpsi_helper::_e("Post Title"); ?></td>
				    <td><?php echo $post_data['post_title']; ?></td>
		  		</tr>
		  		<tr><td><?php wpsi_helper::_e("Post Description"); ?></td>
				    <td><?php echo $post_data['post_des']; ?></td>
		  		</tr>
		  		<tr><td><?php wpsi_helper::_e("Post Status"); ?></td>
				    <td><?php echo $post_data['post_status']; ?></td>
		  		</tr>
		  		<tr><td><?php wpsi_helper::_e("Post Author"); ?> </td>
				    <td><?php echo get_the_author_meta('display_name', $post_data['post_auth']);
				     ?> </td>
		  		</tr>
		  		<tr><td> <?php wpsi_helper::_e("Post Date"); ?> </td>
				    <td> <?php 
				    		if (array_key_exists("date_type", $post_data)) {
				    			if($post_data['date_type'] == 'auto')
				    				wpsi_helper::_e('Auto');
				    			else if($post_data['date_type'] == 'specific')
				    				echo esc_attr($post_data['post_date']);
				    			else
				    				wpsi_helper::_e('Random Date Between : '. $post_data['post_date_start']." to ".$post_data['post_date_end']);
						    }
						?>
				    </td>
		  		</tr>
		  		<tr><td><?php wpsi_helper::_e("Media Images"); ?></td>
				    <td><?php echo $post_data['media_imgs']; ?></td>
		  		</tr>
		  		<tr><td><?php wpsi_helper::_e("Download Images"); ?></td>
				    <td><?php echo $post_data['download_imgs']; ?></td>
		  		</tr>
		  		<tr><td><?php wpsi_helper::_e("Set Featured Image"); ?></td>
				    <td><?php echo $post_data['set_featured_image']; ?></td>
		  		</tr>
		  		<tr><td><?php wpsi_helper::_e("Post Type"); ?></td>
				    <td><?php echo $post_data['post_type']; ?></td>
		  		</tr>
		  		<tr><td><?php wpsi_helper::_e("Last Activity"); ?></td>
				    <td><?php echo $data->last_activity; ?></td>
		  		</tr>
			</tbody>
		</table>
		<!--List Custom Fields -->
		<?php if (!empty($post_data['custom_field_name'][0])): ?>
		<br><hr>
		<h2>Custom Fields</h2>
		<table class="wpsi-table wpsi-table-border wpsi-table-hoverable wpsi-custom-field-tab list-table">
			<thead class="head-color-blue">
				<tr><th><?php wpsi_helper::_e("Key"); ?></th>
				    <th><?php wpsi_helper::_e("Value"); ?></th>
				</tr>
			</thead>
		 	<tbody>
			<?php   $custom_field_name = $post_data['custom_field_name'];
					$custom_field_value = $post_data['custom_field_value'];
					foreach ($custom_field_name as  $idx => $value) {
						echo "<tr><td>". $value. "</td>
								<td>". $custom_field_value[$idx] ."</td>
							</tr>";
					}
			?>
			</tbody>
		</table>
		<?php endif; ?>
	</div>
</div>
<!-- Show Preview of Node Elemenents -->
<div class="wpsi-nodes-preview-sticky" id="wpsi-nodes-preview-sticky">
    <input type="hidden" id="element_input" value="<?php echo esc_attr(wpsi_helper::_d($session, 'node')); ?>" data-cnt="<?php echo esc_attr(trim(wpsi_helper::_d($session, 'node_count', 0))); ?>">
	<div class="wpsi-nodes-preview"></div>
</div>