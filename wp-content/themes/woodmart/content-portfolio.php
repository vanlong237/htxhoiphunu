<?php
/**
 * The default template for displaying content
 */

global $woodmart_portfolio_loop;

$size = 'large';

$classes[] = 'portfolio-entry';

$columns = woodmart_loop_prop( 'portfolio_column' );
$style   = woodmart_loop_prop( 'portfolio_style' );

if( ! is_singular( 'portfolio' ) ) {
	$classes[] = woodmart_get_grid_el_class(0, $columns, false, 12);
	$classes[] = 'portfolio-single';
	$classes[] = 'masonry-item';
	woodmart_enqueue_script( 'isotope' );
	woodmart_enqueue_script( 'woodmart-packery-mode' );
	woodmart_enqueue_script( 'woodmart-photoswipe' );
}

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
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $classes ); ?>>
	<header class="entry-header">
		<?php if ( has_post_thumbnail() && ! post_password_required() && ! is_attachment()  && ! is_singular( 'portfolio' ) ) : ?>
			<a href="<?php echo esc_url( get_permalink() ); ?>" class="project-link"></a>
			<figure class="entry-thumbnail">
				<?php if ( ! is_singular( 'portfolio' ) ): ?>
						<a href="<?php echo esc_url( get_permalink() ); ?>" class="portfolio-thumbnail">
							<?php the_post_thumbnail( $size ); ?>
						</a>
					<?php else: ?>
						<?php the_post_thumbnail( $size ); ?>
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
			 
			<?php if ( is_singular( 'portfolio' ) ) : ?>
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<?php else : ?>
				<div class="wrap-title">
					<h3 class="entry-title">
						<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark"><?php the_title(); ?></a>
					</h3>
				</div>
			<?php endif; // is_single() ?>
		 </div>
		<a href="<?php echo esc_url( wp_get_attachment_url( get_post_thumbnail_id($post->ID) ) ); ?>" class="portfolio-enlarge" data-elementor-open-lightbox="no"><?php esc_html_e('View Large', 'woodmart'); ?></a>
		<?php if ( woodmart_is_social_link_enable( 'share' ) ): ?>
			<div class="social-icons-wrapper">
				<?php if( function_exists( 'woodmart_shortcode_social' ) ) echo woodmart_shortcode_social( array( 'size' => 'small', 'style' => 'default', 'color' => 'light' ) ); ?>
			</div>
		<?php endif ?>
	 </header>

	<?php if ( is_singular( 'portfolio' ) ) : ?>
		<div class="entry-content">
			<?php the_content(); ?>
		</div><!-- .entry-content -->
	<?php else : ?>
		<div class="entry-summary">
			<?php //the_excerpt(); ?>
		</div><!-- .entry-summary -->
	<?php endif; ?>

</article><!-- #post -->
