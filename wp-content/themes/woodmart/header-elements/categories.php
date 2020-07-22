<?php
if ( $params['menu_id'] == '' ) {
	return;
}

$extra_class = '';
$opened      = get_post_meta( woodmart_get_the_ID(), '_woodmart_open_categories', true );
$icon_type   = $params['icon_type'];

if ( woodmart_woocommerce_installed() && is_product() ) {
	$opened = false;
}

$class = ( $params['color_scheme'] != 'inherit' ) ? 'color-scheme-' . $params['color_scheme'] : '';

if ( ! empty( $params['background'] ) && ! empty( $params['background']['background-color'] ) ) {
	$class .= ' has-bg';
}

$extra_class .= ( $opened ) ? ' opened-menu' : ' show-on-hover';

if ( $icon_type == 'custom' ) {
	$extra_class .= ' woodmart-cat-custom-icon';
}

$html = '';
if ( $params['more_cat_button'] ) {
	$extra_class .= ' wd-more-cat';
	$html        .= '<li class="menu-item item-level-0 wd-more-cat-btn"><a href="#" class="woodmart-nav-link"></a></li>';
}

$extra_class .= ' whb-' . $id;

?>

<div class="header-categories-nav<?php echo esc_attr( $extra_class ); ?>" role="navigation">
	<div class="header-categories-nav-wrap">
		<span class="menu-opener <?php echo esc_attr( $class ); ?>">
			<?php if ( $icon_type == 'custom' ) : ?>
				<span class="woodmart-custom-burger-icon"><?php echo whb_get_custom_icon( $params['custom_icon'] ); ?></span>
			<?php else : ?>
				<span class="woodmart-burger"></span>
			<?php endif; ?>

			<span class="menu-open-label">
				<?php esc_html_e( 'Browse Categories', 'woodmart' ); ?>
			</span>
			<span class="arrow-opener"></span>
		</span>
		<div class="categories-menu-dropdown vertical-navigation woodmart-navigation">
			<?php
			wp_nav_menu(
				array(
					'menu'       => $params['menu_id'],
					'menu_class' => 'menu wd-cat-nav',
					'walker'     => new WOODMART_Mega_Menu_Walker(),
					'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s' . $html . '</ul>',
				)
			);
			?>
		</div>
	</div>
</div>
