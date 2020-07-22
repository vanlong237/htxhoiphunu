<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) exit( 'No direct script access allowed' );

/**
* ------------------------------------------------------------------------------------------------
* Portfolio shortcode
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'woodmart_shortcode_portfolio' ) ) {
	function woodmart_shortcode_portfolio( $atts ) {
		if ( woodmart_get_opt( 'disable_portfolio' ) ) return;
		
		$output = $el_class = '';
		$parsed_atts = shortcode_atts( array(
			'posts_per_page' => woodmart_get_opt( 'portoflio_per_page' ),
			'filters' => false,
			'categories' => '',
			'style' => woodmart_get_opt( 'portoflio_style' ),
			'columns' => woodmart_get_opt( 'projects_columns' ),
			'spacing' => woodmart_get_opt( 'portfolio_spacing' ),
			'pagination' => woodmart_get_opt( 'portfolio_pagination' ),
			'ajax_page' => '',
			'orderby' => woodmart_get_opt( 'portoflio_orderby' ),
			'order' => woodmart_get_opt( 'portoflio_order' ),
			'portfolio_location' => '',
			'layout' => 'grid',
			// 'speed' => '5000',
			// 'slides_per_view' => '1',
			// 'wrap' => '',
			// 'autoplay' => 'no',
			// 'hide_pagination_control' => '',
			// 'hide_prev_next_buttons' => '',
			// 'scroll_per_page' => 'yes',
			'lazy_loading' => 'no',
			'el_class' => ''
		), $atts );

		extract( $parsed_atts );

		$encoded_atts = json_encode( $parsed_atts );

		$is_ajax = (defined( 'DOING_AJAX' ) && DOING_AJAX);
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		if( $ajax_page > 1 ) $paged = $ajax_page;

		$s = false;

		if( isset( $_REQUEST['s'] ) ) {
			$s = sanitize_text_field( $_REQUEST['s'] );
		}

		$args = array(
			'post_type' => 'portfolio',
			'posts_per_page' => $posts_per_page,
			'orderby' => $orderby,
			'order' => $order,
			'paged' => $paged
		);

		if( $s ) {
			$args['s'] = $s;
		}
 
		if( get_query_var('project-cat') != '' ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'project-cat',
					'field'    => 'slug',
					'terms'    => get_query_var('project-cat')
				),
			);
		}

		if( $categories != '' ) {

			$args['tax_query'] = array(
				array(
					'taxonomy' => 'project-cat',
					'field'    => 'term_id',
					'operator' => 'IN',
					'terms'    => $categories
				),
			);
		}

		if ( $portfolio_location == 'page' ) {
			$filters = woodmart_get_opt( 'portoflio_filters' );
		}

		if ( empty( $style ) || $style == 'inherit' ) $style = woodmart_get_opt( 'portoflio_style' );

		woodmart_set_loop_prop( 'portfolio_style', $style );
		woodmart_set_loop_prop( 'portfolio_column', $columns );
		
		if ( $style == 'parallax' ) {
			woodmart_enqueue_script( 'woodmart-panr-parallax' );
		}

		$query = new WP_Query( $args );

		$parsed_atts['custom_sizes'] = apply_filters( 'woodmart_portfolio_shortcode_custom_sizes', false );

		if ( $lazy_loading == 'yes' ) {
			woodmart_lazy_loading_init( true );
		}

		ob_start();

		?>

			<?php if ( ! $is_ajax && $portfolio_location == 'page' ): ?>
				<div class="site-content page-portfolio col-12" role="main">
			<?php endif ?>

				<?php if ( $query->have_posts() ) : ?>
					<?php if ( ! $is_ajax ): ?>
						<?php if ( ! is_tax() && $filters && ! $s && $layout != 'carousel' ): ?>
							<?php 
								$cats = get_terms( 'project-cat', array( 'parent' => $categories ) );
								if ( ! is_wp_error( $cats ) && ! empty( $cats ) ) : ?>
									<div class="portfolio-filter">
										<ul class="masonry-filter list-inline text-center">
											<li><a href="#" data-filter="*" class="filter-active"><?php esc_html_e('All', 'woodmart'); ?></a></li>
										<?php foreach ($cats as $key => $cat) : ?>
											<li><a href="#" data-filter=".proj-cat-<?php echo esc_attr( $cat->slug ); ?>"><?php echo esc_html( $cat->name ); ?></a></li>
										<?php endforeach?>
										</ul>
									</div>
								<?php endif ?>

						<?php endif ?>

						<div class="<?php echo 'carousel' !== $layout ? 'masonry-container' : ''; ?> woodmart-portfolio-holder row woodmart-spacing-<?php echo esc_attr( $spacing ); ?>" data-atts="<?php echo esc_attr( $encoded_atts ); ?>" data-source="shortcode" data-paged="1">
					<?php endif ?>
						<?php 
							/* The loop */
								if ( $layout == 'carousel' ) {
								echo woodmart_generate_posts_slider( $parsed_atts, $query );
							} else {
								while ( $query->have_posts() ) {
									$query->the_post();
									get_template_part( 'content', 'portfolio' );
								}
							}
						?>

					<?php if ( ! $is_ajax ): ?>
						</div>
						<?php
							if ( $query->max_num_pages > 1 && !$is_ajax && $pagination != 'disable' && $layout != 'carousel' ) {
								?>
							    	<div class="portfolio-footer">
							    		<?php if ( $pagination == 'infinit' || $pagination == 'load_more'): ?>
							    			<a href="#" rel="nofollow" class="btn woodmart-load-more woodmart-portfolio-load-more load-on-<?php echo 'load_more' === $pagination ? 'click' : 'scroll'; ?>"><span class="load-more-label"><?php esc_html_e('Load more projects', 'woodmart'); ?></span><span class="load-more-loading"><?php esc_html_e('Loading...', 'woodmart'); ?></span></a>
						    			<?php else: ?>
							    			<?php query_pagination( $query->max_num_pages ); ?>
							    		<?php endif ?>
							    	</div>
							    <?php
							}
						?>
						
					<?php endif ?>

				<?php elseif ( ! $is_ajax ) : ?>
					<?php get_template_part( 'content', 'none' ); ?>
				<?php endif; ?>

			<?php if ( ! $is_ajax && $portfolio_location == 'page' ): ?>
				</div><!-- .site-content -->
			<?php endif ?>
		<?php

		$output .= ob_get_clean();

		if ( $lazy_loading == 'yes' ) {
			woodmart_lazy_loading_deinit();
		}

		wp_reset_postdata();
		
		woodmart_reset_loop();

	    if( $is_ajax ) {
	    	$output =  array(
	    		'items' => $output,
	    		'status' => ( $query->max_num_pages > $paged ) ? 'have-posts' : 'no-more-posts'
	    	);
	    }

		return $output;
	}
}
