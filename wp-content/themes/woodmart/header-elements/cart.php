<?php
$extra_class = $custom_icon = $custom_icon_width = $custom_icon_height = '';
$icon_type = $params['icon_type'];
$cart_position = $params['position'];

$extra_class .= ' woodmart-cart-design-'. $params['style']; 

if ( $icon_type == 'bag' ) {
	$extra_class .= ' woodmart-cart-alt';
}

if ( $icon_type == 'custom' ) {
	$extra_class .= ' wd-tools-custom-icon';
}

if ( $cart_position == 'side' ) {
	$extra_class .= ' cart-widget-opener';
}


if ( ! woodmart_woocommerce_installed() || $params['style'] == 'disable' || ( ! is_user_logged_in() && woodmart_get_opt( 'login_prices' ) ) ) return; ?>

<div class="woodmart-shopping-cart wd-tools-element<?php echo esc_attr( $extra_class ); ?>" title="<?php echo esc_attr__( 'Shopping cart', 'woodmart' ); ?>">
	<a href="<?php echo esc_url( wc_get_cart_url() ); ?>">
		<span class="woodmart-cart-icon wd-tools-icon">
			<?php
				if ( $icon_type == 'custom' ) {
					echo whb_get_custom_icon( $params['custom_icon'] );
				}
			?>
			
			<?php if ( '2' == $params['style'] || '5' == $params['style'] ) : ?>
				<?php woodmart_cart_count(); ?>
			<?php endif; ?>
		</span>
		<span class="woodmart-cart-totals wd-tools-text">
			<?php if ( '2' != $params['style'] && '5' != $params['style'] ) : ?>
				<?php woodmart_cart_count(); ?>
			<?php endif; ?>

			<span class="subtotal-divider">/</span>
			<?php woodmart_cart_subtotal(); ?>
		</span>
	</a>
	<?php if ( $cart_position != 'side' && $cart_position != 'without' ): ?>
		<div class="dropdown-cart">
			<?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
		</div>
	<?php endif; ?>
</div>
