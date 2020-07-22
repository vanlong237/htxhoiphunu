<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Options;

/**
 * Performance.
 */
Options::add_section(
	array(
		'id'       => 'performance',
		'name'     => esc_html__( 'Performance', 'woodmart' ),
		'priority' => 150,
		'icon'     => 'dashicons dashicons-performance',
	)
);

Options::add_field(
	array(
		'id'          => 'minified_css',
		'name'        => esc_html__( 'Include minified CSS', 'woodmart' ),
		'description' => esc_html__( 'Minified version of style.css file will be loaded (style.min.css)', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'performance',
		'default'     => '1',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'minified_js',
		'name'        => esc_html__( 'Include minified JS', 'woodmart' ),
		'description' => esc_html__( 'Minified version of functions.js file will be loaded', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'performance',
		'default'     => '1',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'combined_js',
		'name'        => esc_html__( 'Combine JS files', 'woodmart' ),
		'description' => esc_html__( 'Combine all third party libraries and theme functions into one JS file (theme.min.js)', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'performance',
		'default'     => false,
		'priority'    => 30,
	)
);


/**
 * CSS.
 */
Options::add_section(
	array(
		'id'       => 'performance_css',
		'name'     => esc_html__( 'CSS', 'woodmart' ),
		'parent'   => 'performance',
		'priority' => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'google_font_display',
		'name'        => esc_html__( '"font-display" for Google Fonts', 'woodmart' ),
		'description' => 'You can specify "font-display" property for fonts loaded from Google. Read more information <a href="https://developers.google.com/web/updates/2016/02/font-display">here</a>',
		'type'        => 'select',
		'section'     => 'performance_css',
		'default'     => 'disable',
		'options'     => array(
			'disable'  => array(
				'name'  => esc_html__( 'Disable', 'woodmart' ),
				'value' => 'disable',
			),
			'block' => array(
				'name'  => esc_html__( 'Block', 'woodmart' ),
				'value' => 'block',
			),
			'swap' => array(
				'name'  => esc_html__( 'Swap', 'woodmart' ),
				'value' => 'swap',
			),
			'fallback' => array(
				'name'  => esc_html__( 'Fallback', 'woodmart' ),
				'value' => 'fallback',
			),
			'optional' => array(
				'name'  => esc_html__( 'Optional', 'woodmart' ),
				'value' => 'optional',
			),
		),
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'icons_font_display',
		'name'        => esc_html__( '"font-display" for icon fonts (not recommended)', 'woodmart' ),
		'description' => 'You can specify "font-display" property for fonts used for icons in our theme including Font Awesome. Read more information <a href="https://developers.google.com/web/updates/2016/02/font-display">here</a>',
		'type'        => 'select',
		'section'     => 'performance_css',
		'default'     => 'disable',
		'options'     => array(
			'disable'  => array(
				'name'  => esc_html__( 'Disable', 'woodmart' ),
				'value' => 'disable',
			),
			'block' => array(
				'name'  => esc_html__( 'Block', 'woodmart' ),
				'value' => 'block',
			),
			'swap' => array(
				'name'  => esc_html__( 'Swap', 'woodmart' ),
				'value' => 'swap',
			),
			'fallback' => array(
				'name'  => esc_html__( 'Fallback', 'woodmart' ),
				'value' => 'fallback',
			),
			'optional' => array(
				'name'  => esc_html__( 'Optional', 'woodmart' ),
				'value' => 'optional',
			),
		),
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'font_awesome_css',
		'name'        => esc_html__( 'Font Awesome library' ),
		'description' => esc_html__( 'You can choose to load Font Awesome 5 library on all pages or use our special light version with social icons only that are required for our theme.', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'performance_css',
		'options'     => array(
			'always' => array(
				'name'  => esc_html__( 'Always use', 'woodmart' ),
				'value' => 'always',
			),
			'not_use'   => array(
				'name'  => esc_html__( 'Don\'t use', 'woodmart' ),
				'value' => 'not_use',
			),
		),
		'default'     => 'not_use',
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'light_bootstrap_version',
		'name'        => esc_html__( 'Light bootstrap version', 'woodmart' ),
		'description' => esc_html__( 'Clean bootstrap CSS from code that are not used by the theme.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'performance_css',
		'default'     => false,
		'priority'    => 50,
	)
);

Options::add_field(
	array(
		'id'          => 'disable_gutenberg_css',
		'name'        => esc_html__( 'Disable Gutenberg styles', 'woodmart' ),
		'description' => esc_html__( 'If you are not using Gutenberg elements you will not need these files to be loaded.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'performance_css',
		'default'     => false,
		'priority'    => 60,
	)
);

/**
 * JS
 */
Options::add_section(
	array(
		'id'       => 'performance_js',
		'name'     => esc_html__( 'JS', 'woodmart' ),
		'parent'   => 'performance',
		'priority' => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'disable_nanoscroller',
		'name'        => esc_html__( 'Nanoscroller library', 'woodmart' ),
		'description' => esc_html__( 'This library adds nice style to elements with scroll bar like cart widget, filters widget, AJAX search results etc. In modern browsers we can style them without this JS libary so you can disable it.', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'performance_js',
		'default'     => 'enable',
		'options'     => array(
			'enable'  => array(
				'name'  => esc_html__( 'Enable', 'woodmart' ),
				'value' => 'enable',
			),
			'disable' => array(
				'name'  => esc_html__( 'Disable', 'woodmart' ),
				'value' => 'disable',
			),
			'webkit'  => array(
				'name'  => esc_html__( 'Enable for old browsers', 'woodmart' ),
				'value' => 'webkit',
			),
		),
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'disable_owl_mobile_devices',
		'name'        => esc_html__( 'Disable OWL Carousel script on mobile devices', 'woodmart' ),
		'description' => esc_html__( 'Using native browser\'s scrolling feature on mobile devices may improve your page loading and performance on some devices. Desktop will be handled with OWL Carousel JS library.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'performance_js',
		'default'     => false,
		'priority'    => 20,
	)
);

/**
 * Lazy loading
 */
Options::add_section(
	array(
		'id'       => 'performance_lazy_loading',
		'name'     => esc_html__( 'Lazy loading', 'woodmart' ),
		'parent'   => 'performance',
		'priority' => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'lazy_loading',
		'name'        => esc_html__( 'Lazy loading for images', 'woodmart' ),
		'description' => esc_html__( 'Enable this option to optimize your images loading on the website. They will be loaded only when user will scroll the page.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'performance_lazy_loading',
		'default'     => false,
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'lazy_loading_offset',
		'name'        => esc_html__( 'Offset', 'woodmart' ),
		'description' => esc_html__( 'Start load images X pixels before the page is scrolled to the item', 'woodmart' ),
		'type'        => 'range',
		'section'     => 'performance_lazy_loading',
		'default'     => 0,
		'min'         => 0,
		'step'        => 10,
		'max'         => 1000,
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'lazy_effect',
		'name'        => esc_html__( 'Appearance effect', 'woodmart' ),
		'description' => esc_html__( 'When enabled, your images will be replaced with their blurred small previews. And when the visitor will scroll the page to that image, it will be replaced with an original image.', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'performance_lazy_loading',
		'default'     => 'fade',
		'options'     => array(
			'fade' => array(
				'name'  => esc_html__( 'Fade', 'woodmart' ),
				'value' => 'fade',
			),
			'blur' => array(
				'name'  => esc_html__( 'Blur', 'woodmart' ),
				'value' => 'blur',
			),
			'none' => array(
				'name'  => esc_html__( 'None', 'woodmart' ),
				'value' => 'none',
			),
		),
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'lazy_generate_previews',
		'name'        => esc_html__( 'Generate previews', 'woodmart' ),
		'description' => esc_html__( 'Create placeholders previews as miniatures from the original images.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'performance_lazy_loading',
		'default'     => '1',
		'priority'    => 40,
	)
);

Options::add_field(
	array(
		'id'          => 'lazy_base_64',
		'name'        => esc_html__( 'Base 64 encode for placeholders', 'woodmart' ),
		'description' => esc_html__( 'This option allows you to decrease a number of HTTP requests replacing images with base 64 encoded sources.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'performance_lazy_loading',
		'default'     => '1',
		'priority'    => 50,
	)
);

Options::add_field(
	array(
		'id'          => 'lazy_proprtion_size',
		'name'        => esc_html__( 'Proportional placeholders size', 'woodmart' ),
		'description' => esc_html__( 'Will generate proportional image size for the placeholder based on original image size.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'performance_lazy_loading',
		'default'     => '1',
		'priority'    => 60,
	)
);

Options::add_field(
	array(
		'id'          => 'lazy_custom_placeholder',
		'name'        => esc_html__( 'Upload custom placeholder image', 'woodmart' ),
		'description' => esc_html__( 'Add your custom image placeholder that will be used before the original image will be loaded.', 'woodmart' ),
		'type'        => 'upload',
		'section'     => 'performance_lazy_loading',
		'priority'    => 70,
	)
);

/**
 * Preloader
 */
Options::add_section(
	array(
		'id'       => 'preloader_section',
		'name'     => esc_html__( 'Preloader', 'woodmart' ),
		'parent'   => 'performance',
		'priority' => 40,
	)
);

Options::add_field(
	array(
		'id'          => 'preloader',
		'name'        => esc_html__( 'Preloader (beta)', 'woodmart' ),
		'description' => esc_html__( 'Enable preloader animation while loading your website content. Useful when you move all the CSS to the footer.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'preloader_section',
		'default'     => false,
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'       => 'preloader_image',
		'name'     => esc_html__( 'Animated image', 'woodmart' ),
		'type'     => 'upload',
		'section'  => 'preloader_section',
		'priority' => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'preloader_background_color',
		'name'        => esc_html__( 'Background for loader screen', 'woodmart' ),
		'type'        => 'color',
		'default'     => array(
			'idle' => '#ffffff'
		),
		'section'     => 'preloader_section',
		'priority'    => 30,
	)
);