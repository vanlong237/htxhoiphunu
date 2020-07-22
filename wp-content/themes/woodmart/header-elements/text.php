<?php

$classes = $params['inline'] ? ' text-element-inline' : '';

?>

<div class="whb-text-element reset-mb-10 <?php echo esc_attr( $params['css_class'] ); ?><?php echo esc_attr( $classes ); ?>"><?php echo do_shortcode($params['content']); ?></div>
