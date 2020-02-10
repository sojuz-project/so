<?php 
$data = array();
$request = wpsi_helper::recursive_sanitize_text_field($_REQUEST);
if (!isset($request['_nonce']) || !wp_verify_nonce(@$request['_nonce'], 'wpsi_nonce')) {
	echo '<h1>Invalid HTTP Request</h1>';
	wp_die();
}
$wpsiQuery = new wpSmartImportQuery;
if (isset($request['id'])) {
	if (substr_count( $request['id'], ",") > 0) {
		$ids = array_filter(explode(',', $request['id']));
		foreach ($ids as $id) {
			$data[] = $wpsiQuery->wpsi_getRow('wpsi_files',$id);
		}
		$data = array_filter($data);
	} else {
		$ids = $request['id'];
		$data[] = $wpsiQuery->wpsi_getRow('wpsi_files', $request['id']);
	}
}
if (empty($data) || wpsi_helper::array_key_exists_r('error', $data))
	exit("<h1 class='text-center error-text'> Error Data Not Found ! </h1>");
?>
<h1><?php wpsi_helper::_e('Delete'); ?></h1>
 <!-- Progress Bar -->
<div class="meter animate_progress" style="display: none;" >
  <span class="text-center progress-text" style="width: 0%"><span></span></span>
</div>
<div id="Response-content" style="display: none;">
	<h1><?php wpsi_helper::_e('Deleted Record'); ?></h1>
	<table class="wpsi-table wpsi-table-border wpsi-table-hoverable list-table display-none" id="manage-file-record">
		<thead class="head-color-green">
			<tr>
				<th><?php wpsi_helper::_e('File Name'); ?></th>
				<th><?php wpsi_helper::_e('file'); ?></th>
				<th><?php wpsi_helper::_e('Import'); ?></th>
				<th><?php wpsi_helper::_e('Post'); ?></th>
			</tr>
		</thead>
		<tbody></tbody>
		<tfoot></tfoot>
	</table>
</div>
<!-- Progress Bar -->
<form accept="" method="post" id="wpsi-delete-file">
		<?php foreach ($data as $idx => $obj): ?>
			<div class="wpsi-portlet" >
			    <div class="wpsi-portlet-title">
			        <div class="caption">
			            <span class="caption-subject">
			            	<?php wpsi_helper::_e('File Data'); ?>
			            	<?php wpsi_helper::_e("Id : "); ?>
			            	<?php echo $obj->id ?> 
			            </span>
			        </div>
			    </div>
				<table class="wpsi-table wpsi-table-border wpsi-table-hoverable wpsi-custom-field-tab list-table">
				 	<tbody>
				 		<tr>
						    <td><?php wpsi_helper::_e('ID'); ?></td>
						    <td><?php echo $obj->id; ?></td>
				  		</tr>
				 		<tr>
						    <td><?php wpsi_helper::_e('File Name'); ?></td>
						    <td><?php echo $obj->name; ?> </td>
				  		</tr>
				  		<tr>
						    <td><?php wpsi_helper::_e('File Path'); ?></td>
						    <td><?php echo $obj->file_path; ?></td>
				  		</tr>
				  		<input type="hidden" name="manage_file[id][]" value="<?php echo esc_attr($obj->id); ?>">
						<input type="hidden" name="manage_file[file_name][]" value="<?php echo esc_attr($obj->name); ?>">
						<input type="hidden" name="manage_file[file_path][]" value="<?php echo esc_attr($obj->file_path); ?>">
					</tbody>
				</table>
			</div>
		<?php endforeach; ?>
	<div class="wpsi-portlet" >
	    <div class="wpsi-portlet-title">
	        <div class="caption">
	            <span class="caption-subject"><?php wpsi_helper::_e('Options'); ?></span>
	        </div>
	    </div>
		<div class="wpsi-portlet-body">
			<label>  
				<input type="radio" name="manage_file[delete]" checked="" value="record" class="mycxk">
				<?php wpsi_helper::_e('Delete File Record Only ( File will not show in existing file list )'); ?>
			</label>
			<label>  
				<input type="radio" name="manage_file[delete]" value="import" class="mycxk">
				<?php wpsi_helper::_e('Delete All Imports Created by File'); ?>
			</label>
			<label>  
				<input type="radio" name="manage_file[delete]" value="post" class="mycxk">
				<?php wpsi_helper::_e('Delete All Post Created by File'); ?>
			</label>
			<label>  
				<input type="radio" name="manage_file[delete]" value="full" class="mycxk">
				<?php wpsi_helper::_e('Delete All Imports and Post Created by File'); ?>				
			</label>
		</div>
	</div>
	<div class="wpsi-step-next">
		<div class="upload-button-group">
			<button class="wpsi-button target-disabled wpsi-button-big btn-grp btn-next" type="button" id="delete_files"><?php wpsi_helper::_e('Delete'); ?></button>
		</div>
	</div>
</form>