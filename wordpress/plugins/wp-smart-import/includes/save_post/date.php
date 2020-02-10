<?php
if (array_key_exists('date_type', $pvalue)) {
	if ($pvalue['date_type'] != 'auto') {
		if ($pvalue['date_type'] == 'specific' && !empty($pvalue['post_date'])) {
			$post_dates =  date('Y-m-d H:i:s', strtotime( trim( $pvalue['post_date'])));
		} elseif (!empty($pvalue['post_date_start']) && !empty($pvalue['post_date_end'])) {
			$startDate = $pvalue['post_date_start'];
			$endDate = $pvalue['post_date_end'];
			$tmp_startDate = strtotime($startDate);
				$tmp_endDate = strtotime($endDate);
			if (($tmp_endDate - $tmp_startDate) > 0) {
				$post_dates =  $wpSmartImportCommon->get_random_dateTime($startDate, $endDate);
			}
		}
		if (!empty($post_dates)) {
			$post = array(
			      'ID' => $post_id,
			      'post_date' => $post_dates,
			      'post_date_gmt' => get_gmt_from_date($post_dates),
			      'post_modified' => $post_dates,
			      'post_modified_gmt' => get_gmt_from_date($post_dates),
			  	);
			wp_update_post($post);
		}
		$tmp_date[] = $post_dates;
	}
} 