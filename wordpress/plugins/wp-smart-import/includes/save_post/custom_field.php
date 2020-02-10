<?php	
if (!empty($pvalue['custom_field_name'])) {
	$custom_field_name = $pvalue['custom_field_name'];
	$custom_fied_value = $pvalue['custom_field_value'];
	foreach ($custom_field_name as $key => $field_name) {
		$array = array('id' => $post_id, 'key' => $field_name, 'value' => $custom_fied_value[$key]);
		add_post_meta($post_id, sanitize_text_field($field_name), $custom_fied_value[$key]);
	}
}