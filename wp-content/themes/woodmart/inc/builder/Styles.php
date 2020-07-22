<?php

use XTS\Singleton;

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

class WOODMART_HB_Styles {
	/**
	 * Header elements css.
	 *
	 * @var array
	 */
	private $elements_css;

	public function get_elements_css() {
		return $this->elements_css;
	}

	public function get_all_css( $el, $options ) {
		$this->set_elements_css( $el );

		return $this->get_header_css( $options ) . $this->get_elements_css();
	}

	/**
	 * Set header elements css.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $el Header structure.
	 */
	public function set_elements_css( $el = false ) {
		if ( ! $el ) {
			$el = woodmart_get_config( 'header-builder-structure' );
		}

		$selector = 'whb-' . $el['id'];

		if ( isset( $el['content'] ) && is_array( $el['content'] ) ) {
			foreach ( $el['content'] as $element ) {
				$this->set_elements_css( $element );
			}
		}

		$css        = '';
		$rules      = '';
		$border_css = '';

		if ( isset( $el['params']['background'] ) && ( 'categories' !== $el['type'] ) ) {
			$rules .= $this->generate_background_css( $el['params']['background']['value'] );
		}

		if ( isset( $el['params']['border'] ) && ( 'categories' !== $el['type'] ) ) {
			$sides      = isset( $el['params']['border']['value']['sides'] ) ? $el['params']['border']['value']['sides'] : array( 'bottom' );
			$border_css = $this->generate_border_css( $el['params']['border']['value'], $sides );
		}

		if ( isset( $el['params']['border'] ) && isset( $el['params']['border']['value']['applyFor'] ) && 'boxed' === $el['params']['border']['value']['applyFor'] ) {
			$css .= '.' . $selector . '-inner { ' . $border_css . ' }';
		} elseif ( $border_css ) {
			$rules .= $border_css;
		}

		if ( 'categories' === $el['type'] ) {
			if ( isset( $el['params']['background'] ) ) {
				$css .= '.' . $selector . ' .menu-opener { ' . $this->generate_background_css( $el['params']['background']['value'] ) . ' }';
			}

			if ( isset( $el['params']['border'] ) ) {
				$sides = isset( $el['params']['border']['value']['sides'] ) ? $el['params']['border']['value']['sides'] : array( 'bottom' );
				$css  .= '.' . $selector . ' .menu-opener { ' . $this->generate_border_css( $el['params']['border']['value'], $sides ) . ' }';
			}

			if ( isset( $el['params']['more_cat_button'] ) && $el['params']['more_cat_button'] ) {
				$count = $el['params']['more_cat_button_count']['value'] + 1;
				$css  .= '.' . $selector . '.wd-more-cat:not(.wd-show-cat) .item-level-0:nth-child(n+' . $count . '):not(:last-child) {
				    display: none;
				}.wd-more-cat .item-level-0:nth-child(n+' . $count . ') {
				    animation: wd-fadeIn .3s ease both;
				}
				.wd-show-cat .wd-more-cat-btn {
				    display: none;
				}';
			}
		}
		
		if ( $rules ) {
			$css .= "\n" . '.' . $selector . ' {' . "\n";
			$css .= "\t" . $rules . "\n";
			$css .= '}' . "\n";
		}

		$this->elements_css .= $css;
	}

	/**
	 * Generate background CSS code.
	 *
	 * @since 1.0.0
	 *
	 * @param array $bg Background data.
	 *
	 * @return string
	 */
	public function generate_background_css( $bg ) {
		$css = '';

		if ( isset( $bg['background-color'] ) ) {
			extract( $bg['background-color'] );
		}

		if ( isset( $r ) && isset( $g ) && isset( $b ) && isset( $a ) ) {
			$css .= 'background-color: rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $a . ');';
		}

		if ( isset( $bg['background-image'] ) ) {
			extract( $bg['background-image'] );
		}

		if ( isset( $url ) ) {
			$css .= 'background-image: url(' . $url . ');';
		}

		if ( isset( $bg['background-size'] ) ) {
			$css .= 'background-size: ' . $bg['background-size'] . ';';
		}

		if ( isset( $bg['background-attachment'] ) ) {
			$css .= 'background-attachment: ' . $bg['background-attachment'] . ';';
		}

		if ( isset( $bg['background-position'] ) ) {
			$css .= 'background-position: ' . $bg['background-position'] . ';';
		}

		if ( isset( $bg['background-repeat'] ) ) {
			$css .= 'background-repeat: ' . $bg['background-repeat'] . ';';
		}

		return $css;
	}

	/**
	 * Generate border CSS code.
	 *
	 * @since 1.0.0
	 *
	 * @param array $border Border data.
	 * @param array $sides Sides data.
	 *
	 * @return string
	 */
	public function generate_border_css( $border, $sides ) {
		$css = '';

		if ( is_array( $border ) ) {
			extract( $border );
		}
		if ( isset( $color ) ) {
			extract( $color );
		}

		if ( isset( $r ) && isset( $g ) && isset( $b ) && isset( $a ) ) {
			$css .= 'border-color: rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $a . ');';
		}

		foreach ( $sides as $side ) {
			if ( isset( $width ) ) {
				$css .= 'border-' . $side . '-width: ' . $width . 'px;';
			}

			$css .= ( isset( $style ) ) ? 'border-' . $side . '-style: ' . $style . ';' : 'border-' . $side . '-style: solid;';
		}

		return $css;
	}

	public function get_header_css( $options ) {
		$top_border    = ( isset( $options['top-bar']['border']['width'] ) ) ? (int) $options['top-bar']['border']['width'] : 0;
		$header_border = ( isset( $options['general-header']['border']['width'] ) ) ? (int) $options['general-header']['border']['width'] : 0;
		$bottom_border = ( isset( $options['header-bottom']['border']['width'] ) ) ? (int) $options['header-bottom']['border']['width'] : 0;

		$total_border_height = $top_border + $header_border + $bottom_border;

		$total_height = $options['top-bar']['height'] + $options['general-header']['height'] + $options['header-bottom']['height'];

		$mobile_height = $options['top-bar']['mobile_height'] + $options['general-header']['mobile_height'] + $options['header-bottom']['mobile_height'] + $total_border_height;

		$total_height += $total_border_height;

		if ( $options['boxed'] && ( $options['top-bar']['hide_desktop'] || ( ! $options['top-bar']['hide_desktop'] && $options['top-bar']['background'] ) ) ) {
			$total_height = $total_height + 30;
		}

		$sticky_elements = array_filter(
			$options,
			function( $el ) {
				return isset( $el['sticky'] ) && $el['sticky'];
			}
		);

		$total_sticky_height = 0;

		foreach ( $sticky_elements as $key => $el ) {
			if ( isset( $el['height'] ) && $el['height'] ) {
				$total_sticky_height += $el['height'];
			}
		}

		ob_start();
		?>
@media (min-width: 1025px) {
	.whb-top-bar-inner {
		height: <?php echo esc_html( $options['top-bar']['height'] ); ?>px;
	}
	
	.whb-general-header-inner {
		height: <?php echo esc_html( $options['general-header']['height'] ); ?>px;
	}
	
	.whb-header-bottom-inner {
		height: <?php echo esc_html( $options['header-bottom']['height'] ); ?>px;
	}
	
	.whb-sticked .whb-top-bar-inner {
		height: <?php echo esc_html( $options['top-bar']['sticky_height'] ); ?>px;
	}
	
	.whb-sticked .whb-general-header-inner {
		height: <?php echo esc_html( $options['general-header']['sticky_height'] ); ?>px;
	}
	
	.whb-sticked .whb-header-bottom-inner {
		height: <?php echo esc_html( $options['header-bottom']['sticky_height'] ); ?>px;
	}
	
	/* HEIGHT OF HEADER CLONE */
	.whb-clone .whb-general-header-inner {
		height: <?php echo esc_html( $options['sticky_height'] ); ?>px;
	}
	
	/* HEADER OVERCONTENT */
	.woodmart-header-overcontent .title-size-small {
		padding-top: <?php echo esc_html( $total_height + 20 ); ?>px;
	}
	
	.woodmart-header-overcontent .title-size-default {
		padding-top: <?php echo esc_html( $total_height + 60 ); ?>px;
	}
	
	.woodmart-header-overcontent .title-size-large {
		padding-top: <?php echo esc_html( $total_height + 100 ); ?>px;
	}
	
	/* HEADER OVERCONTENT WHEN SHOP PAGE TITLE TURN OFF  */
	.woodmart-header-overcontent .without-title.title-size-small {
		padding-top: <?php echo esc_html( $total_height ); ?>px;
	}
	
	.woodmart-header-overcontent .without-title.title-size-default {
		padding-top: <?php echo esc_html( $total_height + 35 ); ?>px;
	}
	
	.woodmart-header-overcontent .without-title.title-size-large {
		padding-top: <?php echo esc_html( $total_height + 60 ); ?>px;
	}
	
	/* HEADER OVERCONTENT ON SINGLE PRODUCT */
	.single-product .whb-overcontent:not(.whb-custom-header) {
		padding-top: <?php echo esc_html( $total_height ); ?>px;
	}
	
	/* HEIGHT OF LOGO IN TOP BAR */
	.whb-top-bar .woodmart-logo img {
		max-height: <?php echo esc_html( $options['top-bar']['height'] ); ?>px;
	}
	
	.whb-sticked .whb-top-bar .woodmart-logo img {
		max-height: <?php echo esc_html( $options['top-bar']['sticky_height'] ); ?>px;
	}
	
	/* HEIGHT OF LOGO IN GENERAL HEADER */
	.whb-general-header .woodmart-logo img {
		max-height: <?php echo esc_html( $options['general-header']['height'] ); ?>px;
	}
	
	.whb-sticked .whb-general-header .woodmart-logo img {
		max-height: <?php echo esc_html( $options['general-header']['sticky_height'] ); ?>px;
	}
	
	/* HEIGHT OF LOGO IN BOTTOM HEADER */
	.whb-header-bottom .woodmart-logo img {
		max-height: <?php echo esc_html( $options['header-bottom']['height'] ); ?>px;
	}
	
	.whb-sticked .whb-header-bottom .woodmart-logo img {
		max-height: <?php echo esc_html( $options['header-bottom']['sticky_height'] ); ?>px;
	}
	
	/* HEIGHT OF LOGO IN HEADER CLONE */
	.whb-clone .whb-general-header .woodmart-logo img {
		max-height: <?php echo esc_html( $options['sticky_height'] ); ?>px;
	}
	
	/* HEIGHT OF HEADER BUILDER ELEMENTS */
	/* HEIGHT ELEMENTS IN TOP BAR */
	.whb-top-bar .wd-tools-element > a,
	.whb-top-bar .main-nav .item-level-0 > a,
	.whb-top-bar .whb-secondary-menu .item-level-0 > a,
	.whb-top-bar .categories-menu-opener,
	.whb-top-bar .menu-opener,
	.whb-top-bar .whb-divider-stretch:before,
	.whb-top-bar form.woocommerce-currency-switcher-form .dd-selected,
	.whb-top-bar .whb-text-element .wcml-dropdown a.wcml-cs-item-toggle {
		height: <?php echo esc_html( $options['top-bar']['height'] ); ?>px;
	}
	
	.whb-sticked .whb-top-bar .wd-tools-element > a,
	.whb-sticked .whb-top-bar .main-nav .item-level-0 > a,
	.whb-sticked .whb-top-bar .whb-secondary-menu .item-level-0 > a,
	.whb-sticked .whb-top-bar .categories-menu-opener,
	.whb-sticked .whb-top-bar .menu-opener,
	.whb-sticked .whb-top-bar .whb-divider-stretch:before,
	.whb-sticked .whb-top-bar form.woocommerce-currency-switcher-form .dd-selected,
	.whb-sticked .whb-top-bar .whb-text-element .wcml-dropdown a.wcml-cs-item-toggle {
		height: <?php echo esc_html( $options['top-bar']['sticky_height'] ); ?>px;
	}
	
	/* HEIGHT ELEMENTS IN GENERAL HEADER */
	.whb-general-header .whb-divider-stretch:before,
	.whb-general-header .navigation-style-bordered .item-level-0 > a {
		height: <?php echo esc_html( $options['general-header']['height'] ); ?>px;
	}
	
	.whb-sticked:not(.whb-clone) .whb-general-header .whb-divider-stretch:before,
	.whb-sticked:not(.whb-clone) .whb-general-header .navigation-style-bordered .item-level-0 > a {
		height: <?php echo esc_html( $options['general-header']['sticky_height'] ); ?>px;
	}
	
	.whb-sticked:not(.whb-clone) .whb-general-header .woodmart-search-dropdown,
	.whb-sticked:not(.whb-clone) .whb-general-header .dropdown-cart,
	.whb-sticked:not(.whb-clone) .whb-general-header .woodmart-navigation:not(.vertical-navigation):not(.navigation-style-bordered) .sub-menu-dropdown {
		margin-top: <?php echo esc_html( ( $options['general-header']['sticky_height'] / 2 ) - 20 ); ?>px;
	}
	
	.whb-sticked:not(.whb-clone) .whb-general-header .woodmart-search-dropdown:after,
	.whb-sticked:not(.whb-clone) .whb-general-header .dropdown-cart:after,
	.whb-sticked:not(.whb-clone) .whb-general-header .woodmart-navigation:not(.vertical-navigation):not(.navigation-style-bordered) .sub-menu-dropdown:after {
		height: <?php echo esc_html( ( $options['general-header']['sticky_height'] / 2 ) - 20 ); ?>px;
	}
	
	/* HEIGHT ELEMENTS IN BOTTOM HEADER */
	.whb-header-bottom .wd-tools-element > a,
	.whb-header-bottom .main-nav .item-level-0 > a,
	.whb-header-bottom .whb-secondary-menu .item-level-0 > a,
	.whb-header-bottom .categories-menu-opener,
	.whb-header-bottom .menu-opener,
	.whb-header-bottom .whb-divider-stretch:before,
	.whb-header-bottom form.woocommerce-currency-switcher-form .dd-selected,
	.whb-header-bottom .whb-text-element .wcml-dropdown a.wcml-cs-item-toggle {
		height: <?php echo esc_html( $options['header-bottom']['height'] ); ?>px;
	}
	
	.whb-header-bottom.whb-border-fullwidth .menu-opener {
		height: <?php echo esc_html( $options['header-bottom']['height'] + $bottom_border + $header_border ); ?>px;
		margin-top: -<?php echo esc_html( $header_border ); ?>px;
		margin-bottom: -<?php echo esc_html( $bottom_border ); ?>px;
	}
	
	.whb-header-bottom.whb-border-boxed .menu-opener {
		height: <?php echo esc_html( $options['header-bottom']['height'] + $header_border ); ?>px;
		margin-top: -<?php echo esc_html( $header_border ); ?>px;
		margin-bottom: -<?php echo esc_html( $bottom_border ); ?>px;
	}
	
	.whb-sticked .whb-header-bottom .wd-tools-element > a,
	.whb-sticked .whb-header-bottom .main-nav .item-level-0 > a,
	.whb-sticked .whb-header-bottom .whb-secondary-menu .item-level-0 > a,
	.whb-sticked .whb-header-bottom .categories-menu-opener,
	.whb-sticked .whb-header-bottom .whb-divider-stretch:before,
	.whb-sticked .whb-header-bottom form.woocommerce-currency-switcher-form .dd-selected,
	.whb-sticked .whb-header-bottom .whb-text-element .wcml-dropdown a.wcml-cs-item-toggle {
		height: <?php echo esc_html( $options['header-bottom']['sticky_height'] ); ?>px;
	}
	
	.whb-sticked .whb-header-bottom.whb-border-fullwidth .menu-opener {
		height: <?php echo esc_html( $options['header-bottom']['sticky_height'] + $bottom_border + $header_border ); ?>px;
	}
	
	.whb-sticked .whb-header-bottom.whb-border-boxed .menu-opener {
		height: <?php echo esc_html( $options['header-bottom']['sticky_height'] + $header_border ); ?>px;
	}
	
	.whb-sticky-shadow.whb-sticked .whb-header-bottom .menu-opener {
		height: <?php echo esc_html( $options['header-bottom']['sticky_height'] + $header_border ); ?>px;
		margin-bottom:0;
	}
	
	/* HEIGHT ELEMENTS IN HEADER CLONE */
	.whb-clone .wd-tools-element > a,
	.whb-clone .main-nav .item-level-0 > a,
	.whb-clone .whb-secondary-menu .item-level-0 > a,
	.whb-clone .categories-menu-opener,
	.whb-clone .menu-opener,
	.whb-clone .whb-divider-stretch:before,
	.whb-clone .navigation-style-bordered .item-level-0 > a,
	.whb-clone form.woocommerce-currency-switcher-form .dd-selected,
	.whb-clone .whb-text-element .wcml-dropdown a.wcml-cs-item-toggle {
		height: <?php echo esc_html( $options['sticky_height'] ); ?>px;
	}
}

@media (max-width: 1024px) {
	.whb-top-bar-inner {
		height: <?php echo esc_html( $options['top-bar']['mobile_height'] ); ?>px;
	}
	
	.whb-general-header-inner {
		height: <?php echo esc_html( $options['general-header']['mobile_height'] ); ?>px;
	}
	
	.whb-header-bottom-inner {
		height: <?php echo esc_html( $options['header-bottom']['mobile_height'] ); ?>px;
	}
	
	/* HEIGHT OF HEADER CLONE */
	.whb-clone .whb-general-header-inner {
		height: <?php echo esc_html( $options['general-header']['mobile_height'] ); ?>px;
	}
	
	/* HEADER OVERCONTENT */
	.woodmart-header-overcontent .page-title {
		padding-top: <?php echo esc_html( $mobile_height + 15 ); ?>px;
	}
	
	/* HEADER OVERCONTENT WHEN SHOP PAGE TITLE TURN OFF  */
	.woodmart-header-overcontent .without-title.title-shop {
		padding-top: <?php echo esc_html( $mobile_height ); ?>px;
	}
	
	/* HEADER OVERCONTENT ON SINGLE PRODUCT */
	.single-product .whb-overcontent:not(.whb-custom-header) {
		padding-top: <?php echo esc_html( $mobile_height ); ?>px;
	}
	
	/* HEIGHT OF LOGO IN TOP BAR */
	.whb-top-bar .woodmart-logo img {
		max-height: <?php echo esc_html( $options['top-bar']['mobile_height'] ); ?>px;
	}
	
	/* HEIGHT OF LOGO IN GENERAL HEADER */
	.whb-general-header .woodmart-logo img {
		max-height: <?php echo esc_html( $options['general-header']['mobile_height'] ); ?>px;
	}
	
	/* HEIGHT OF LOGO IN BOTTOM HEADER */
	.whb-header-bottom .woodmart-logo img {
		max-height: <?php echo esc_html( $options['header-bottom']['mobile_height'] ); ?>px;
	}
	
	/* HEIGHT OF LOGO IN HEADER CLONE */
	.whb-clone .whb-general-header .woodmart-logo img {
		max-height: <?php echo esc_html( $options['general-header']['mobile_height'] ); ?>px;
	}
	
	/* HEIGHT OF HEADER BULDER ELEMENTS */
	/* HEIGHT ELEMENTS IN TOP BAR */
	.whb-top-bar .wd-tools-element > a,
	.whb-top-bar .main-nav .item-level-0 > a,
	.whb-top-bar .whb-secondary-menu .item-level-0 > a,
	.whb-top-bar .categories-menu-opener,
	.whb-top-bar .whb-divider-stretch:before,
	.whb-top-bar form.woocommerce-currency-switcher-form .dd-selected,
	.whb-top-bar .whb-text-element .wcml-dropdown a.wcml-cs-item-toggle {
		height: <?php echo esc_html( $options['top-bar']['mobile_height'] ); ?>px;
	}
	
	/* HEIGHT ELEMENTS IN GENERAL HEADER */
	.whb-general-header .wd-tools-element > a,
	.whb-general-header .main-nav .item-level-0 > a,
	.whb-general-header .whb-secondary-menu .item-level-0 > a,
	.whb-general-header .categories-menu-opener,
	.whb-general-header .whb-divider-stretch:before,
	.whb-general-header form.woocommerce-currency-switcher-form .dd-selected,
	.whb-general-header .whb-text-element .wcml-dropdown a.wcml-cs-item-toggle {
		height: <?php echo esc_html( $options['general-header']['mobile_height'] ); ?>px;
	}
	
	/* HEIGHT ELEMENTS IN BOTTOM HEADER */
	.whb-header-bottom .wd-tools-element > a,
	.whb-header-bottom .main-nav .item-level-0 > a,
	.whb-header-bottom .whb-secondary-menu .item-level-0 > a,
	.whb-header-bottom .categories-menu-opener,
	.whb-header-bottom .whb-divider-stretch:before,
	.whb-header-bottom form.woocommerce-currency-switcher-form .dd-selected,
	.whb-header-bottom .whb-text-element .wcml-dropdown a.wcml-cs-item-toggle {
		height: <?php echo esc_html( $options['header-bottom']['mobile_height'] ); ?>px;
	}
	
	/* HEIGHT ELEMENTS IN HEADER CLONE */
	.whb-clone .wd-tools-element > a,
	.whb-clone .main-nav .item-level-0 > a,
	.whb-clone .whb-secondary-menu .item-level-0 > a,
	.whb-clone .categories-menu-opener,
	.whb-clone .menu-opener,
	.whb-clone .whb-divider-stretch:before,
	.whb-clone form.woocommerce-currency-switcher-form .dd-selected,
	.whb-clone .whb-text-element .wcml-dropdown a.wcml-cs-item-toggle {
		height: <?php echo esc_html( $options['general-header']['mobile_height'] ); ?>px;
	}
}
<?php

		return ob_get_clean();
	}
}
