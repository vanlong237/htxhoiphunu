<?php
/**
 * The template for displaying slide.
 *
 * @package xts
 */

get_header();

global $post;

$slider_term    = wp_get_post_terms( $post->ID, 'woodmart_slider' );
$slider_id      = $slider_term ? $slider_term[0]->term_id : '';
$carousel_id    = 'slider-' . $slider_id;

?>
<div class="container">
	<div class="row">
		<div class="site-content col-lg-12 col-12 col-md-12">
			<?php woodmart_get_slider_css( $slider_id, $carousel_id, array( $post ) ); ?>
			<div id="<?php echo esc_attr( $carousel_id ); ?>" class="woodmart-slider-wrapper<?php echo woodmart_get_slider_class( $slider_id ); ?>">
				<div class="woodmart-slider">
					<?php
					$slide_id        = 'slide-' . $post->ID;
					$slide_animation = get_post_meta( $post->ID, 'slide_animation', true );
					?>
					
					<div id="<?php echo esc_attr( $slide_id ); ?>" class="woodmart-slide woodmart-loaded active<?php echo woodmart_get_slide_class( $post->ID ); ?>">
						<div class="container woodmart-slide-container">
							<div class="woodmart-slide-inner <?php echo ( ! empty( $slide_animation ) && $slide_animation != 'none' ) ? 'slide-animation anim-' . esc_attr( $slide_animation ) : ''; ?>">
								<?php while ( have_posts() ) : ?>
									<?php the_post(); ?>
									<?php the_content(); ?>
								<?php endwhile; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>
