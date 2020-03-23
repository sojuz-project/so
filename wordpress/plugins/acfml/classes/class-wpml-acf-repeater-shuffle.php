<?php
/**
 * Handle the case when the user changes translated repeater state (reorder fields, add new field in the middle...).
 *
 * @package acfml
 */
class WPML_ACF_Repeater_Shuffle {
	/**
	 * @var array Meta data before the shuffle.
	 */
	private $meta_data_before_move;
	/**
	 * @var array Meta data after the shuffle.
	 */
	private $meta_data_after_move;
	/**
	 * @var array Final metadata values in another languages to save
	 */
	private $meta_to_update;

	/**
	 * Registers hooks used while repeater field's values are being updated.
	 */
	public function register_hooks() {
		add_action( 'acf/save_post', array( $this, 'store_state_before' ), 5, 1 );
		add_action( 'acf/save_post', array( $this, 'update_translated_repeaters' ), 15, 1 );
	}

	/**
	 * Load all existing translations for this post and all existing metadata for this post.
	 *
	 * @param int $post_id ID of the post being saved.
	 */
	public function store_state_before( $post_id = 0 ) {
		if ( $post_id > 0 && ! $this->meta_data_before_move ) {
			$this->meta_data_before_move = $this->get_cleaned_meta_data( $post_id );
		}
	}

	/**
	 * @param int $post_id ID of the post being saved.
	 */
	public function update_translated_repeaters( $post_id = 0 ) {
		if ( $this->should_translation_update_run( $post_id ) ) {
			$this->meta_data_after_move = $this->get_cleaned_meta_data( $post_id );
			foreach ( $this->meta_data_after_move as $key => $value ) {
				$key_change = $this->are_keys_for_meta_value_changed( $key, $value );
				if ( $key_change ) {
					$translations = $this->get_post_translations( $post_id );
					if ( $translations ) {
						$this->remove_deprecated_meta( $translations, $key_change['was'], $key_change['is'] );
					}
				}
			}

			$this->readd_meta();

		}
	}

	/**
	 * Returns always meta data array with single values and always related to ACF fields.
	 *
	 * When get_post_meta is used without key value, it always works like the third parameter is false so it always
	 * returns array of array values. This function flattens it look like get_post_meta has been called with true as
	 * third parameter.
	 *
	 * Additionally removes from array metadata which are not set for ACF fields
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return array Flatten array.
	 */
	private function get_cleaned_meta_data( $post_id ) {
		$meta_data = get_post_meta( $post_id );
		if ( is_array( $meta_data ) ) {
			foreach ( $meta_data as $key => $maybe_array ) {
				$acf_field = get_field_object( $key, $post_id );
				if ( ! $acf_field ) {
					unset( $meta_data[ $key ] );
				} elseif ( is_array( $maybe_array ) ) {
					$meta_data[ $key ] = end( $maybe_array );
				}
			}
		}
		return $meta_data;
	}

	/**
	 * Get all translations of post with given ID.
	 *
	 * @param int $post_id The post ID.
	 *
	 * @return bool|array
	 */
	private function get_post_translations( $post_id ) {
		static $element_translations = array();
		static $last_post_id         = false;

		if ( $post_id !== $last_post_id || ! $element_translations ) {
			$type = apply_filters( 'wpml_element_type', get_post_type( $post_id ) );
			$trid = apply_filters( 'wpml_element_trid', 0, $post_id, $type );
			if ( $trid > 0 ) {
				$element_translations = apply_filters( 'wpml_get_element_translations', false, $trid, $type );
				if ( $element_translations ) {
					foreach ( $element_translations as $language_code => $post ) {
						if ( (int) $post->element_id === $post_id ) {
							unset( $element_translations[ $language_code ] );
						}
					}
				}
			}
		}

		$last_post_id = $post_id;

		return $element_translations;
	}

	/**
	 * Checks if post ID is given and if the state for comparision is already saved.
	 *
	 * @param int $post_id
	 *
	 * @return bool
	 */
	private function should_translation_update_run( $post_id = 0 ) {
		return $post_id > 0 && $this->meta_data_before_move;
	}

	/**
	 * Checks if meta key has changed for given meta value.
	 *
	 * @param string $meta_key_after_move   The new meta key.
	 * @param mixed  $meta_value_after_move Meta value.
	 *
	 * @return array|bool
	 */
	private function are_keys_for_meta_value_changed( $meta_key_after_move, $meta_value_after_move ) {
		$changed = array();
		// For given meta value after move, find related keys in data before move.
		$keys_before_move = $this->keys_before_move( $meta_value_after_move );
		if ( $keys_before_move ) {
			// Now find keys for the same data but after move.
			$keys_after_move = $this->keys_after_move( $meta_value_after_move );
			// Check if keys are different.
			$key_was = array_diff( $keys_before_move, $keys_after_move );
			$key_is  = array_diff( $keys_after_move, $keys_before_move );
			$found   = array_search( $meta_key_after_move, $key_is, true );
			if ( false !== $found ) {
				$key_was = array_values( $key_was );
				$key_is  = array_values( $key_is );
				if ( $key_was && $key_is ) {
					$changed = [
						'was' => array_shift( $key_was ),
						'is'  => array_shift( $key_is ),
					];
				}
			}
		}
		return $changed;
	}

	/**
	 * Returns new meta keys for given meta value.
	 *
	 * @param mixed $meta_value_after_move
	 *
	 * @return array
	 */
	private function keys_after_move( $meta_value_after_move ) {
		return array_keys( $this->meta_data_after_move, $meta_value_after_move, true );
	}

	/**
	 * Returns original meta keys for given meta value.
	 *
	 * @param mixed $meta_value_after_move
	 *
	 * @return array
	 */
	private function keys_before_move( $meta_value_after_move ) {
		return array_keys( $this->meta_data_before_move, $meta_value_after_move, true );
	}

	/**
	 * Re-add metas again from saved pairs.
	 */
	private function readd_meta() {
		if ( $this->meta_to_update ) {
			foreach ( $this->meta_to_update as $translated_post_id => $meta_pairs ) {
				foreach ( $meta_pairs as $pair ) {
					update_post_meta( $translated_post_id, $pair[0], $pair[1] );
				}
			}
		}
	}

	/**
	 * Foreach existing translation remove it but keep its value in pair with new key.
	 *
	 * @param array  $translations Post translations to update.
	 * @param string $key_of_original_value Original meta key.
	 * @param string $meta_key_after_move New meta key.
	 */
	private function remove_deprecated_meta( array $translations, $key_of_original_value, $meta_key_after_move ) {
		foreach ( $translations as $language_code => $translated_post_data ) {
			$translated_meta_value =
				get_post_meta( $translated_post_data->element_id, $key_of_original_value, true );
			if ( $translated_meta_value ) {
				delete_post_meta( $translated_post_data->element_id, $key_of_original_value );
				$this->meta_to_update[ $translated_post_data->element_id ][] = array(
					$meta_key_after_move,
					$translated_meta_value,
				);
			}
		}
	}

}
