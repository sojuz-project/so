<?php

function _action() {
	echo 'Simple non privileged function example for functions that start with _ char. Action should start with __';
	die;
}

function action() {
	echo 'Simple non privileged function example for normal functions';
	die;
}
add_filter('sojuz_actions' , function($actions){
  /**
   * How to correctly register form action (display)
   *
   * $actions is a multidimmensional array which look like this:
   *
   * [
   *  'Built in' => [
	 *  	'login'=> 'Login',
	 *		'register' => 'Register',
   *    [...]
   *	],
	 *	'User defined' => [
     *		'action'			=> 'This action is only avaliable when user is logged in',
     *		'_action'			=> 'Actions containing _ as first character do not require user to be legged in',
     *		'__action'		=> 'If your custom action starts with _ you must double the underscore sign',
   *	],
   * ]
   *
   * Built in's are actions already handled by the frontend app
   * User defined ones register ajax actions according to their keys
   * In eitther case values are labels displayed on form creation screen
   * Each unprivileged action is also defined for privileged run
   */
  $actions['User defined']['__contact_form'] = 'Contact form';
  $actions['User defined']['create_reservation'] = 'Create reservation';
  $actions['User defined']['create_opinion'] = 'Create opinion';
  return $actions; 
});

function _contact_form() {
  $data = $_REQUEST;
  // TODO send email
  // wp_mail( $to, $subject, $message, $headers = '', $attachments = array() )

  // callback
  $data['messages'] = array('Email was send...');
  echo json_encode($data);
	die;
}

function create_opinion() {
  $data = $_REQUEST;
  
  // insert post
  $post_id = wp_insert_post(array (
   'post_type' => 'opinion',
   'post_title' => $data['opinion_title'],
   'post_content' => $data['opinion'],
   'post_status' => 'publish',
   'comment_status' => 'closed', 
   'ping_status' => 'closed',  
  ));

  // insert metadata
  add_post_meta( $post_id, 'post_name', $data['post_name']);

  // callback
  $data['messages'] = array('Opinion for '.$data['opinion_title'].' send');
  echo json_encode($data);
	die;
}

function create_reservation() {
  $data = $_REQUEST;
  
  // insert post
  $post_id = wp_insert_post(array (
   'post_type' => 'entry',
   'post_title' => $data['post_name'],
   'post_status' => 'publish',
   'comment_status' => 'closed', 
   'ping_status' => 'closed',  
   'post_password' => get_current_user_id(),
  ));

  // insert terms
  $cats = [];
  foreach (explode(",", $data['term']) as $value) {
    $cats[] = explode("|", $value)[1];
  }
  $data['catds'] = $cats;
  // wp_set_post_terms( $post_id, $cats, 'category', true );
  wp_set_object_terms($post_id, $cats, 'category');

  // insert metadata
  add_post_meta( $post_id, 'post_name', $data['post_name']);
  add_post_meta( $post_id, 'activity_from', $data['activity_from']);
  add_post_meta( $post_id, 'activity_to', $data['activity_to']);

  // update elastic
  
  notify_index( $post_id, null, true ); 

  // callback
  $data['messages'] = array('Reservation for '.$data['post_name'].' send');
  echo json_encode($data);
	die;
}

// https://send.firefox.com/download/69f98f0f9ce0740f/#JaxXwuLfnQindsd1j3v3xw