<?php
if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * ------------------------------------------------------------------------------------------------
 * Size guide shortcode
 * ------------------------------------------------------------------------------------------------
 */
if ( ! function_exists( 'woodmart_size_guide_shortcode' ) ) {
	function woodmart_size_guide_shortcode( $element_args ) {
		$default_args = array(
			'id'          => '',
			'el_class'    => '',
			'css'         => '',
			'title'       => 1,
			'description' => 1,
		);

		$element_args = wp_parse_args( $element_args, $default_args );

		$wrapper_classes  = '';
		$wrapper_classes .= ' ' . $element_args['el_class'];

		if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
			$wrapper_classes .= ' ' . vc_shortcode_custom_css_class( $element_args['css'] );
		}

		if ( ! $element_args['id'] ) {
			return '';
		}

		$sguide_post = get_post( $element_args['id'] );
		$size_tables = get_post_meta( $element_args['id'], 'woodmart_sguide' );

		ob_start();
		?>
		<div class="woodmart-sizeguide<?php echo esc_attr( $wrapper_classes ); ?>">
			<?php if ( $sguide_post->post_title && $element_args['title'] ) : ?>
				<h4 class="woodmart-sizeguide-title">
					<?php echo esc_html( $sguide_post->post_title ); ?>
				</h4>
			<?php endif; ?>

			<?php if ( $sguide_post->post_content && $element_args['description'] ) : ?>
				<div class="woodmart-sizeguide-content">
					<?php echo do_shortcode( $sguide_post->post_content ); ?>
				</div>
			<?php endif; ?>

			<div class="responsive-table">
				<table class="woodmart-sizeguide-table">
					<?php foreach ( $size_tables as $table ) : ?>
						<?php foreach ( $table as $row ) : ?>
							<tr>
								<?php foreach ( $row as $col ) : ?>
									<td><?php echo esc_html( $col ); ?></td>
								<?php endforeach; ?>
							</tr>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</table>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}

if ( ! function_exists( 'woodmart_get_size_guides_array' ) ) {
	function woodmart_get_size_guides_array() {
		$output = array(
			esc_html__( 'Select', 'woodmart' ) => '',
		);
		$posts  = get_posts(
			array(
				'posts_per_page' => 200,
				'post_type'      => 'woodmart_size_guide',
			)
		);

		foreach ( $posts as $post ) {
			$output[ $post->post_title ] = $post->ID;
		}

		return $output;
	}
}
