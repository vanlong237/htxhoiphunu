<?php
	$class = ( $params['full_height'] ) ? 'whb-divider-stretch' : 'whb-divider-default';
	$class .= ' ' . $params['css_class'];
 ?>
<div class="whb-divider-element <?php echo esc_attr( $class ); ?>"></div>