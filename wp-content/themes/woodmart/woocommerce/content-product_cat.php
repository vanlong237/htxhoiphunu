<?php
/**
 * The template for displaying product category thumbnails within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product_cat.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version 	2.6.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$isotope 		   		   = woodmart_loop_prop( 'products_masonry' );
$different_sizes  		   = woodmart_loop_prop( 'products_different_sizes' );
$categories_design 		   = woodmart_loop_prop( 'product_categories_design' );
$product_categories_shadow = woodmart_loop_prop( 'product_categories_shadow' );
$product_categories_style  = woodmart_loop_prop( 'product_categories_style' );
$loop_column 			   = woodmart_loop_prop( 'products_columns' );
$classes 				   = array();
$hide_product_count        = woodmart_get_opt( 'hide_categories_product_count' );

if( $different_sizes ) $isotope = true;

// Increase loop count
woodmart_set_loop_prop( 'woocommerce_loop', woodmart_loop_prop( 'woocommerce_loop' ) + 1 );

$woocommerce_loop = woodmart_loop_prop( 'woocommerce_loop' );

$items_wide = woodmart_get_wide_items_array( $different_sizes );

$is_double_size = $different_sizes && in_array( $woocommerce_loop, $items_wide );

woodmart_set_loop_prop( 'double_size', $is_double_size );

$xs_columns = (int) woodmart_get_opt( 'products_columns_mobile' );
$xs_size = 12 / $xs_columns;

if( $product_categories_style != 'carousel' ) {
	$classes[] = woodmart_get_grid_el_class( $woocommerce_loop, $loop_column, $different_sizes, $xs_size );
}
	
$classes[] = 'category-grid-item';
$classes[] = 'cat-design-' . $categories_design;

if ( $product_categories_shadow != 'disable' && ( $categories_design == 'alt' || $categories_design == 'default' ) ) {
	$classes[] = 'categories-with-shadow';
}

if ( $hide_product_count ) {
	$classes[] = 'without-product-count';
}

?>
<div <?php wc_product_cat_class( $classes, $category ); ?> data-loop="<?php echo esc_attr( $woocommerce_loop ); ?>">
	<div class="wrapp-category">
		<div class="category-image-wrapp">
			<a href="<?php echo esc_url( get_term_link( $category->slug, 'product_cat' ) ); ?>" class="category-image">
				<?php do_action( 'woocommerce_before_subcategory', $category ); ?>

				<?php
					/**
					 * woocommerce_before_subcategory_title hook
					 *
					 * @hooked woodmart_category_thumb_double_size - 10
					 */
					do_action( 'woocommerce_before_subcategory_title', $category );
				?>
			</a>
		</div>
		<div class="hover-mask">
			<h3 class="category-title">
				<?php
					echo esc_html( $category->name );

					if ( $category->count > 0 ) echo apply_filters( 'woocommerce_subcategory_count_html', ' <mark class="count">(' . $category->count . ')</mark>', $category );
				?>
			</h3>

			<?php if ( ! $hide_product_count ): ?>
				<div class="more-products"><a href="<?php echo esc_url( get_term_link( $category->slug, 'product_cat' ) ); ?>"><?php echo sprintf( _n( '%s product', '%s products', $category->count, 'woodmart' ), $category->count ); ?></a></div>
			<?php endif; ?>

			<?php
				/**
				 * woocommerce_after_subcategory_title hook
				 */
				do_action( 'woocommerce_after_subcategory_title', $category );
			?>
		</div>
		<a href="<?php echo esc_url( get_term_link( $category->slug, 'product_cat' ) ); ?>" class="category-link"></a>
		<?php do_action( 'woocommerce_after_subcategory', $category ); ?>
	</div>
</div>