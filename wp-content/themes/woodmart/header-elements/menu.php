<?php
$menu_style = ( $params['menu_style'] ) ? $params['menu_style'] : 'default';
$location = 'main-menu';
$classes = 'menu-' . $params['menu_align'];
$classes .= ' navigation-style-' . $menu_style;
?>
<div class="whb-navigation whb-secondary-menu site-navigation woodmart-navigation <?php echo esc_attr( $classes ); ?>" role="navigation">
	<?php
		if( wp_get_nav_menu_object( $params['menu_id'] ) ) {
			wp_nav_menu(
				array(
					'menu' => $params['menu_id'],
					'menu_class' => 'menu',
					'walker' => new WOODMART_Mega_Menu_Walker()
				)
			);
		}
	 ?>
</div><!--END MAIN-NAV-->
