<?php
/**
 * Perform request to elasticsearch
 *
 * @param   array  $args       request's body
 * @param   string  $endpoint  indexes endpoint
 * @param   string  $method    HTTP Request method i.e. GET | POST | PUT | DELETE
 *
 * @return  string|object      Query result
 */
function elasticsearch($args, $endpoint, $method = 'GET') {
  //Populate the correct endpoint for the API request
  $index = (isset($_ENV['INDEX']) && '' != $_ENV['INDEX'])? $_ENV['INDEX'] : 'sojuz';
  $url                = "http://elasticsearch:9200/{$index}{$endpoint}";

  //Populate the args for use in the wp_remote_request call

  $wp_args['method']  = $method;
  if (is_array($args)) {
    $args = json_encode($args, JSON_UNESCAPED_UNICODE);
    // var_dump($args);
    $wp_args['body'] = $args;
    $wp_args['headers'] = array(
      'Content-Type'=> 'application/json'
    );
  }
  $wp_args['timeout'] = 30;

  //Make the call and store the response in $res
  $res = wp_remote_request($url, $wp_args);

  //Check for success
  if(!is_wp_error($res) && ($res['response']['code'] == 200 || $res['response']['code'] == 201)) {
    return $res['body'];
  }
  else {
    return $res;
  }
}

/**
 * Returns term's ID
 *
 * @param   object  $term  WP_Term object
 *
 * @return  int         Term's ID
 */
function extract_term_id($term) {
  return $term->term_id;
}

/**
 * Posts public objects into elasticsearch index
 *
 * @return  void
 */
function sync_index() {
  $langs = array_keys(apply_filters( 'wpml_active_languages', [] ));
  if (!count($langs)) $langs[] = '';

  foreach($langs as $lang) {
    do_action( 'wpml_switch_language',  $lang );
    $args = array(
      'public'   => true,
      // '_builtin' => false
    );
    $post_types = get_post_types( $args );
    $post_types = apply_filters( 'index_extra_post_types', $post_types, $args );
    foreach ($post_types as $type) {
      $args = array(
        'posts_per_page' => -1,
        'post_type' => $type,
      );
      if ('attachment' == $type) {
        $args['post_status'] = 'inherit';
      }
      $all = new WP_Query($args);
      foreach ($all->posts as $post) {
        notify_index($post->ID, $post, true, $type);
      }
    }
  }
}

/**
 * Remove extra array from post's custom
 *
 * @param array $item Item value ref
 * @param string $key Item key
 */
function flatten_meta_fields( &$item, $key ) {
  if (is_object($item)) {
      $keys = array_keys((array) $item);
      $first = array_shift($keys);
      $item = $item->$first;
  } elseif (is_array($item)) {
      $keys = array_keys($item);
      $item = $item[array_shift($keys)];
  }
  // echo '<pre>';var_dump($item, $key);echo '</pre>';
}

/**
 * Checks if given key starts with _
 *
 * @param   string  $key  key name
 *
 * @return  bool        is meta public
 */
function filter_internal_fields( $key ) {
  // echo '<pre>';var_dump($key, !strpos($key, '_'));echo '</pre>';
  return $key[0] != '_';
}

/**
* Return parsed operation's custom fields
*
* @param int $post_id
* @return array
*/
function get_operation_meta( $post_id ) {
  $custom = $parsed = get_post_custom( $post_id );
  // $parsed = array_filter( $custom, 'filter_internal_fields', ARRAY_FILTER_USE_KEY);
  array_walk( $parsed, 'flatten_meta_fields' );
  return $parsed;
}

/**
 * Posts given post into elasticsearch index
 *
 * @param   int  $post_id  ID of the post
 * @param   object  $post     Post object
 * @param   bool  $update   Is post created, or updated
 *
 * @return  void
 */
function notify_index( $post_id, $post = NULL, $update = false ) {
  if ( wp_is_post_revision( $post_id ) )
  return;
  
  if (in_array($post->post_status, ['trash', 'draft', 'pending', 'private', 'auto-draft'], true)) {
    return;
  }

  if (is_null($post)) {
    $post = get_post($post_id);
  }

  $type = $post->post_type;
  $taxonomy = [];
  $tax = get_taxonomies([], 'object');
  foreach ($tax as $t) {
    foreach ($t->object_type as $type) {
      $taxonomy[$type][] = $t->name;
    }
  }
  // global $index;
  //  echo '<pre>';var_dump($taxonomy);echo '</pre>';


  $toIndex = apply_filters( 'index_extra_fields_names', [
    "ID",
    "post_title",
    "post_content",
    "post_excerpt",
    "post_status",
    "post_type",
    "post_name",
    "post_parent",
    "post_date",
    "menu_order",
    "likes",
    "post_meta",
    "categories",
    "thumbnail",
    "author",
    "blocks",
    "related",
    'suggest',
    'role',
    'protected',
    'caps'
  ], $type);

  // var_dump((array)$post);

  foreach ((array) $post as $field => $value) {
    if (($index = array_search($field, $toIndex)) !== false) {
      $data[$field] = $value;
      unset($toIndex[$index]);
    }
  }

  $custom = get_operation_meta($post_id);
  $lang = apply_filters( 'wpml_post_language_details', [], $post_id );
  $default = apply_filters( 'wpml_default_language', [] );
  $lang = $lang['language_code'];
  if ($default == $lang) $lang = '';
  foreach ($toIndex as $field) {
    switch ($field) {
      case"likes":
        $data[$field] = $custom['likes'];
        break;
      case"post_meta":
        if ('attachment' == $post->post_type) {
          $path = dirname('/wp-content/uploads/'.$custom['_wp_attached_file']);
          $custom['location'] = get_bloginfo('url').$path.'/';
          $custom['fullLocation'] = get_bloginfo('url').'/wp-content/uploads/'.$custom['_wp_attached_file'];
          // echo '<pre>';var_dump($custom, unserialize($custom['_wp_attachment_metadata']));echo '</pre>';
        }
        $data[$field] = $custom;
        break;
      case"categories":
        if (!is_array($data[$field])) $data[$field] = [];
        foreach ($taxonomy[$type] as $taxType) {
          $terms = get_the_terms($post_id, $taxType);
          if ($terms) foreach ($terms as $term) {
            $data[$field][] = [
              'term_id' => $term->term_id,
              'name' => $term->name,
              'slug' => $term->slug,
              'term_group' => $term->term_group,
              'term_taxonomy_id' => $term->term_taxonomy_id,
              'taxonomy' => $term->taxonomy,
              'description' => $term->description,
              'parent' => $term->parent,
            ];
          //   $tm = get_term_meta($term->term_id, 'thumbnail_id', true);
          //   if ($tm != '') {
          //     $args = array(
          //       'post_type' => 'attachment',
          //       'include' => $tm
          //   );
          //   $thumbs = get_posts( $args );
          //   $thumb = null;
          //   if ( $thumbs ) {
          //       // now create the new array
          //       $thumb['title'] = $thumbs[0]->post_title;
          //       $thumb['description'] = $thumbs[0]->post_content;
          //       $thumb['caption'] = $thumbs[0]->post_excerpt;
          //       $thumb['alt'] = get_post_meta( $tm, '_wp_attachment_image_alt', true );
          //       $att = wp_get_attachment_image_src( $tm, 'full', false );
          //       $thumb['sizes'][] = [
          //         'size' => 'full',
          //         'file' => $att[0],
          //         'url' => $att[0],
          //         'width' => $att[1],
          //         'height' => $att[2],
          //       ];
          //       // add the additional image sizes
          //       foreach ( get_intermediate_image_sizes() as $size ) {
          //         $att = wp_get_attachment_image_src( $tm, $size, false );
          //           $thumb['sizes'][] = [
          //             'size' => $size,
          //             'file' => $att[0],
          //             'url' => $att[0],
          //             'width' => $att[1],
          //             'height' => $att[2],
          //           ];
          //       }
          //   } // end if
          // }
          // $term->thumbnail = $thumb;
            // $data[$field][]= (array) $term;
          }
        }
        break;
      case"thumbnail":
        if ( has_post_thumbnail($post_id) ) {
          $thumb = array();
          $thumb_id = get_post_thumbnail_id($post_id);
          // first grab all of the info on the image... title/description/alt/etc.
          $args = array(
              'post_type' => 'attachment',
              'include' => $thumb_id
          );
          $thumbs = get_posts( $args );
          if ( $thumbs ) {
              $thumbAtts = get_post_meta($thumb_id, '_wp_attachment_metadata', true);
              echo '<pre>';var_dump($thumbAtts);
              $colors = array_keys($thumbAtts['colors']);
              $colors = array_map(function ($e) { return '#'.$e;}, $colors);
              // now create the new array
              $thumb['id'] = intval($thumb_id);
              $thumb['title'] = $thumbs[0]->post_title;
              $thumb['description'] = $thumbs[0]->post_content;
              $thumb['caption'] = $thumbs[0]->post_excerpt;
              $thumb['colors'] = [array_shift($colors)];
              $thumb['alt'] = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
              $att = wp_get_attachment_image_src( $thumb_id, 'full', false );
              // $thumb['sizes'][] = [
              //   'size' => 'full',
              //   'file' => $att[0],
              //   'url' => $att[0],
              //   'width' => $att[1],
              //   'height' => $att[2],
              // ];
              // add the additional image sizes
              // foreach ( get_intermediate_image_sizes() as $size ) {
              //   $att = wp_get_attachment_image_src( $thumb_id, $size, false );
              //     $thumb['sizes'][] = [
              //       'size' => $size,
              //       'file' => $att[0],
              //       'url' => $att[0],
              //       'width' => $att[1],
              //       'height' => $att[2],
              //     ];
              // }
          } // end if
          // echo '<pre>';var_dump($thumb);echo '</pre>';
          $data[$field] = $thumb;
      } // end if
        break;
      case"author":
        $user = new WP_User($post->post_author);
        $user = (array)$user->data;
        unset ($user['user_email'], $user['user_pass'], $user['user_activation_key'], $user['user_login']);
        $data[$field] = $user;
        break;
      case"blocks":
        $blocks = parse_blocks($post->post_content);
        $blocks = array_filter($blocks, function($e) { return (!is_null($e['blockName']));});
        $data[$field] = json_encode($blocks);
        break;
      case"related":
        $data[$field] = $custom['_cross_sell'];
        break;
      case 'suggest':
        $data[$field] = [$post->post_title];
        break;
      case 'role':
        if (!function_exists('get_post_statest')) require_once(ABSPATH.'/wp-admin/includes/template.php');
        $data[$field] = array_keys(get_post_states($post));
        break;
      case 'protected':
        $data[$field] = (strlen($post->post_password)) ? true : false;
        break;
      case 'caps':
        $caps = (strlen($post->post_password)) ? explode('|', $post->post_password) : [];
        $caps = array_map('intval', array_filter($caps, 'is_numeric'));
        $data[$field] = $caps;
        break;
      default:
        $extra = apply_filters( 'index_extra_fields_parser', false, $field, $data, $post);
        if ($extra) {
          $data[$field] = $extra;
        }
    }
  }

  $response = elasticsearch($data, $lang.'/_doc/'.$post_id, 'PUT');
}
add_action( 'save_post', 'notify_index', 10, 3 );
add_action( 'edit_attachment', 'notify_index', 10, 1);
add_action( 'add_attachment', 'notify_index', 10, 1);

function remove_from_index($id) {
  elasticsearch(false, '/_doc/'.$id.'/?pretty', 'DELETE');
}
add_action('wp_trash_post', 'remove_from_index');

// function do_elasticsearch($args = array()) {
//   $defaults = array(
//     'query' => (isset($_GET['s'])) ? $_GET['s'] : '',
//     'category' => (isset($_GET['c'])) ? $_GET['c'] : '',
//     'tag' => (isset($_GET['t'])) ? $_GET['t'] : '',
//   );

//   $args = wp_parse_args($args, $defaults);

//   $replacements = array(
//     'poir' => 'Program Operacyjny Inteligentny Rozwój',
//     'popw' => 'Program Operacyjny Polska Wschodnia',
//     'power' => 'Program Operacyjny Wiedza Edukacja Rozwój'
//   );
//   $args['query'] = strtolower($args['query']);
//   $mQuery = explode(' ', str_replace(array_keys($replacements), array_values($replacements), $args['query']));
//   $mQuery = array_filter($mQuery, function($e) { return (strlen($e) >= 3); });
//   $min_score = intval(get_option('min_score', '0.7')*100);
//   $sa = array(
//     'query' => array(
//       'bool' => array(
//         'must' => array(
//           'multi_match' => array(
//             'query' => strtolower(implode(' ', $mQuery)),
//             'fields' => array(
//               'question^5',
//               'answer^2',
//               'linked'
//             ),
//             // 'type' => 'phrase_prefix',
//             // 'fuzziness' => 2,
//             'analyzer' => 'morfologik',
//             'minimum_should_match' => $min_score.'%',
//           ),
//         ),
//       ),
//     ),
//     'highlight' => array(
//       'fields' => array(
//         'answer' => array('type' => 'plain'),
//         'question' => array('type' => 'plain'),
//       ),
//       'fragment_size' => 800,
//     ),
//   );

//   if ($args['tag'] != '') {
//     $tagIDs = explode(',', $args['tag']);
//     $tagQuery = array();
//     foreach ($tagIDs as $id) {
//       $tagQuery[] = array(
//         'term' => array(
//           'tag' => intval($id),
//         ),
//       );
//     }
//     $sa['query']['bool']['filter'] = $tagQuery;
//   }

//   if ($args['category'] != '') {
//     $catIDs = explode(',', $args['category']);
//     $catIDs = array_map(function ($e) {
//       if ((int)$e) return (int)$e;
//       $t = get_term_by('slug', $e, 'category');
//       return $t->term_id;
//     }, $catIDs);
//     // var_dump($catIDs);
//     if (count($catIDs) > 1) {
//       $type =  'terms';
//     } else {
//       $type = 'term';
//       $catIDs = $catIDs[0];
//     }
//     $sa['query']['bool']['filter'][] = array(
//       $type => array(
//         'category' => $catIDs,
//       ),
//     );
//   }

//   if ('' == $args['query']) {
//     if ('' == $args['tag'] && '' != $args['category']) {
//       header("location:".get_category_link($args['category']));
//       return;
//     }
//     $sa['query']['bool']['must'] = $sa['query']['bool']['filter'];
//     unset($sa['query']['bool']['filter']);
//   }

//   // if (isset($t)) {
//   //   $sa['query']['bool']['must'] = $t;
//   // }

//   $results = json_decode(elasticsearch($sa, 'qandaparp/_search/', "POST"), true);
//   // var_dump('<pre>', $sa, $results);
//   return $results;
// }

// function do_elasticsuggest($args = array()) {
//   $defaults = array(
//     'query' => (isset($_GET['s'])) ? $_GET['s'] : '',
//     'category' => (isset($_GET['c'])) ? $_GET['c'] : '',
//     'tag' => (isset($_GET['t'])) ? $_GET['t'] : '',
//   );

//   $args = wp_parse_args($args, $defaults);
//     $replacements = array(
//     'poir' => 'Program Operacyjny Inteligentny Rozwój',
//     'popw' => 'Program Operacyjny Polska Wschodnia',
//     'power' => 'Program Operacyjny Wiedza Edukacja Rozwój'
//   );
//   $args['query'] = strtolower($args['query']);
//   $mQuery = explode(' ', str_replace(array_keys($replacements), array_values($replacements), $args['query']));
//   $mQuery = array_filter($mQuery, function($e) { return (strlen($e) >= 3); });
//   $min_score = intval(get_option('min_score', '0.7')*100);
//   $sa = array(
//     'query' => array(
//       'bool' => array(
//         'must' => array(
//           'multi_match' => array(
//             'query' => strtolower(implode(' ', $mQuery)),
//             // 'query' => $args['query'],
//             'fields' => array(
//               'question^3',
//               'linked'
//             ),
//             // 'type' => 'phrase_prefix',
//             // 'fuzziness' => 2,
//             'analyzer' => 'morfologik',
//             'minimum_should_match' => $min_score.'%',
//           ),
//         ),
//       ),
//     ),
//   );

//   if ($args['tag'] != '') {
//     $tagIDs = explode(',', $args['tag']);
//     $tagQuery = array();
//     foreach ($tagIDs as $id) {
//       $tagQuery[] = array(
//         'term' => array(
//           'tag' => intval($id),
//         ),
//       );
//     }
//     $sa['query']['bool']['filter'] = $tagQuery;
//   }

//   if ($args['category'] != '') {
//     $catIDs = explode(',', $args['category']);
//     $catIDs = array_map(function ($e) {
//       if ((int)$e) return (int)$e;
//       $t = get_term_by('slug', $e, 'category');
//       return $t->term_id;
//     }, $catIDs);
//     // var_dump($catIDs);
//     if (count($catIDs) > 1) {
//       $type =  'terms';
//     } else {
//       $type = 'term';
//       $catIDs = $catIDs[0];
//     }
//     $sa['query']['bool']['filter'][] = array(
//       $type => array(
//         'category' => $catIDs,
//       ),
//     );
//   }

//   $results = json_decode(elasticsearch($sa, 'qandaparp/_search/', "POST"), true);
//   // var_dump($sa, $results);
//   return $results;
// }

// function my_modify_main_query( $query ) {
//   if ($query->is_search && !is_admin()) {
//     $results = do_elasticsearch();
//     // var_dump($results);
//     $ids = array();
//     $hits = array();
//     $log = false;
//     $squery = $query->query_vars['s'];
//     unset($query->query_vars['s']);

//     if ($results['hits']['total']) {
//       foreach ($results['hits']['hits'] as $hit) {
//         // $score = $results['hits']['max_score'] / $hit['_score'];
//         // var_dump($score, $results['hits']['max_score'], $hit['_score']);
//         // if ($score >= get_option('min_score', 0.7)) {
//           $hits[$hit['_id']] = $hit;
//           $ids[] = $hit['_source']['ID']; // Legacy
//         // }
//       }
//       if (count($ids)) {
//         $query->query_vars['post__in'] = $ids;
//         $query->hits= $hits;
//         // var_dump($hits);
//       } else {
//         $query->query_vars['cat'] = 999999; // Non existent category ID
//         $log = true;
//       }
//     } else {
//       $query->query_vars['year'] = 1993;
//       $log = true;
//     }

//     if ($log) {
//         $offsets = array();
//         $normal = array();
//         $aa = array(
//             "analyzer"=> "morfologik",
//             "text"=> $squery,
//         );
//         $results = json_decode(elasticsearch($aa, '_analyze/', "POST"), true);

//         if (!is_array($results['tokens'])) {
//           return;
//         }
//         foreach ($results['tokens'] as $res) {
//             if (!in_array($res['start_offset'], $offsets)) {
//                 array_push($offsets, $res['start_offset']);
//                 $normal[] = $res['token'];
//             }
//         }
//         $normal = implode(' ', $normal);
//         require_once ABSPATH . '/wp-admin/includes/post.php';
//         if (! ($ppost = post_exists($normal))) {
//             $log_args = array(
//                 'post_type' => 'logs',
//                 'post_title' => $normal,
//                 'post_status' => 'publish',
//             );
//             $npost = wp_insert_post($log_args);
//             update_post_meta($npost, 'hits', 1);
//         } else {
//             $hits = (int)get_post_meta($ppost, 'hits', true);
//             update_post_meta($ppost, 'hits', ++$hits);
//         }
//     }
//     // var_dump('<pre>',$query, $ids, $hits);
//   } else if ($query->is_category && !is_admin() && isset($_GET['jt']))
//   {
//     $tags = explode(',', $_GET['jt']);
//     $query->query_vars['tag__in'] = $tags;
//     $query->query_vars['paged'] = 0;
//   }
// }
// Hook my above function to the pre_get_posts action
// add_action( 'pre_get_posts', 'my_modify_main_query' );
