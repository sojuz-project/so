<?php 
$post_type = $pvalue['post_type'];
$taxonomies = get_object_taxonomies($post_type, 'objects');
$taxonomies_names = array();
$taxonomies_hierarchical = array();
if (!empty($taxonomies)) {
	foreach ($taxonomies as $key => $value) {
		$tax_name = $value->name;
		$term_ids = array();
		$is_hierarchical = $value->hierarchical;
		$delemiter = !empty($pvalue['term'][$tax_name.'_delemiter']) ? $pvalue['term'][$tax_name.'_delemiter'] : ',';
		if(array_key_exists($tax_name, $pvalue['taxo'])) {
			$hierarchy_group = $pvalue['term'][$tax_name.'_grp'];
			if(isset($pvalue['term'][$tax_name.'_hierarchical']) && !empty($hierarchy_group)) {
				foreach ($hierarchy_group as $term_grp) {
					if(!empty($term_grp)){
						$terms = explode('>', $term_grp);
						reset($terms);
						$first_key = key($terms);
						foreach ($terms as $k => $term) {
							if ($k == $first_key) {
								$has_parent = false;
							} else {
								$has_parent = true;
								$parent = $terms[$k-1];
							}
							if ($has_parent) {
								if (strlen($term<=28)){
									$parent_tdata = get_term_by('name', $parent, $tax_name);
									$term_exists = term_exists($term, $tax_name);
									if ($term_exists !== 0 && $term_exists !== null) {
										$tdata = get_term_by(
											'name',
											$term,
											$tax_name
										);
										wp_update_term(
											(int)$tdata->term_id,
											$tax_name,
											array('parent' => (int)$parent_tdata->term_id)
										);
										$term_ids[] = (int)$tdata->term_id;
									} else {
										$tdata = wp_insert_term( 
											$term,
											$tax_name,
											array('parent' => (int)$parent_tdata->term_id)
										);
										$term_ids[] = (int)$tdata['term_id'];
									}
								}
							} else {
								if (strlen($term<=28)){
									$term_exists = term_exists( $term, $tax_name );
									if ($term_exists !== 0 && $term_exists !== null) {

										$tdata = get_term_by('name', $term, $tax_name);
										$term_ids[] = (int)$tdata->term_id;

									} else {
										
										$tdata = wp_insert_term($term, $tax_name);
										$term_ids[] = (int) $tdata['term_id']; 
									}
								}
							}
						}
					}
				}
			}
			$term_list = $pvalue['term'][$tax_name.'_list']; 
			if (!empty($term_list)){
				$tlist = explode($delemiter, $term_list);
				foreach ($tlist as $term) {
					$term_exists = term_exists($term, $tax_name);
					if(strlen($term<=28)){
						if ($term_exists !== 0 && $term_exists !== null) {
							$tdata = get_term_by('name', $term, $tax_name );
							$term_ids[] = (int)$tdata->term_id;
						} else {
							$tdata = wp_insert_term($term, $tax_name);
							$term_ids[] = (int)$tdata['term_id'];
						}
					}
				}
			}
			if (!empty($term_ids)){
				if($is_hierarchical) {
					wp_set_object_terms($post_id,$term_ids,$tax_name, true);
				} else {
					$term_array = explode($delemiter, $term_list);
					wp_set_post_terms($post_id, $term_array, $tax_name, true);
				}
			}
		}
	}
}