<?php
	if ( ! woodmart_woocommerce_installed() || ! woodmart_get_opt( 'wishlist' ) || ( woodmart_get_opt( 'wishlist_logged' ) && ! is_user_logged_in() ) ) return;

	$extra_class = '';
	$icon_type = $params['icon_type'];

	$extra_class .= ' wd-style-' . $params['design'];

	if ( $params['hide_product_count'] ) {
		$extra_class .= ' without-product-count';
	} else {
		$extra_class .= ' with-product-count';
	}

	if ( $icon_type == 'custom' ) {
		$extra_class .= ' wd-tools-custom-icon';
	}
?>

<div class="woodmart-wishlist-info-widget wd-tools-element<?php echo esc_attr( $extra_class ); ?>" title="<?php echo esc_attr__( 'My Wishlist', 'woodmart' ); ?>">
	<a href="<?php echo esc_url( woodmart_get_whishlist_page_url() ); ?>">
		<span class="wishlist-icon wd-tools-icon">
			<?php 
				if ( $icon_type == 'custom' ) {
					echo whb_get_custom_icon( $params['custom_icon'] );
				}
			?>

			<?php if ( ! $params['hide_product_count'] ): ?>
				<span class="wishlist-count wd-tools-count">
					<?php echo esc_html( woodmart_get_wishlist_count() ); ?>
				</span>
			<?php endif; ?>
		</span>
		<span class="wishlist-label wd-tools-text">
			<?php esc_html_e( 'Wishlist', 'woodmart' ) ?>
		</span>
	</a>
</div>
