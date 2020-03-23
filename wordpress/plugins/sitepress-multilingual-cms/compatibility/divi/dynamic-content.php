<?php

namespace WPML\Compatibility\Divi;

use SitePress;

class DynamicContent implements \IWPML_DIC_Action, \IWPML_Backend_Action {

	const ENCODED_CONTENT_START = '@ET-DC@';
	const ENCODED_CONTENT_END   = '@';

	/** @var $positions */
	private $positions = [ 'before', 'after' ];

	/** @var SitePress */
	private $sitepress;

	/**
	 * @param SitePress $sitepress
	 */
	public function __construct( SitePress $sitepress ) {
		$this->sitepress = $sitepress;
	}

	/**
	 * Add filters and actions.
	 */
	public function add_hooks() {
		if ( $this->sitepress->is_setup_complete() ) {
			add_filter( 'wpml_pb_shortcode_decode', [ $this, 'decode_dynamic_content' ], 10, 2 );
			add_filter( 'wpml_pb_shortcode_encode', [ $this, 'encode_dynamic_content' ], 10, 2 );
		}
	}

	/**
	 * Sets 'before' and 'after' dynamic content settings to translatable.
	 *
	 * @param string $string   The decoded string so far.
	 * @param string $encoding The encoding used.
	 * @return array
	 */
	public function decode_dynamic_content( $string, $encoding ) {
		if ( $this->is_dynamic_content( $string ) ) {
			$field = $this->decode_field( $string );

			$string = [
				'et-dynamic-content' => [
					'value'     => $string,
					'translate' => false,
				],
			];

			foreach ( $this->positions as $position ) {
				if ( ! empty( $field['settings'][ $position ] ) ) {
					$string[ $position ] = [
						'value'     => $field['settings'][ $position ],
						'translate' => true,
					];
				}
			}
		}

		return $string;
	}

	/**
	 * Rebuilds dynamic content with translated strings.
	 *
	 * @param array  $string   The field array.
	 * @param string $encoding The encoding used.
	 * @return string
	 */
	public function encode_dynamic_content( $string, $encoding ) {
		if ( is_array( $string ) && isset( $string['et-dynamic-content'] ) ) {
			$field = $this->decode_field( $string['et-dynamic-content'] );

			foreach ( $this->positions as $position ) {
				if ( isset( $string[ $position ] ) ) {
					$field['settings'][ $position ] = $string[ $position ];
				}
			}

			$string = $this->encode_field( $field );
		}

		return $string;
	}

	/**
	 * Check if a certain field contains dynamic content.
	 *
	 * @param string $string The string to check.
	 */
	private function is_dynamic_content( $string ) {
		return substr( $string, 0, strlen( self::ENCODED_CONTENT_START ) ) === self::ENCODED_CONTENT_START;
	}

	/**
	 * Decode a dynamic-content field.
	 *
	 * @param string $string The string to decode.
	 * @return array
	 */
	private function decode_field( $string ) {
		$start = strlen( self::ENCODED_CONTENT_START );
		$end   = strlen( self::ENCODED_CONTENT_END );

		// phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		return json_decode( base64_decode( substr( $string, $start, -$end ) ), true );
	}

	/**
	 * Encodes a dynamic-content field.
	 *
	 * @param array $field The field to encode.
	 * @return string
	 */
	private function encode_field( $field ) {
		// phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		return self::ENCODED_CONTENT_START
			. base64_encode( wp_json_encode( $field ) )
			. self::ENCODED_CONTENT_END;
	}

}
