<?php

add_action( 'plugins_loaded', function () {
    remove_filter( 'rest_api_init', 'create_initial_rest_routes' );
});

function change_rest_prefix() {
    return "api";
}
add_filter( 'rest_url_prefix', 'change_rest_prefix');

function register_routes() {
	remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
	register_rest_route( 'v1', '/find', array(
        'methods' => 'GET',
        'callback' => 'do_search',
    ));

    register_rest_route( 'v1', '/suggest', array(
        'methods' => 'GET',
        'callback' => 'do_suggestion',
    ));
}
add_action( 'rest_api_init', 'register_routes');

function do_search($request) {
    global $wpdb;
	$args = $request->get_params();
	// $defaults = array(
 //    'query' => (isset($_GET['s'])) ? $_GET['s'] : '',
 //    'category' => (isset($_GET['c'])) ? $_GET['c'] : '',
 //    'tag' => (isset($_GET['t'])) ? $_GET['t'] : '',
 //  );
	$results = do_elasticsearch($args);
	$answer = '';
    $log = false;
    if ($results['hits']['total']) {
        $hit = $results['hits']['hits'][0];
	    $score = $results['hits']['max_score'] / $hit['_score'];
        if ($score >= get_option('min_score', 0.7)) {
          $hits = (int)get_post_meta($hit['_source']['ID'], 'hits', true);
          update_post_meta($hit['_source']['ID'], 'hits', ++$hits);
          $res = $wpdb->insert($wpdb->prefix.'hits', array(
            'date' => date('Y-m-d H:i:s'),
            'object_id' => $hit['_source']['ID']
            )
          );
          // var_dump($res);
          $cats = get_the_category($hit['_source']['ID']);
          $answer = $hit['_source']['answer'];
          foreach ($cats as $c) {
              if ($c->parent) $answer .= '<p class="linkback"><a href="'.get_category_link($c->term_id).'">'.$c->cat_name.'</a></p>';
          }
        } else {
            $log = true;
        }
    } else {
        $log = true;
    }

    if ($log) {
        $offsets = array();
        $normal = array();
        $aa = array(
            "analyzer"=> "morfologik",
            "text"=> $args['query'],
        );
        $results = json_decode(elasticsearch($aa, '_analyze/', "POST"), true);

        foreach ($results['tokens'] as $res) {
            if (!in_array($res['start_offset'], $offsets)) {
                array_push($offsets, $res['start_offset']);
                $normal[] = $res['token'];
            }
        }
        $normal = implode(' ', $normal);
        require_once ABSPATH . '/wp-admin/includes/post.php';
        if (! ($ppost = post_exists($normal))) {
            $log_args = array(
                'post_type' => 'logs',
                'post_title' => $normal,
                'post_status' => 'publish',
            );
            $npost = wp_insert_post($log_args);
            update_post_meta($npost, 'hits', 1);
        } else {
            $hits = (int)get_post_meta($ppost, 'hits', true);
            update_post_meta($ppost, 'hits', ++$hits);
        }
    }
    return $answer;
}

function do_suggestion($request) {
	$args = $request->get_params();
	$results = do_elasticsuggest($args);
	$suggestions = array();
    if ($results['hits']['total']) {
      foreach ($results['hits']['hits'] as $hit) {
        $suggestions[] = $hit['_source']['question']    ;
      }
    }
    return $suggestions;
}
