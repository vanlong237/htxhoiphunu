<?php
/**
 * Portfolio template function
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_elementor_portfolio_template' ) ) {
	function woodmart_elementor_portfolio_template( $settings ) {
		if ( woodmart_get_opt( 'disable_portfolio' ) ) {
			 return '';
		}

		$default_settings = [
			'posts_per_page'     => woodmart_get_opt( 'portoflio_per_page' ),
			'filters'            => false,
			'categories'         => '',
			'style'              => woodmart_get_opt( 'portoflio_style' ),
			'columns'            => woodmart_get_opt( 'projects_columns' ),
			'spacing'            => woodmart_get_opt( 'portfolio_spacing' ),
			'pagination'         => woodmart_get_opt( 'portfolio_pagination' ),
			'ajax_page'          => '',
			'orderby'            => woodmart_get_opt( 'portoflio_orderby' ),
			'order'              => woodmart_get_opt( 'portoflio_order' ),
			'portfolio_location' => '',
			'layout'             => 'grid',
			'lazy_loading'       => 'no',
			'elementor'          => true,
			'custom_sizes'       => apply_filters( 'woodmart_portfolio_shortcode_custom_sizes', false ),
		];

		$settings            = wp_parse_args( $settings, $default_settings );
		$settings['columns'] = isset( $settings['columns']['size'] ) ? $settings['columns']['size'] : $settings['columns'];
		$encoded_settings    = wp_json_encode( array_intersect_key( $settings, $default_settings ) );
		$is_ajax             = woodmart_is_woo_ajax();
		$paged               = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

		if ( $settings['ajax_page'] > 1 ) {
			$paged = $settings['ajax_page'];
		}

		$s = false;

		if ( isset( $_REQUEST['s'] ) ) {
			$s = sanitize_text_field( $_REQUEST['s'] );
		}

		$args = array(
			'post_type'      => 'portfolio',
			'posts_per_page' => $settings['posts_per_page'],
			'orderby'        => $settings['orderby'],
			'order'          => $settings['order'],
			'paged'          => $paged,
		);

		if ( $s ) {
			$args['s'] = $s;
		}

		if ( '' != get_query_var( 'project-cat' ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'project-cat',
					'field'    => 'slug',
					'terms'    => get_query_var( 'project-cat' ),
				),
			);
		}

		if ( $settings['categories'] ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'project-cat',
					'field'    => 'term_id',
					'operator' => 'IN',
					'terms'    => $settings['categories'],
				),
			);
		}

		if ( 'page' === $settings['portfolio_location'] ) {
			$settings['filters'] = woodmart_get_opt( 'portoflio_filters' );
		}

		if ( ! $settings['style'] || ( 'inherit' === $settings['style'] ) ) {
			$settings['style'] = woodmart_get_opt( 'portoflio_style' );
		}

		woodmart_set_loop_prop( 'portfolio_style', $settings['style'] );
		woodmart_set_loop_prop( 'portfolio_column', $settings['columns'] );

		if ( 'parallax' === $settings['style'] ) {
			woodmart_enqueue_script( 'woodmart-panr-parallax' );
		}

		$query = new WP_Query( $args );

		if ( 'yes' === $settings['lazy_loading'] ) {
			woodmart_lazy_loading_init( true );
		}
		
		if ( $is_ajax ) {
			ob_start();
		}

		?>
		<?php if ( ! $is_ajax && 'page' === $settings['portfolio_location'] ) : ?>
			<div class="site-content page-portfolio col-12" role="main">
		<?php endif ?>
	
		<?php if ( $query->have_posts() ) : ?>
			<?php if ( ! $is_ajax ) : ?>
				<?php if ( ! is_tax() && $settings['filters'] && ! $s && 'carousel' !== $settings['layout'] ) : ?>
					<?php $cats = get_terms( 'project-cat', array( 'parent' => $settings['categories'] ) ); ?>
					<?php if ( ! is_wp_error( $cats ) && ! empty( $cats ) ) : ?>
						<div class="portfolio-filter">
							<ul class="masonry-filter list-inline text-center">
								<li>
									<a href="#" data-filter="*" class="filter-active">
										<?php esc_html_e( 'All', 'woodmart' ); ?>
									</a>
								</li>

								<?php foreach ( $cats as $key => $cat ) : ?>
									<li>
										<a href="#" data-filter=".proj-cat-<?php echo esc_attr( $cat->slug ); ?>">
											<?php echo esc_html( $cat->name ); ?>
										</a>
									</li>
								<?php endforeach ?>
							</ul>
						</div>
					<?php endif; ?>
				<?php endif ?>

				<div class="<?php echo $settings['layout'] !== 'carousel' ? 'masonry-container' : ''; ?> woodmart-portfolio-holder row woodmart-spacing-<?php echo esc_attr( $settings['spacing'] ); ?>" data-atts="<?php echo esc_attr( $encoded_settings ); ?>" data-source="shortcode" data-paged="1">
			<?php endif ?>
	
			<?php if ( 'carousel' === $settings['layout'] ) : ?>
				<?php echo woodmart_generate_posts_slider( $settings, $query ); ?>
			<?php else : ?>
				<?php while ( $query->have_posts() ) : ?>
					<?php $query->the_post(); ?>
					<?php get_template_part( 'content', 'portfolio' ); ?>
				<?php endwhile; ?>
			<?php endif; ?>
	
			<?php if ( ! $is_ajax ) : ?>
				</div>
				
					<?php if ( $query->max_num_pages > 1 && ! $is_ajax && 'disable' !== $settings['pagination'] && 'carousel' !== $settings['layout'] ) : ?>
				<div class="portfolio-footer">
						<?php if ( 'infinit' === $settings['pagination'] || 'load_more' === $settings['pagination'] ) : ?>
						<a href="#" rel="nofollow" class="btn woodmart-load-more woodmart-portfolio-load-more load-on-<?php echo $settings['pagination'] === 'load_more' ? 'click' : 'scroll'; ?>"><span class="load-more-label"><?php esc_html_e( 'Load more projects', 'woodmart' ); ?></span><span class="load-more-loading"><?php esc_html_e( 'Loading...', 'woodmart' ); ?></span></a>
					<?php else : ?>
						<?php query_pagination( $query->max_num_pages ); ?>
					<?php endif ?>
				</div>
			<?php endif ?>
		<?php endif ?>
		
		<?php elseif ( ! $is_ajax ) : ?>
			<?php get_template_part( 'content', 'none' ); ?>
		<?php endif; ?>

		<?php if ( ! $is_ajax && 'page' === $settings['portfolio_location'] ) : ?>
			</div>
		<?php endif ?>
		<?php

		if ( 'yes' === $settings['lazy_loading'] ) {
			woodmart_lazy_loading_deinit();
		}

		wp_reset_postdata();

		woodmart_reset_loop();
		
		if ( $is_ajax ) {
			return array(
				'items'  => ob_get_clean(),
				'status' => $query->max_num_pages > $paged ? 'have-posts' : 'no-more-posts',
			);
		}
	}
}
