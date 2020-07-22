<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) exit( 'No direct script access allowed' );

/**
 * ------------------------------------------------------------------------------------------------
 * Shortcode function to display posts as a slider or as a grid
 * ------------------------------------------------------------------------------------------------
 */

if ( ! function_exists( 'woodmart_shortcode_products' ) ) {
	
	function woodmart_shortcode_products( $atts, $query = false ) {
		
		$parsed_atts = shortcode_atts( woodmart_get_default_product_shortcode_atts(), $atts );
		
		extract( $parsed_atts );
		
		$is_ajax = ( defined( 'DOING_AJAX' ) && DOING_AJAX && $force_not_ajax != 'yes' );
		
		$parsed_atts['force_not_ajax'] = 'no'; // :)
		
		$encoded_atts = json_encode( $parsed_atts );
		
		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		
		if ( $ajax_page > 1 ) $paged = $ajax_page;
		
		$ordering_args = WC()->query->get_catalog_ordering_args( $orderby, $order );
		
		$meta_query   = WC()->query->get_meta_query();
		
		$tax_query   = WC()->query->get_tax_query();
		
		if ( $post_type == 'new' ){
			$meta_query[] = array(
				'key'     => '_woodmart_new_label',
				'value'   => 'on',
				'operator' => 'IN',
			);
		}
		
		if ( $orderby == 'post__in' ) {
			$ordering_args['orderby'] = $orderby;
		}
		
		$args = array(
			'post_type' 			=> 'product',
			'post_status' 			=> 'publish',
			'ignore_sticky_posts' 	=> 1,
			'paged' 			  	=> $paged,
			'orderby'             	=> $ordering_args['orderby'],
			'order'               	=> $ordering_args['order'],
			'posts_per_page' 		=> $items_per_page,
			'meta_query' 			=> $meta_query,
			'tax_query'             => $tax_query,
		);
		
		if ( ! empty( $ordering_args['meta_key'] ) ) {
			$args['meta_key'] = $ordering_args['meta_key'];
		}
		
		if ( ! empty( $meta_key ) ) {
			$args['meta_key'] = $meta_key;
		}
		
		if ( $post_type == 'ids' && $include != '' ) {
			$args['post__in'] = array_map('trim', explode(',', $include) );
		}
		
		if ( ! empty( $exclude ) ) {
			$args['post__not_in'] = array_map('trim', explode(',', $exclude) );
		}
		
		if ( ! empty( $taxonomies ) ) {
			$taxonomy_names = get_object_taxonomies( 'product' );
			$terms = get_terms( $taxonomy_names, array(
				'orderby' => 'name',
				'include' => $taxonomies,
				'hide_empty' => false,
			));
			
			if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
				if ( $post_type == 'featured' ) $args['tax_query'] = array( 'relation' => 'AND' );
				
				$relation = $query_type ? $query_type : 'OR';
				if ( count( $terms ) > 1 ) $args['tax_query']['categories'] = array( 'relation' => $relation );
				
				foreach ( $terms as $term ) {
					$args['tax_query']['categories'][] = array(
						'taxonomy' => $term->taxonomy,
						'field' => 'slug',
						'terms' => array( $term->slug ),
						'include_children' => true,
						'operator' => 'IN'
					);
				}
			}
		}
		
		if ( $post_type == 'featured' ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => 'featured',
				'operator' => 'IN',
				'include_children' => false,
			);
		}
		
		if ( apply_filters( 'woodmart_hide_out_of_stock_items', false ) && 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
			$args['meta_query'][] = array( 'key' => '_stock_status', 'value' => 'outofstock', 'compare' => 'NOT IN' );
		}
		
		if ( ! empty( $order ) ) {
			$args['order'] = $order;
		}
		
		if ( ! empty( $offset ) ) {
			$args['offset'] = $offset;
		}
		
		
		if ( $post_type == 'sale' ) {
			$args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
		}
		
		if ( $post_type == 'bestselling' ) {
			$args['orderby'] = 'meta_value_num';
			$args['meta_key'] = 'total_sales';
			$args['order'] = 'DESC';
		}
		
		if ( empty( $product_hover ) || $product_hover == 'inherit' ) $product_hover = woodmart_get_opt( 'products_hover' );
		
		if ( $sale_countdown ) {
			woodmart_enqueue_script( 'woodmart-countdown' );
		}
		
		woodmart_set_loop_prop( 'timer', $sale_countdown );
		woodmart_set_loop_prop( 'progress_bar', $stock_progress_bar );
		woodmart_set_loop_prop( 'product_hover', $product_hover );
		woodmart_set_loop_prop( 'products_view', $layout );
		woodmart_set_loop_prop( 'is_shortcode', true );
		woodmart_set_loop_prop( 'img_size', $img_size );
		woodmart_set_loop_prop( 'products_columns', $columns );

		if ( $products_masonry ) woodmart_set_loop_prop( 'products_masonry', ( $products_masonry == 'enable' ) ? true : false );
		if ( $product_quantity ) woodmart_set_loop_prop( 'product_quantity', ( $product_quantity == 'enable' ) ? true : false );
		if ( $products_different_sizes ) woodmart_set_loop_prop( 'products_different_sizes', ( $products_different_sizes == 'enable' ) ? true : false );
		
		if ( 'top_rated_products' === $post_type ) {
			add_filter( 'posts_clauses', 'woodmart_order_by_rating_post_clauses' );
			$products = new WP_Query( $args );
			remove_filter( 'posts_clauses', 'woodmart_order_by_rating_post_clauses' );
		} else {
			$products = new WP_Query( $args );
		}
		
		WC()->query->remove_ordering_args();
		
		$parsed_atts['custom_sizes'] = apply_filters( 'woodmart_products_shortcode_custom_sizes', false );
		
		// Simple products carousel
		if ( $layout == 'carousel' ){
			return woodmart_generate_posts_slider( $parsed_atts, $products );
		}
		
		if ( $pagination != 'arrows' ) {
			woodmart_set_loop_prop( 'woocommerce_loop', $items_per_page * ( $paged - 1 ) );
		}
		
		if ( $layout == 'list' ) {
			$class .= ' elements-list';
		} else {
			if ( ! $highlighted_products ) {
				$class .= ' woodmart-spacing-' . $spacing;
			}
			$class .= ' grid-columns-' . $columns;
		}
		
		$class .= ' pagination-' . $pagination;
		
		if ( woodmart_loop_prop( 'products_masonry' ) ) {
			$class .= ' grid-masonry';
			woodmart_enqueue_script( 'isotope' );
			woodmart_enqueue_script( 'woodmart-packery-mode' );
		}
		
		$products_element_classes = $highlighted_products ? ' woodmart-highlighted-products' : '';
		$products_element_classes .= ( $element_title ) ? ' with-title' : '';
		$products_element_classes .= $el_class ? ' ' . $el_class : '';
		
		if ( $products_bordered_grid && ! $highlighted_products ) {
			$class .= ' products-bordered-grid';
		}
		
		if ( woodmart_loop_prop( 'product_quantity' ) ) {
			$class .= ' wd-quantity-enabled';
		}
		
		if ( 'none' !== woodmart_get_opt( 'product_title_lines_limit' ) && $layout !== 'list' ) {
			$class .= ' title-line-' . woodmart_get_opt( 'product_title_lines_limit' );
		}
		
		if ( $lazy_loading == 'yes' ) {
			woodmart_lazy_loading_init( true );
		}
		
		ob_start();
		
		if ( ! $is_ajax ) echo '<div class="woodmart-products-element' . esc_attr( $products_element_classes ) . '">';
		
		if ( ! $is_ajax && $pagination == 'arrows' ) echo '<div class="woodmart-products-loader"></div>';
		
		if ( ! $is_ajax ) echo '<div class="products elements-grid align-items-start row woodmart-products-holder ' . esc_attr( $class ) . '" data-paged="1" data-atts="' . esc_attr( $encoded_atts ) . '" data-source="shortcode">';
		
		//Element title
		if ( ( ! $is_ajax || $pagination == 'arrows' ) && $element_title ) echo '<h4 class="title element-title col-12">' . esc_html( $element_title ) . '</h4>';
		
		if ( $products->have_posts() ) :
			while ( $products->have_posts() ) :
				$products->the_post();
				wc_get_template_part( 'content', 'product' );
			endwhile;
		endif;
		
		if ( ! $is_ajax ) echo '</div>';
		
		if ( function_exists( 'woocommerce_reset_loop' ) ) woocommerce_reset_loop();
		
		wp_reset_postdata();
		
		woodmart_reset_loop();
		
		if ( $lazy_loading == 'yes' ) {
			woodmart_lazy_loading_deinit();
		}
		
		if ( $products->max_num_pages > 1 && ! $is_ajax && $pagination ) {
			?>
			<div class="products-footer">
				<?php if ( $pagination == 'more-btn' || $pagination == 'infinit' ): ?>
					<a href="#" rel="nofollow" class="btn woodmart-load-more woodmart-products-load-more load-on-<?php echo 'more-btn' === $pagination ? 'click' : 'scroll'; ?>"><span class="load-more-label"><?php esc_html_e( 'Load more products', 'woodmart' ); ?></span><span class="load-more-loading"><?php esc_html_e( 'Loading...', 'woodmart' ); ?></span></a>
				<?php elseif ( $pagination == 'arrows' ): ?>
					<div class="wrap-loading-arrow">
						<div class="woodmart-products-load-prev disabled"><?php esc_html_e( 'Load previous products', 'woodmart' ); ?></div>
						<div class="woodmart-products-load-next"><?php esc_html_e( 'Load next products', 'woodmart' ); ?></div>
					</div>
				<?php elseif ( $pagination == 'links' ): ?>
					<nav class="woocommerce-pagination">
						<?php
						$url = woodmart_get_whishlist_page_url();
						$id  = get_query_var( 'wishlist_id' );
						
						if ( $id && $id > 0 ) {
							$url .= $id . '/';
						}
						
						if ( '' != get_option( 'permalink_structure' ) ) {
							$base = user_trailingslashit( $url . 'page/%#%' );
						} else {
							$base = add_query_arg( 'page', '%#%', $url );
						}
						
						add_filter( 'get_pagenum_link', '__return_false' );
						
						echo paginate_links( //phpcs:ignore
							array(
								'base'      => $base,
								'add_args'  => true,
								'total'     => $products->max_num_pages,
								'prev_text' => '&larr;',
								'next_text' => '&rarr;',
								'type'      => 'list',
								'end_size'  => 3,
								'mid_size'  => 3,
							)
						);
						
						remove_filter( 'get_pagenum_link', '__return_false' );
						?>
					</nav>
				<?php endif ?>
			</div>
			<?php
		}
		
		if ( ! $is_ajax ) echo '</div>';
		
		$output = ob_get_clean();
		
		if ( $is_ajax ) {
			$output =  array(
				'items' => $output,
				'status' => ( $products->max_num_pages > $paged ) ? 'have-posts' : 'no-more-posts'
			);
		}
		
		return $output;
		
	}
	
}

if ( ! function_exists( 'woodmart_order_by_rating_post_clauses' ) ) {
	function woodmart_order_by_rating_post_clauses( $args ) {
		global $wpdb;
		
		$args['where']  .= " AND $wpdb->commentmeta.meta_key = 'rating' ";
		$args['join']   .= "LEFT JOIN $wpdb->comments ON($wpdb->posts.ID = $wpdb->comments.comment_post_ID) LEFT JOIN $wpdb->commentmeta ON($wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id)";
		$args['orderby'] = "$wpdb->commentmeta.meta_value DESC";
		$args['groupby'] = "$wpdb->posts.ID";
		
		return $args;
	}
}

if ( ! function_exists( 'woodmart_get_default_product_shortcode_atts' ) ) {
	function woodmart_get_default_product_shortcode_atts() {
		return array(
			'element_title' => '',
			'post_type'  => 'product',
			'layout' => 'grid',
			'include'  => '',
			'custom_query'  => '',
			'taxonomies'  => '',
			'pagination'  => '',
			'items_per_page'  => 12,
			'product_hover'  => woodmart_get_opt( 'products_hover' ),
			'spacing'  => woodmart_get_opt( 'products_spacing' ),
			'columns'  => 4,
			'sale_countdown' => 0,
			'stock_progress_bar' => 0,
			'highlighted_products' => 0,
			'products_bordered_grid' => 0,
			'product_quantity'         => 0,
			'offset'  => '',
			'orderby'  => '',
			'query_type'  => 'OR',
			'order'  => '',
			'meta_key'  => '',
			'exclude'  => '',
			'class'  => '',
			'ajax_page' => '',
			'speed' => '5000',
			'slides_per_view' => '4',
			'wrap' => '',
			'autoplay' => 'no',
			'center_mode' => 'no',
			'hide_pagination_control' => '',
			'hide_prev_next_buttons' => '',
			'scroll_per_page' => 'yes',
			'carousel_js_inline' => 'no',
			'img_size' => 'woocommerce_thumbnail',
			'force_not_ajax' => 'no',
			'products_masonry' => woodmart_get_opt( 'products_masonry' ),
			'products_different_sizes' => woodmart_get_opt( 'products_different_sizes' ),
			'lazy_loading' => 'no',
			'scroll_carousel_init' => 'no',
			'el_class' => '',
		);
	}
}