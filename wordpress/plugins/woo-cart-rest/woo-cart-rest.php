<?php
/**
 * Plugin Name:     Woo Cart Rest
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          YOUR NAME HERE
 * Author URI:      YOUR SITE HERE
 * Text Domain:     woo-cart-rest
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Woo_Cart_Rest
 */

// Your code starts here.

add_action( 'rest_api_init', 'add_cart_api');

function add_cart_api(){
  register_rest_route( 'sojuz/v1', '/cart/', array(
      'methods' => array("GET", 'POST', 'PATCH', 'PUT', 'DELETE'),
      'callback' => 'add_to_cart_by_key',
  ));
  register_rest_route( 'sojuz/v1', '/cart/coupon', array(
    'methods' => array("GET", 'POST', 'DELETE'),
    'callback' => 'apply_cart_coupon',
  ));
}

function prepare_cart_session() {
  if ( null === WC()->session ) {
		$session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );

		//Prefix session class with global namespace if not already namespaced
		if ( false === strpos( $session_class, '' ) ) {
			$session_class = '' . $session_class;
		}

		WC()->session = new $session_class();
    WC()->session->init();
    WC()->customer = new WC_Customer();
  }
  include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
  include_once WC_ABSPATH . 'includes/wc-notice-functions.php';

  if ( null === WC()->cart ) {
		WC()->cart = new WC_Cart();

		// We need to force a refresh of the cart contents from session here (cart contents are normally refreshed on wp_loaded, which has already happened by this point).
		WC()->cart->get_cart();
	}
}

add_action('init', 'set_cart_cookie');
function set_cart_cookie() {
  $entityBody = json_decode(file_get_contents('php://input'), ARRAY_A);
  if (is_null($entityBody)) $entityBody = [];
  $entityBody = array_merge($entityBody, $_REQUEST);
  if (isset($entityBody['key'])) {
    $cookies = explode('|||', $entityBody['key']);
    foreach ($cookies as $cookie) {
      $parsed = explode('=', $cookie);
      $_COOKIE[$parsed[0]] = str_replace('%7C%7C', '||', $parsed[1]);
    }
  }
}

function add_to_cart_by_key($request) {
  $parameters = $request->get_params();

  $productId = $parameters['product_id'];
  $itemKey = $parameters['item_key'];
  $quantity = $parameters['quantity'];

  prepare_cart_session();

  switch ($request->get_method()) {
    case 'POST':
      WC()->cart->add_to_cart($productId, $quantity);
      break;
    case 'PUT':
      $cart = WC()->cart->cart_contents;
      foreach( $cart as $cart_item_id=>$cart_item ) {
        if ($cart_item_id === $itemKey) {
          $cart_item['quantity'] = $quantity;
          WC()->cart->cart_contents[$cart_item_id] = $cart_item;
        }
      }
      break;
    case 'PATCH':
      WC()->cart->remove_cart_item( $itemKey );
      break;
    case 'DELETE':
      WC()->cart->empty_cart();
      WC()->session->destroy_session();
      break;
  }
  WC()->cart->set_session();
  WC()->session->save_data();

  return new WP_REST_Response(WC()->session->get_session_data());
}

function apply_cart_coupon($request) {
  prepare_cart_session();
  $parameters = $request->get_params();

  $coupon = $parameters['coupon'];

  switch ($request->get_method()) {
    case 'POST':
      WC()->cart->apply_coupon($coupon);
      break;
  }
  WC()->cart->set_session();
  WC()->session->save_data();
  return new WP_REST_Response(['applied_coupons' => WC()->session->get_session_data()['applied_coupons']]);
}