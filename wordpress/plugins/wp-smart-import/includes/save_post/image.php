<?php 
$images1 = array();
$images2 = array();
$upload_dir = wp_upload_dir();
if (!empty($pvalue['media_imgs'])) { // LOCAL Media Library 
	$files = explode(',', $pvalue['media_imgs']);
	$mode = 'media';
	$result1 = $wpSmartImportCommon->sort_and_get_images($files, $mode);
	$images1 = $result1['srcs'];
}
if (!empty($pvalue['download_imgs'])) { 
	$images2 = array_map('trim', explode(',', $pvalue['download_imgs']));
	$images2 = array_values( array_unique($images2));
}
if ($pvalue['set_featured_image'] == 'yes') { // Set Featured Image
	if ($pvalue['img_from'] == 'media-file' && !empty($images1)) {
		$arr = array( "\\" => "/" );
		$image = strtr($images1[0] ,$arr);
		$attachment_id = attachment_url_to_postid($image);
		if ($attachment_id)
			set_post_thumbnail($post_id, $attachment_id);

	} elseif ($pvalue['img_from'] == 'download' && !empty($images2)) {
		$image = $images2[0];
		if(!empty($image) && wp_remote_retrieve_response_code(wp_remote_get($image)) == 200)
		{
			$attachment_id = wpSmartImportCommon::generate_featured_image($image, $post_id);
			if ($attachment_id){
				set_post_thumbnail($post_id, $attachment_id);
			}
		}
	}
}
$images = array();
$images = array_merge($images1, $dinp);
$images = array_filter($images);
if (!empty($images)) { // put image data in meta post meta
	foreach ($images as $image) {
		$arr = array( "\\" => "/" );
		$image = strtr($image ,$arr);
		add_post_meta($post_id, 'wpsi-images', $image);
	}
}