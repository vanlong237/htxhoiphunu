<?php 

/* Template name: Portfolio */


get_header(); 

if ( 'elementor' === woodmart_get_opt( 'page_builder', 'wpb' ) ) {
	woodmart_elementor_portfolio_template( array( 'portfolio_location' => 'page' ) );
} else {
	echo woodmart_shortcode_portfolio( array( 'portfolio_location' => 'page' ) );
}

do_action( 'woodmart_after_portfolio_loop' );

get_footer(); ?>