<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Options;

/**
 * Maintenance.
 */
Options::add_section(
	array(
		'id'       => 'import_export',
		'name'     => esc_html__( 'Import / Export', 'woodmart' ),
		'priority' => 170,
		'icon'     => 'dashicons dashicons-image-rotate',
	)
);

Options::add_field(
	array(
		'id'       => 'import_export',
		'name'     => esc_html__( 'Import/export', 'woodmart' ),
		'type'     => 'import',
		'section'  => 'import_export',
		'priority' => 10,
	)
);
