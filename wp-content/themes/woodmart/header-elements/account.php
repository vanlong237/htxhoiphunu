<?php
if( ! woodmart_woocommerce_installed() ) return '';

$links = woodmart_get_header_links( $params );
$my_account_style = $params['display'];
$login_side = $params['form_display'] == 'side';
$icon_type = $params['icon_type'];
$extra_class = '';

$classes = '';
$classes .= ( ! empty( $link['dropdown'] ) ) ? ' menu-item-has-children' : '';
$classes .= ( $params['with_username'] ) ? ' my-account-with-username' : '';
$classes .= ( $my_account_style ) ? ' my-account-with-' . $my_account_style : '';
$classes .= ( ! is_user_logged_in() && $params['login_dropdown'] && $login_side ) ? ' login-side-opener' : '';

if ( $icon_type == 'custom' && $my_account_style == 'icon' ) {
	$classes .= ' wd-tools-custom-icon';
}

if( empty( $links ) ) return '';

?>
<div class="woodmart-header-links woodmart-navigation menu-simple-dropdown wd-tools-element item-event-hover <?php echo esc_attr( $classes ); ?>"  title="<?php echo esc_attr__( 'My account', 'woodmart' ); ?>">
	<?php foreach ($links as $key => $link): ?>
		<a href="<?php echo esc_url( $link['url'] ); ?>">
			<span class="wd-tools-icon">
				<?php
				if ( $icon_type == 'custom' && $my_account_style == 'icon' ) {
					echo whb_get_custom_icon( $params['custom_icon'] );
				}
				?>
			</span>
			<span class="wd-tools-text">
				<?php echo wp_kses( $link['label'], 'default' ); ?>
			</span>
		</a>
		
		<?php if( ! empty( $link['dropdown'] ) ) echo apply_filters( 'woodmart_account_element_dropdown', $link['dropdown'] ); ?>
	<?php endforeach; ?>
</div>
