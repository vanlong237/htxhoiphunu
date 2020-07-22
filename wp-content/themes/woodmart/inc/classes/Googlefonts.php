<?php
/**
 * Manage google fonts.
 *
 * @package xts
 */

namespace XTS;

use XTS\Singleton;

/**
 * Static class to manage google fonts.
 *
 * @since 1.0.0
 */
class Google_Fonts extends Singleton {

	/**
	 * All google fonts array.
	 *
	 * @var array
	 */
	public static $all_google_fonts = array();

	/**
	 * Google fonts array that will be displayed on frontend.
	 *
	 * @var array
	 */
	private static $_google_fonts = array();

	/**
	 * Register hooks and load base data.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		self::$all_google_fonts = require dirname( __FILE__ ) . '/../options/controls/typography/google-fonts.php';
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_fonts' ), 30000 );
	}

	/**
	 * Add google font.
	 *
	 * @since 1.0.0
	 *
	 * @param array $font     Font name.
	 * @param bool  $variants Font variants.
	 */
	public static function add_google_font( $font, $variants = false ) {
		$defaults = array(
			'font-family' => '',
			'font-weight' => '',
			'font-style'  => '',
			'font-subset' => '',
		);

		if ( ! isset( $font['font-family'] ) || ! isset( self::$all_google_fonts[ $font['font-family'] ] ) ) {
			return;
		}

		$font = wp_parse_args( $font, $defaults );

		$font_to_add['font-family'] = $font['font-family'];
		$font_to_add['font-weight'] = $font['font-weight'];
		$font_to_add['font-style']  = $font['font-style'];
		$font_to_add['font-subset'] = $font['font-subset'];

		if ( isset( self::$all_google_fonts[ $font['font-family'] ]['variants'] ) ) {
			$font_to_add['variants'] = self::$all_google_fonts[ $font['font-family'] ]['variants'];
		}
		
		foreach ( self::$_google_fonts as $font ) {
			if ( array_search( $font_to_add['font-family'], $font ) ) {
				return;
			}
		}
		
		self::$_google_fonts[] = $font_to_add;
	}

	/**
	 * Get fonts from Google based on all fonts selected in the settings.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_fonts() {

		$link    = '';
		$subsets = array();
		
		if ( ! self::$_google_fonts ) {
			return;
		}

		foreach ( self::$_google_fonts as $family => $font ) {
			if ( ! empty( $link ) ) {
				$link .= '%7C'; // Append a new font to the string.
			}

			$link .= $font['font-family'];

			if ( ! empty( $font['variants'] ) ) {
				$link  .= ':';
				$i     = 0;
				$count = count( $font['variants'] );
				foreach ( $font['variants'] as $key => $variant ) {
					$i ++;
					$link .= $variant['id'];
					if ( $i < $count ) {
						$link .= ',';
					}
				}
			}

			if ( isset( $font['font-subset'] ) && ! empty( $font['font-subset'] ) && ! in_array( $font['font-subset'], $subsets, true ) ) {
				array_push( $subsets, $font['font-subset'] );
			}
		}

		if ( ! empty( $subsets ) ) {
			$link .= '&subset=' . implode( ',', $subsets );
		}

		if ( 'disable' !== woodmart_get_opt( 'google_font_display' ) ) {
			$link .= '&display=' . woodmart_get_opt( 'google_font_display' );
		}

		wp_enqueue_style( 'xts-google-fonts', '//fonts.googleapis.com/css?family=' . str_replace( '|', '%7C', $link ), array(), woodmart_get_theme_info( 'Version' ) );
	}

}

Google_Fonts::get_instance();
