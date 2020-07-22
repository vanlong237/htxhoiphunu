<?php
/**
 * Product Loop Start
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.3.0
 */

$spacing      = woodmart_get_opt( 'products_spacing' );
$class        = '';
$is_list_view = woodmart_loop_prop( 'products_view' ) == 'list';

if ( woodmart_loop_prop( 'products_masonry' ) ) {
	$class .= ' grid-masonry';
	woodmart_enqueue_script( 'isotope' );
	woodmart_enqueue_script( 'woodmart-packery-mode' );
}

if ( $is_list_view ) {
	$class .= ' elements-list';
} else {
	$class .= ' woodmart-spacing-' . $spacing;
}

if ( woodmart_get_opt( 'products_bordered_grid' ) ) {
	$class .= ' products-bordered-grid';
}

if ( woodmart_get_opt( 'product_quantity' ) ) {
	$class .= ' wd-quantity-enabled';
}

$class .= ' pagination-' . woodmart_get_opt( 'shop_pagination' );

if ( 'none' !== woodmart_get_opt( 'product_title_lines_limit' ) && ! $is_list_view ) {
	$class .= ' title-line-' . woodmart_get_opt( 'product_title_lines_limit' );
}

// fix for price filter ajax
$min_price = isset( $_GET['min_price'] ) ? esc_attr( $_GET['min_price'] ) : '';
$max_price = isset( $_GET['max_price'] ) ? esc_attr( $_GET['max_price'] ) : '';

if ( woodmart_get_opt( 'shop_countdown' ) ) woodmart_enqueue_script( 'woodmart-countdown' );

?>

<div class="products elements-grid align-items-start woodmart-products-holder <?php echo esc_attr( $class ); ?> row grid-columns-<?php echo esc_attr( woodmart_loop_prop( 'products_columns' ) ); ?>" data-source="main_loop" data-min_price="<?php echo esc_attr( $min_price ); ?>" data-max_price="<?php echo esc_attr( $max_price ); ?>">