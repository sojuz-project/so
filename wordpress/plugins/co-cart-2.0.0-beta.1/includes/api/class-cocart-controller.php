<?php
/**
 * CoCart REST API controller
 *
 * Handles requests to the cart endpoint.
 *
 * @author   Sébastien Dumont
 * @category API
 * @package  CoCart/API
 * @since    2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CoCart REST API controller class.
 *
 * @package CoCart REST API/API
 */
class CoCart_API_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'cocart/v1';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = '';

	/**
	 * Register the routes for cart.
	 *
	 * @access public
	 */
	public function register_routes() {
		// Get Cart - cocart/v1/get-cart (GET)
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/get-cart', array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_cart' ),
			'args'     => array(
				'thumb' => array(
					'default' => false,
				),
			),
		));
 
		// Get Cart of a Customer - cocart/v1/get-cart/1 (GET)
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/get-cart/(?P<id>[\d]+)', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_cart_customer' ),
			'permission_callback' => array( $this, 'get_permission_check' ),
			'args'                => array(
				'id' => array(
					'required'    => true,
					'description' => __( 'Unique identifier for the customer.', 'cart-rest-api-for-woocommerce' ),
					'type'        => 'integer',
				),
				'thumb' => array(
					'default' => false,
				),
			),
		));

		// Add Item - cocart/v1/add-item (POST)
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/add-item', array(
			'methods'  => WP_REST_Server::CREATABLE,
			'callback' => array( $this, 'add_to_cart' ),
			'args'     => array(
				'product_id' => array(
					'validate_callback' => function( $param, $request, $key ) {
						return is_numeric( $param );
					}
				),
				'quantity' => array(
					'validate_callback' => function( $param, $request, $key ) {
						return is_numeric( $param );
					}
				),
				'variation_id' => array(
					'validate_callback' => function( $param, $request, $key ) {
						return is_numeric( $param );
					}
				),
				'variation' => array(
					'validate_callback' => function( $param, $request, $key ) {
						return is_array( $param );
					}
				),
				'cart_item_data' => array(
					'validate_callback' => function( $param, $request, $key ) {
						return is_array( $param );
					}
				)
			)
		) );

		// Calculate Cart Total - cocart/v1/calculate (POST)
		register_rest_route( $this->namespace, '/' . $this->rest_base  . '/calculate', array(
			'methods'  => WP_REST_Server::CREATABLE,
			'callback' => array( $this, 'calculate_totals' ),
			'args'     => array(
				'return' => array(
					'validate_callback' => function( $param, $request, $key ) {
						return is_bool( $param );
					}
				)
			)
		));

		// Clear Cart - cocart/v1/clear (POST)
		register_rest_route( $this->namespace, '/' . $this->rest_base  . '/clear', array(
			'methods'  => WP_REST_Server::CREATABLE,
			'callback' => array( $this, 'clear_cart' ),
		));

		// Count Items in Cart - cocart/v1/count-items (GET)
		register_rest_route( $this->namespace, '/' . $this->rest_base  . '/count-items', array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_cart_contents_count' ),
			'args'     => array(
				'return' => array(
					'default' => 'numeric'
				),
			),
		));

		// Update, Remove or Restore Item - cocart/v1/item (GET, POST, DELETE)
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/item', array(
			'args' => array(
				'cart_item_key' => array(
					'description' => __( 'The cart item key is what identifies the item in the cart.', 'cart-rest-api-for-woocommerce' ),
					'type'        => 'string',
				),
				'return_cart' => array(
					'description'       => __( 'Returns the whole cart if requested.', 'cart-rest-api-for-woocommerce' ),
					'default'           => false,
					'validate_callback' => function( $param, $request, $key ) {
						return is_bool( $param );
					}
				)
			),
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'restore_item' ),
			),
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'update_item' ),
				'args'     => array(
					'quantity' => array(
						'default'           => 1,
						'validate_callback' => function( $param, $request, $key ) {
							return is_numeric( $param );
						}
					),
				),
			),
			array(
				'methods'  => WP_REST_Server::DELETABLE,
				'callback' => array( $this, 'remove_item' ),
			),
		) );

		// Get Cart Totals - cocart/v1/totals (GET)
		register_rest_route( $this->namespace, '/' . $this->rest_base  . '/totals', array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_totals' ),
			'args'     => array(
				'html' => array(
					'default' => false,
				),
			),
		));
	} // register_routes()

	/**
	 * Check if a given request can read the cart.
	 *
	 * @access public
	 * @since  2.0.0
	 * @return bool|WP_ERROR
	 */
	public function get_permission_check() {
		if ( ! current_user_can( 'administrator' ) ) {
			return new WP_Error( 'cocart_cannot_read_cart', __( 'Cannot read cart!', 'cart-rest-api-for-woocommerce' ), array( 'status' => 500 ) );
		}

		return true;
	} // END get_permission_check()

	/**
	 * Get cart.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @version 2.0.0
	 * @param   array  $data
	 * @param   string $item_key
	 * @return  WP_REST_Response
	 */
	public function get_cart( $data = array(), $item_key = '0' ) {
		$cart_contents = $this->get_cart_contents( $data, $item_key );

		do_action( 'cocart_get_cart', $cart_contents );

		return new WP_REST_Response( $cart_contents, 200 );
	} // END get_cart()

	/**
	 * Get cart for a specific customer.
	 *
	 * @access public
	 * @since  2.0.0
	 * @param  array $data
	 * @param  string $item_key
	 * @return array|WP_ERROR
	 */
	public function get_cart_customer( $data = array(), $item_key = '0' ) {
		if ( empty( $data['id'] ) ) {
			return new WP_Error( 'cocart_customer_missing', __( 'Customer ID is required!', 'cart-rest-api-for-woocommerce' ), array( 'status' => 500 ) );
		}

		$saved_cart = $this->get_saved_cart( $data );

		// If a saved cart exists then replace the carts content.
		if ( ! empty( $saved_cart ) ) {
			return $this->return_cart_contents( $saved_cart, $data, $item_key );
		}

		return $this->get_cart_contents( $data, $item_key );
	} // END get_cart_customer()

	/**
	 * Gets the cart contents.
	 *
	 * @access public
	 * @since  2.0.0
	 * @param  array  $data
	 * @param  string $item_key
	 * @return array  $cart_contents
	 */
	public function get_cart_contents( $data = array(), $item_key = '0' ) {
		$cart_contents = isset( WC()->cart ) ? WC()->cart->get_cart() : WC()->session->cart;

		return $this->return_cart_contents( $cart_contents, $data, $item_key );
	} // END get_cart_contents()

	/**
	 * Return cart contents.
	 *
	 * @access public
	 * @since  2.0.0
	 * @param  array  $cart_contents
	 * @param  array  $data
	 * @param  string $item_key
	 * @return array  $cart_contents
	 */
	public function return_cart_contents( $cart_contents, $data = array(), $item_key = '0' ) {
		if ( $this->get_cart_contents_count( array( 'return' => 'numeric' ) ) <= 0 || empty( $cart_contents ) ) {
			return array();
		}

		$show_thumb = ! empty( $data['thumb'] ) ? $data['thumb'] : false;

		// Used to check if the item exists in the cart with `find_product_in_cart()` function.
		if ( ! empty( $item_key ) && $item_key != '0' ) {
			$does_item_exist = $cart_contents[$item_key];

			// If the data does not return empty then the item is in cart.
			if ( ! empty( $does_item_exist ) ) {
				return true;
			}
			else {
				return false;
			}
		}

		foreach ( $cart_contents as $item_key => $cart_item ) {
			// If product data is missing then get product data and apply.
			if ( ! isset( $cart_item['data'] ) ) {
				$cart_item['data'] = wc_get_product( $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'] );
				$cart_contents[$item_key]['data'] = $cart_item['data'];
			}

			$_product = apply_filters( 'cocart_item_product', $cart_item['data'], $cart_item, $item_key );

			// Adds the product name and title as new variables.
			$cart_contents[$item_key]['product_name']  = $_product->get_name();
			$cart_contents[$item_key]['product_title'] = $_product->get_title();

			// If main product thumbnail is requested then add it to each item in cart.
			if ( $show_thumb ) {
				$thumbnail_id = apply_filters( 'cocart_item_thumbnail', $_product->get_image_id(), $cart_item, $item_key );

				$thumbnail_src = wp_get_attachment_image_src( $thumbnail_id, apply_filters( 'cocart_item_thumbnail_size', 'woocommerce_thumbnail' ) );

				// Add main product image as a new variable.
				$cart_contents[$item_key]['product_image'] = esc_url( $thumbnail_src[0] );
			}

			// This filter allows additional data to be returned for a specific item in cart.
			$cart_contents = apply_filters( 'cocart_cart_contents', $cart_contents, $item_key, $cart_item, $_product );
		}

		// The cart contents is returned and can be filtered.
		return apply_filters( 'cocart_return_cart_contents', $cart_contents );
	} // END return_cart_contents()

	/**
	 * Get cart contents count.
	 *
	 * @access public
	 * @since  1.0.0
	 * @param  array $data
	 * @return string|WP_REST_Response
	 */
	public function get_cart_contents_count( $data = array() ) {
		$count = WC()->cart->get_cart_contents_count();

		$return = ! empty( $data['return'] ) ? $data['return'] : '';

		if ( $return != 'numeric' && $count <= 0 ) {
			return new WP_REST_Response( __( 'There are no items in the cart!', 'cart-rest-api-for-woocommerce' ), 200 );
		}

		return $count;
	} // END get_cart_contents_count()

	/**
	 * Returns a customers saved cart from the database if one exists.
	 *
	 * @access public
	 * @since  2.0.0
	 * @param  array $data       The customer ID is a required variable.
	 * @return array $saved_cart Returns the cart content from the database.
	 */
	public function get_saved_cart( $data = array() ) {
		$saved_cart = array();

		$customer_id = ! empty( $data['customer_id'] ) ? $data['customer_id'] : 0;

		$saved_cart_meta = get_user_meta( $customer_id, '_woocommerce_persistent_cart_' . get_current_blog_id(), true );

		if ( isset( $saved_cart_meta['cart'] ) ) {
			$saved_cart = array_filter( (array) $saved_cart_meta['cart'] );
		}

		return $saved_cart;
	} // END get_saved_cart()

	/**
	 * Clear cart.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @version 2.0.0
	 * @return  WP_ERROR|WP_REST_Response
	 */
	public function clear_cart() {
		WC()->cart->empty_cart();
		WC()->session->set('cart', array()); // Empty the session cart data

		if ( WC()->cart->is_empty() ) {
			do_action( 'cocart_cart_cleared' );

			return new WP_REST_Response( __( 'Cart is cleared.', 'cart-rest-api-for-woocommerce' ), 200 );
		} else {
			return new WP_Error( 'cocart_clear_cart_failed', __( 'Clearing the cart failed!', 'cart-rest-api-for-woocommerce' ), array( 'status' => 500 ) );
		}
	} // END clear_cart()

	/**
	 * Validate the product id argument.
	 *
	 * @access protected
	 * @since  1.0.0
	 * @param  int $product_id
	 * @return WP_Error
	 */
	protected function validate_product_id( $product_id ) {
		if ( $product_id <= 0 ) {
			return new WP_Error( 'cocart_product_id_required', __( 'Product ID number is required!', 'cart-rest-api-for-woocommerce' ), array( 'status' => 500 ) );
		}

		if ( ! is_numeric( $product_id ) ) {
			return new WP_Error( 'cocart_product_id_not_numeric', __( 'Product ID must be numeric!', 'cart-rest-api-for-woocommerce' ), array( 'status' => 500 ) );
		}
	} // END validate_product_id()

	/**
	 * Validate the product quantity argument.
	 *
	 * @access protected
	 * @since  1.0.0
	 * @param  int $quantity
	 * @return WP_Error
	 */
	protected function validate_quantity( $quantity ) {
		if ( ! is_numeric( $quantity ) ) {
			return new WP_Error( 'cocart_quantity_not_numeric', __( 'Quantity must be numeric!', 'cart-rest-api-for-woocommerce' ), array( 'status' => 500 ) );
		}
	} // END validate_quantity()

	/**
	 * Validate product before it is added to the cart, updated or removed.
	 *
	 * @access protected
	 * @since  1.0.0
	 * @param  int $product_id
	 * @param  int $quantity
	 * @return WP_Error
	 */
	protected function validate_product( $product_id = null, $quantity = 1 ) {
		$this->validate_product_id( $product_id );

		$this->validate_quantity( $quantity );
	} // END validate_product()

	/**
	 * Check if product is in the cart and return cart item key.
	 *
	 * Cart item key will be unique based on the item and its properties, such as variations.
	 *
	 * @access public
	 * @since  2.0.0
	 * @param  mixed  $cart_id id of product to find in the cart.
	 * @return string cart item key
	 */
	public function find_product_in_cart( $cart_id = false ) {
		if ( false !== $cart_id ) {
			if ( is_array( $this->get_cart() ) && null !== $this->get_cart( array(), $cart_id ) ) {
				return $cart_id;
			}
		}

		return '';
	} // END find_product_in_cart()

	/**
	 * Checks if the product in the cart has enough stock 
	 * before updating the quantity.
	 * 
	 * @access protected
	 * @since  1.0.6
	 * @param  array  $current_data
	 * @param  string $quantity
	 * @return bool|WP_Error
	 */
	protected function has_enough_stock( $current_data = array(), $quantity = 1 ) {
		$product_id      = ! isset( $current_data['product_id'] ) ? 0 : absint( $current_data['product_id'] );
		$variation_id    = ! isset( $current_data['variation_id'] ) ? 0 : absint( $current_data['variation_id'] );
		$current_product = wc_get_product( $variation_id ? $variation_id : $product_id );

		$quantity = absint( $quantity );

		if ( ! $current_product->has_enough_stock( $quantity ) ) {
			return new WP_Error( 'cocart_not_enough_in_stock', sprintf( __( 'You cannot add that amount of &quot;%1$s&quot; to the cart because there is not enough stock (%2$s remaining).', 'cart-rest-api-for-woocommerce' ), $current_product->get_name(), wc_format_stock_quantity_for_display( $current_product->get_stock_quantity(), $current_product ) ), array( 'status' => 500 ) );
		}

		return true;
	} // END has_enough_stock()

	/**
	 * Add to Cart.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @version 2.0.0
	 * @param   array $data
	 * @return  WP_Error|WP_REST_Response
	 */
	public function add_to_cart( $data = array() ) {
		$product_id     = ! isset( $data['product_id'] ) ? 0 : absint( $data['product_id'] );
		$quantity       = ! isset( $data['quantity'] ) ? 1 : absint( $data['quantity'] );
		$variation_id   = ! isset( $data['variation_id'] ) ? 0 : absint( $data['variation_id'] );
		$variation      = ! isset( $data['variation'] ) ? array() : $data['variation'];
		$cart_item_data = ! isset( $data['cart_item_data'] ) ? array() : $data['cart_item_data'];

		$this->validate_product( $product_id, $quantity );

		$product_data = wc_get_product( $variation_id ? $variation_id : $product_id );

		if ( $quantity <= 0 || ! $product_data || 'trash' === $product_data->get_status() ) {
			return new WP_Error( 'cocart_product_does_not_exist', __( 'Warning: This product does not exist!', 'cart-rest-api-for-woocommerce' ), array( 'status' => 500 ) );
		}

		// Find the cart item key in the existing cart.
		$cart_item_key = $this->find_product_in_cart( $cart_id );

		// Force quantity to 1 if sold individually and check for existing item in cart.
		if ( $product_data->is_sold_individually() ) {
			$quantity = 1;

			$cart_contents = $this->get_cart();

			$found_in_cart = apply_filters( 'cocart_add_to_cart_sold_individually_found_in_cart', $cart_item_key && $cart_contents[ $cart_item_key ]['quantity'] > 0, $product_id, $variation_id, $cart_item_data, $cart_id );

			if ( $found_in_cart ) {
				/* translators: %s: product name */
				return new WP_Error( 'cocart_product_sold_individually', sprintf( __( 'You cannot add another "%s" to your cart.', 'cart-rest-api-for-woocommerce' ), $product_data->get_name() ), array( 'status' => 500 ) );
			}
		}

		// Product is purchasable check.
		if ( ! $product_data->is_purchasable() ) {
			return new WP_Error( 'cocart_cannot_be_purchased', __( 'Sorry, this product cannot be purchased.', 'cart-rest-api-for-woocommerce' ), array( 'status' => 500 ) );
		}

		// Stock check - only check if we're managing stock and backorders are not allowed.
		if ( ! $product_data->is_in_stock() ) {
			return new WP_Error( 'cocart_product_out_of_stock', sprintf( __( 'You cannot add &quot;%s&quot; to the cart because the product is out of stock.', 'cart-rest-api-for-woocommerce' ), $product_data->get_name() ), array( 'status' => 500 ) );
		}

		if ( ! $product_data->has_enough_stock( $quantity ) ) {
			/* translators: 1: product name 2: quantity in stock */
			return new WP_Error( 'cocart_not_enough_in_stock', sprintf( __( 'You cannot add that amount of &quot;%1$s&quot; to the cart because there is not enough stock (%2$s remaining).', 'cart-rest-api-for-woocommerce' ), $product_data->get_name(), wc_format_stock_quantity_for_display( $product_data->get_stock_quantity(), $product_data ) ), array( 'status' => 500 ) );
		}

		// Stock check - this time accounting for whats already in-cart.
		if ( $product_data->managing_stock() ) {
			$products_qty_in_cart = WC()->cart->get_cart_item_quantities();

			if ( isset( $products_qty_in_cart[ $product_data->get_stock_managed_by_id() ] ) && ! $product_data->has_enough_stock( $products_qty_in_cart[ $product_data->get_stock_managed_by_id() ] + $quantity ) ) {
				return new WP_Error(
					'cocart_not_enough_stock_remaining',
					sprintf(
						__( 'You cannot add that amount to the cart &mdash; we have %1$s in stock and you already have %2$s in your cart.', 'cart-rest-api-for-woocommerce' ),
						wc_format_stock_quantity_for_display( $product_data->get_stock_quantity(), $product_data ),
						wc_format_stock_quantity_for_display( $products_qty_in_cart[ $product_data->get_stock_managed_by_id() ], $product_data )
					),
					array( 'status' => 500 )
				);
			}
		}

		$response  = apply_filters( 'cocart_ok_to_add_response', '', $product_data );
		$ok_to_add = apply_filters( 'cocart_ok_to_add', true, $product_data );

		// If it is not OK to add the item, return an error response.
		if ( ! $ok_to_add ) {
			$error_msg = empty( $response ) ? __( 'This item can not be added to the cart.', 'cart-rest-api-for-woocommerce' ) : $response;

			return new WP_Error(
				'cocart_not_ok_to_add_item', 
				$error_msg, 
				array( 'status' => 500 )
			);
 		}

		// Add item to cart.
		$item_key = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation, $cart_item_data );

		// Return response to added item to cart or return error.
		if ( $item_key ) {
			$item_added = WC()->cart->get_cart_item( $item_key );

			do_action( 'cocart_item_added_to_cart', $item_key, $item_added );

			// Was it requested to return the whole cart once item added?
			if ( isset( $data['return_cart'] ) ) {
				$cart_contents = $this->get_cart_contents( $data );

				return new WP_REST_Response( $cart_contents, 200 );
			}

			if ( is_array( $item_added ) ) {
				return new WP_REST_Response( $item_added, 200 );
			}
		} else {
			return new WP_Error( 'cocart_cannot_add_to_cart', sprintf( __( 'You cannot add "%s" to your cart.', 'cart-rest-api-for-woocommerce' ), $product_data->get_name() ), array( 'status' => 500 ) );
		}
	} // END add_to_cart()

	/**
	 * Remove Item in Cart.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @version 2.0.0
	 * @param   array $data
	 * @return  WP_Error|WP_REST_Response
	 */
	public function remove_item( $data = array() ) {
		$cart_item_key = ! isset( $data['cart_item_key'] ) ? '0' : wc_clean( $data['cart_item_key'] );

		if ( $cart_item_key != '0' ) {
			$current_data = WC()->cart->get_cart_item( $cart_item_key ); // Fetches the cart item data before it is removed.

			if ( WC()->cart->remove_cart_item( $cart_item_key ) ) {
				do_action( 'cocart_item_removed', $current_data );

				// Was it requested to return the whole cart once item removed?
				if ( isset( $data['return_cart'] ) ) {
					$cart_contents = $this->get_cart_contents( $data );

					return new WP_REST_Response( $cart_contents, 200 );
				}

				return new WP_REST_Response( __( 'Item has been removed from cart.', 'cart-rest-api-for-woocommerce' ), 200 );
			} else {
				return new WP_ERROR( 'cocart_can_not_remove_item', __( 'Unable to remove item from cart.', 'cart-rest-api-for-woocommerce' ), array( 'status' => 500 ) );
			}
		} else {
			return new WP_ERROR( 'cocart_cart_item_key_required', __( 'Cart item key is required!', 'cart-rest-api-for-woocommerce' ), array( 'status' => 500 ) );
		}
	} // END remove_item()

	/**
	 * Restore Item in Cart.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @version 2.0.0
	 * @param   array $data
	 * @return  WP_Error|WP_REST_Response
	 */
	public function restore_item( $data = array() ) {
		$cart_item_key = ! isset( $data['cart_item_key'] ) ? '0' : wc_clean( $data['cart_item_key'] );

		if ( $cart_item_key != '0' ) {
			if ( WC()->cart->restore_cart_item( $cart_item_key ) ) {
				$current_data = WC()->cart->get_cart_item( $cart_item_key ); // Fetches the cart item data once it is restored.

				do_action( 'cocart_item_restored', $current_data );

				// Was it requested to return the whole cart once item restored?
				if ( isset( $data['return_cart'] ) ) {
					$cart_contents = $this->get_cart_contents( $data );

					return new WP_REST_Response( $cart_contents, 200 );
				}

				return new WP_REST_Response( __( 'Item has been restored to the cart.', 'cart-rest-api-for-woocommerce' ), 200 );
			} else {
				return new WP_ERROR( 'cocart_can_not_restore_item', __( 'Unable to restore item to the cart.', 'cart-rest-api-for-woocommerce' ), array( 'status' => 500 ) );
			}
		} else {
			return new WP_ERROR( 'cocart_cart_item_key_required', __( 'Cart item key is required!', 'cart-rest-api-for-woocommerce' ), array( 'status' => 500 ) );
		}
	} // END restore_item()

	/**
	 * Update Item in Cart.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @version 2.0.0
	 * @param   array $data
	 * @return  WP_Error|WP_REST_Response
	 */
	public function update_item( $data = array() ) {
		$cart_item_key = ! isset( $data['cart_item_key'] ) ? '0' : wc_clean( $data['cart_item_key'] );
		$quantity      = ! isset( $data['quantity'] ) ? 1 : absint( $data['quantity'] );

		// Allows removing of items if quantity is zero should for example the item was with a product bundle.
		if ( $quantity === 0 ) {
			return $this->remove_item( $data );
		}

		$this->validate_quantity( $quantity );

		if ( $cart_item_key != '0' ) {
			$current_data = WC()->cart->get_cart_item( $cart_item_key ); // Fetches the cart item data before it is updated.

			$this->has_enough_stock( $current_data, $quantity ); // Checks if the item has enough stock before updating.

			if ( WC()->cart->set_quantity( $cart_item_key, $quantity ) ) {

				$new_data = WC()->cart->get_cart_item( $cart_item_key );

				$product_id   = ! isset( $new_data['product_id'] ) ? 0 : absint( $new_data['product_id'] );
				$variation_id = ! isset( $new_data['variation_id'] ) ? 0 : absint( $new_data['variation_id'] );

				$product_data = wc_get_product( $variation_id ? $variation_id : $product_id );

				if ( $quantity != $new_data['quantity'] ) {
					do_action( 'cocart_item_quantity_changed', $cart_item_key, $new_data );
				}

				// Was it requested to return the whole cart once item updated?
				if ( isset( $data['return_cart'] ) ) {
					$cart_contents = $this->get_cart_contents( $data );

					return new WP_REST_Response( $cart_contents, 200 );
				}

				// Return response based on product quantity increment.
				if ( $quantity > $current_data['quantity'] ) {
					return new WP_REST_Response( sprintf( __( 'The quantity for "%1$s" has increased to "%2$s".', 'cart-rest-api-for-woocommerce' ), $product_data->get_name(), $new_data['quantity'] ), 200 );
				} else if ( $quantity < $current_data['quantity'] ) {
					return new WP_REST_Response( sprintf( __( 'The quantity for "%1$s" has decreased to "%2$s".', 'cart-rest-api-for-woocommerce' ), $product_data->get_name(), $new_data['quantity'] ), 200 );
				} else {
					return new WP_REST_Response( sprintf( __( 'The quantity for "%s" has not changed.', 'cart-rest-api-for-woocommerce' ), $product_data->get_name() ), 200 );
				}
			} else {
				return new WP_ERROR( 'cocart_can_not_update_item', __( 'Unable to update item quantity in cart.', 'cart-rest-api-for-woocommerce' ), array( 'status' => 500 ) );
			}
		} else {
			return new WP_ERROR( 'cocart_cart_item_key_required', __( 'Cart item key is required!', 'cart-rest-api-for-woocommerce' ), array( 'status' => 500 ) );
		}
	} // END update_item()

	/**
	 * Calculate Cart Totals.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @version 2.0.0
	 * @param   array $data
	 * @return  WP_REST_Response
	 */
	public function calculate_totals( $data = array() ) {
		if ( $this->get_cart_contents_count( array( 'return' => 'numeric' ) ) <= 0 ) {
			return new WP_REST_Response( __( 'No items in cart to calculate totals.', 'cart-rest-api-for-woocommerce' ), 200 );
		}

		WC()->cart->calculate_totals();

		if ( isset( $data['return'] ) ) {
			return $this->get_totals( $data );
		}

		return new WP_REST_Response( __( 'Cart totals have been calculated.', 'cart-rest-api-for-woocommerce' ), 200 );
	} // END calculate_totals()

	/**
	 * Returns all calculated totals.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @version 2.0.0
	 * @param   array $data
	 * @return  WP_REST_Response
	 */
	public function get_totals( $data = array() ) {
		$totals = WC()->session->get( 'cart_totals' );

		$pre_formatted = ! empty( $data['html'] ) ? $data['html'] : false;

		if ( $pre_formatted ) {
			$new_totals = array();

			$ignore_convert = array(
				'shipping_taxes',
				'cart_contents_taxes',
				'fee_taxes'
			);

			foreach( $totals as $type => $sum ) {
				if ( in_array( $type, $ignore_convert ) ) {
					$new_totals[$type] = $sum;
				} else {
					if ( is_string( $sum ) ) {
						$new_totals[$type] =  html_entity_decode( strip_tags( wc_price( $sum ) ) );
					}
					else {
						$new_totals[$type] =  html_entity_decode( strip_tags( wc_price( strval( $sum ) ) ) );
					}
				}
			}

			return new WP_REST_Response( $new_totals, 200 );
		}

		return new WP_REST_Response( $totals, 200 );
	} // END get_totals()

} // END class
