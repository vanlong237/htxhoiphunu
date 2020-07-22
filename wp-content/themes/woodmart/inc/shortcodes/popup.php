<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) exit( 'No direct script access allowed' );

/**
* ------------------------------------------------------------------------------------------------
* Content in popup
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'woodmart_shortcode_popup' ) ) {
	function woodmart_shortcode_popup( $atts, $content = '' ) {
		$output = '';
		$parsed_atts = shortcode_atts( array(
			'id' 	 	 => 'my_popup',
			'title' 	 => 'GO',
			'link' 	 	 => '',
			'width' 	 => 800,

			'color' 	 => 'default',
			'style'   	 => 'default',
			'shape'   	 => 'rectangle',
			'size' 		 => 'default',
			'align' 	 => 'center',
			'button_inline' => 'no',
			'full_width' => 'no',
			'bg_color' => '',
			'bg_color_hover' => '',
			'color_scheme' => 'light',
			'color_scheme_hover' => 'light',
			
			'woodmart_css_id' => '',
			'css_animation' => 'none',
			'el_class' 	 => '',
			
			'icon_fontawesome' => '',
			'icon_openiconic' => '',
			'icon_typicons' => '',
			'icon_entypo' => '',
			'icon_linecons' => '',
			'icon_monosocial' => '',
			'icon_material' => '',
			'icon_library' => 'fontawesome',
			'icon_position' => 'right',
		), $atts) ;

		extract( $parsed_atts );

		$parsed_atts['link'] = 'url:#' . esc_attr( $id ) . '|||';
		$parsed_atts['el_class'] = 'woodmart-open-popup ' . $el_class;

		$output .= woodmart_shortcode_button( $parsed_atts , true );

		$output .= '<div id="' . esc_attr( $id ) . '" class="mfp-with-anim woodmart-content-popup mfp-hide" style="max-width:' . esc_attr( $width ) . 'px;"><div class="woodmart-popup-inner">' . do_shortcode( $content ) . '</div></div>';

		return $output;

	}
}
