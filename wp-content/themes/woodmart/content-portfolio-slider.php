<?php
/**
 * The default template for displaying content
 */

$size = 'large';

$classes[] = 'portfolio-entry';

$columns = woodmart_loop_prop( 'portfolio_column' );
$style   = woodmart_loop_prop( 'portfolio_style' );

$classes[] = 'portfolio-single';

$cats = wp_get_post_terms( get_the_ID(), 'project-cat' );

if( ! empty( $cats ) ) {
	foreach ($cats as $key => $cat) {
		$classes[] = 'proj-cat-' . $cat->slug;
	}
}

$classes[] = 'portfolio-' . $style;

if ( $style == 'parallax' ) {
	woodmart_enqueue_script( 'woodmart-panr-parallax' );
}

woodmart_enqueue_script( 'woodmart-photoswipe' );

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $classes ); ?>>
	<header class="entry-header">
		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="entry-thumbnail">
				<a href="<?php echo esc_url( get_permalink() ); ?>" class="portfolio-thumbnail">
					<?php the_post_thumbnail( $size ); ?>
				</a>
				<a href="<?php echo esc_url( wp_get_attachment_url( get_post_thumbnail_id($post->ID) ) ); ?>" class="portfolio-enlarge" data-elementor-open-lightbox="no"><?php esc_html_e('View Large', 'woodmart'); ?></a>
				<?php if ( woodmart_is_social_link_enable( 'share' ) ): ?>
					<div class="social-icons-wrapper">
					<?php if( function_exists( 'woodmart_shortcode_social' ) ) echo woodmart_shortcode_social( array( 'size' => 'small', 'style' => 'default', 'color' => 'light' ) ); ?>
					</div>
				<?php endif ?>
			</figure>
		<?php endif; ?>

		<div class="portfolio-info">
			
			<?php 

				if( ! empty( $cats ) ) {
					?>
					<div class="wrap-meta">
						<ul class="proj-cats-list">
						<?php
						foreach ($cats as $key => $cat) {
							$classes[] = 'proj-cat-' . $cat->slug;
							// get_term_link( $cat, 'project-cat' ); 
							?>
								<li><?php echo esc_html($cat->name); ?></li>
							<?php
						}
						?>
						</ul>
					</div>
					<?php
				}

			 ?>
			 
			<div class="wrap-title">
				<h3 class="entry-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark"><?php the_title(); ?></a>
				</h3>
			</div>
		 </div>
	 </header>

</article><!-- #post -->
