<?php

$extra_class = '';
$icon_type = $params['icon_type'];

$extra_class .= ' wd-style-' . $params['style'];

if ( $icon_type == 'custom' ) {
	$extra_class .= ' wd-tools-custom-icon';
}

?>
<div class="woodmart-burger-icon wd-tools-element mobile-nav-icon whb-mobile-nav-icon<?php echo esc_attr( $extra_class ); ?>">
	<a href="#">
		<?php if ( $icon_type == 'custom' ): ?>
			<span class="woodmart-custom-burger-icon wd-tools-icon"><?php echo whb_get_custom_icon( $params['custom_icon'] ); ?></span>
		<?php else: ?>
			<span class="woodmart-burger wd-tools-icon"></span>
		<?php endif; ?>
		<span class="woodmart-burger-label wd-tools-text"><?php esc_html_e( 'Menu', 'woodmart' ); ?></span>
	</a>
</div><!--END MOBILE-NAV-ICON-->