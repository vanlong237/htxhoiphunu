<?php

$extra_class = '';
$icon_type = $params['icon_type'];

if ( $icon_type == 'custom' ) {
	$extra_class .= ' wd-tools-custom-icon';
}

$settings = whb_get_settings();

if ( $params['display'] == 'form' ) {
	$search_style = isset( $params['search_style'] ) ? $params['search_style'] : 'default';
	woodmart_search_form( array(
		'ajax' => $settings['search']['ajax'],
		'post_type' => $settings['search']['post_type'],
		'icon_type' => $icon_type,
		'search_style' => $search_style,
		'custom_icon' => $params['custom_icon'],
		'el_classes' => 'woodmart-mobile-search-form',
	) );
	return;
}

?>

<div class="whb-search search-button wd-tools-element mobile-search-icon<?php echo esc_attr( $extra_class ); ?>">
	<a href="#">
		<span class="search-button-icon">
			<?php 
				if ( $icon_type == 'custom' ) {
					echo whb_get_custom_icon( $params['custom_icon'] );
				}
			?>
		</span>
	</a>
</div>
