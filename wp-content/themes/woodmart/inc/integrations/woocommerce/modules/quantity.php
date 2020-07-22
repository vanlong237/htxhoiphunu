<?php
if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! function_exists( 'woodmart_product_quantity' ) ) {
	function woodmart_product_quantity( $product ) {
		if ( ! $product->is_sold_individually() && 'variable' != $product->get_type() && $product->is_purchasable() && $product->is_in_stock() ) {
			woocommerce_quantity_input(
				array(
					'min_value' => 1,
					'max_value' => $product->backorders_allowed() ? '' : $product->get_stock_quantity(),
				)
			);
		}
	}
}

if ( ! function_exists( 'woodmart_update_cart_item' ) ) {
	function woodmart_update_cart_item() {
		if ( ( isset( $_GET['item_id'] ) && $_GET['item_id'] ) && ( isset( $_GET['qty'] ) && $_GET['qty'] ) ) {
			global $woocommerce;
			$woocommerce->cart->set_quantity( $_GET['item_id'], $_GET['qty'] );
		}

		WC_AJAX::get_refreshed_fragments();
	}
	
	add_action( 'wp_ajax_woodmart_update_cart_item', 'woodmart_update_cart_item' );
	add_action( 'wp_ajax_nopriv_woodmart_update_cart_item', 'woodmart_update_cart_item' );
}