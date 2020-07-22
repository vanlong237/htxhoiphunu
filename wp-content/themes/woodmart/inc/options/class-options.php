<?php
/**
 * Static singleton class for options functions.
 *
 * @package xts
 */

namespace XTS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Presets;

use XTS\Singleton;

/**
 * Create options, register sections and fields.
 */
class Options extends Singleton {

	/**
	 * Options values array from the database.
	 *
	 * @var array
	 */
	private static $_options;

	/**
	 * Array of the options default values.
	 *
	 * @var array
	 */
	private static $_default_options = array();

	/**
	 * Fields objects array.
	 *
	 * @var array
	 */
	private static $_fields = array();

	/**
	 * Sections array.
	 *
	 * @var array
	 */
	private static $_sections = array();

	/**
	 * Key for options to be overriden from meta values.
	 *
	 * @var array
	 */
	private static $_override_options = array();

	/**
	 * Options set prefix.
	 *
	 * @var array
	 */
	public static $opt_name = 'woodmart';

	/**
	 * Presets IDs.
	 *
	 * @var array
	 */
	private $_presets = false;

	/**
	 * Array of field type and controls mapping.
	 *
	 * @var array
	 */
	private static $_controls_classes = array(
		'select'           => 'XTS\Options\Controls\Select',
		'text_input'       => 'XTS\Options\Controls\Text_Input',
		'switcher'         => 'XTS\Options\Controls\Switcher',
		'color'            => 'XTS\Options\Controls\Color',
		'checkbox'         => 'XTS\Options\Controls\Checkbox',
		'buttons'          => 'XTS\Options\Controls\Buttons',
		'upload'           => 'XTS\Options\Controls\Upload',
		'background'       => 'XTS\Options\Controls\Background',
		'textarea'         => 'XTS\Options\Controls\Textarea',
		'image_dimensions' => 'XTS\Options\Controls\Image_Dimensions',
		'typography'       => 'XTS\Options\Controls\Typography',
		'custom_fonts'     => 'XTS\Options\Controls\Custom_Fonts',
		'range'            => 'XTS\Options\Controls\Range',
		'editor'           => 'XTS\Options\Controls\Editor',
		'import'           => 'XTS\Options\Controls\Import',
		'notice'           => 'XTS\Options\Controls\Notice',
		'instagram_api'    => 'XTS\Options\Controls\Instagram_Api',
	);

	/**
	 * Register hooks and load base data.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'admin_init', array( $this, 'register_settings' ), 2 );
		add_action( 'wp', array( $this, 'load_defaults' ), 2 );
		// add_action( 'wp', array( $this, 'load_presets' ), 3 );
		add_action( 'wp', array( $this, 'override_options_from_meta' ), 4 );
		add_action( 'wp', array( $this, 'setup_globals' ), 5 );

		$this->load_options();
		// $this->load_presets();
		$this->setup_globals();

	}

	/**
	 * Load options array from the database options table.
	 *
	 * @since 1.0.0
	 */
	public function load_options() {
		self::$_options = get_option( 'xts-' . self::$opt_name . '-options' );
	}

	/**
	 * Get active presets and load their options.
	 *
	 * @since 1.0.0
	 */
	public function load_presets() {
		if ( is_admin() ) {
			return;
		}

		$presets = Presets::get_active_presets();

		$this->_presets = ! empty( $presets ) ? $presets : false;

		if ( ! $this->_presets ) {
			return;
		}

		foreach ( $this->_presets as $preset_id ) {
			if ( isset( self::$_options[ $preset_id ] ) && is_array( self::$_options[ $preset_id ] ) ) {
				foreach ( self::$_options[ $preset_id ] as $key => $value ) {
					self::$_options[ $key ] = $value;
				}
			}
		}
	}

	/**
	 * Load default options from Field objects arguments.
	 *
	 * @since 1.0.0
	 */
	public function load_defaults() {
		foreach ( self::$_fields as $field ) {
			if ( ! isset( $field->args['default'] ) ) {
				continue;
			}

			if ( ! isset( self::$_options[ $field->get_id() ] ) ) {
				self::$_options[ $field->get_id() ] = $field->args['default'];
			}
		}
	}

	/**
	 * Get options array.
	 *
	 * @return array Options array stored in the database.
	 *
	 * @since 1.0.0
	 */
	public static function get_options() {
		return self::$_options;
	}

	/**
	 * Setup global variable for this options set.
	 *
	 * @since 1.0.0
	 */
	public function setup_globals() {
		$GLOBALS[ 'xts_' . self::$opt_name . '_options' ] = $this::$_options;
	}

	/**
	 * Register settings hook callback.
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {
		register_setting( 'xts-options-group', 'xts-' . self::$opt_name . '-options', array( 'sanitize_callback' => array( $this, 'sanitize_before_save' ) ) );
	}

	/**
	 * Static method to add a section to the array.
	 *
	 * @since 1.0.0
	 *
	 * @param array $section Arguments array for new section.
	 */
	public static function add_section( $section ) {
		self::$_sections[ $section['id'] ] = $section;
	}

	/**
	 * Static method t add a field object based on arguments array.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args New field object arguments.
	 */
	public static function add_field( $args ) {
		$control_classname = self::$_controls_classes[ $args['type'] ];

		if ( ! isset( self::$_options[ $args['id'] ] ) ) {
			self::$_options[ $args['id'] ] = self::get_default( $args );
		}

		$field = new $control_classname( $args, self::$_options );

		self::$_fields[ $args['id'] ] = $field;

		// Set default value.
		foreach ( self::$_fields as $field ) {
			if ( ! isset( self::$_options[ $field->get_id() ] ) ) {
				self::$_options[ $field->get_id() ] = self::get_default( $field->args );
			}
		}
	}

	/**
	 * Get field default value.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Args array for the field.
	 * @return string Field value.
	 */
	public static function get_default( $args ) {
		$value = '';

		if ( isset( $args['default'] ) ) {
			$value = $args['default'];
		} elseif ( ! isset( $args['default'] ) && xts_get_default_value( $args['id'] ) ) {
			$value = xts_get_default_value( $args['id'] );
		}

		return $value;
	}

	/**
	 * Add option key to be overriden from meta value.
	 *
	 * @since 1.0.0
	 *
	 * @param array $key Key for the option to override.
	 */
	public static function register_meta_override( $key ) {
		self::$_override_options[] = $key;
	}

	/**
	 * Override options from meta values.
	 *
	 * @since 1.0.0
	 */
	public static function override_options_from_meta() {
		// foreach ( self::$_override_options as $key ) {
		// 	$meta = get_post_meta( xts_get_page_id(), '_xts_' . $key, true );
		// 	if ( isset( self::$_options[ $key ] ) && ! empty( $meta ) && 'inherit' !== $meta ) {
		// 		self::$_options[ $key ] = $meta;
		// 	}
		// }
	}

	/**
	 * Static method to get all fields objects.
	 *
	 * @since 1.0.0
	 *
	 * @return array Field objects array.
	 */
	public static function get_fields() {
		$fields = self::$_fields;

		usort(
			$fields,
			function ( $item1, $item2 ) {

				if ( ! isset( $item1->args['priority'] ) ) {
					return 1;
				}

				if ( ! isset( $item2->args['priority'] ) ) {
					return -1;
				}

				return $item1->args['priority'] - $item2->args['priority'];
			}
		);

		return $fields;

	}

	/**
	 * Static method to get all sections.
	 *
	 * @since 1.0.0
	 *
	 * @return array Section array.
	 */
	public static function get_sections() {
		$sections = self::$_sections;

		usort(
			$sections,
			function ( $item1, $item2 ) {

				if ( ! isset( $item1['priority'] ) ) {
					return 1;
				}

				if ( ! isset( $item2['priority'] ) ) {
					return -1;
				}

				return $item1['priority'] - $item2['priority'];
			}
		);

		$sections_assoc = array();

		foreach ( $sections as $key => $section ) {
			$sections_assoc[ $section['id'] ] = $section;
		}

		return $sections_assoc;
	}

	/**
	 * Get fields CSS code based on its controls and values.
	 *
	 * @since 1.0.0
	 */
	public function get_css_output() {
		$output = '';

		foreach ( self::$_fields as $key => $field ) {
			$field->set_presets( $this->_presets );
			$output .= $field->css_output();
		}

		return $output;
	}

	/**
	 * Update all fields options array.
	 *
	 * @since 1.0.0
	 *
	 * @param array $options New options.
	 */
	public function override_current_options( $options ) {
		foreach ( self::$_fields as $key => $field ) {
			$field->override_options( $options );
		}
	}

	/**
	 * Update options in the database with a new array. It should be sanitized
	 * with ->sanitize_before_save() method of the class.
	 *
	 * @since 1.0.0
	 *
	 * @param array $new_options New options.
	 */
	public function update_options( $new_options ) {
			update_option( 'xts-woodmart-options', $new_options );
	}

	/**
	 * Sanitize all options before saving callback. Used also for import and reset default actions.
	 *
	 * @since 1.0.0
	 *
	 * @param array $options Options from POST.
	 */
	public function sanitize_before_save( $options ) {

		$sanitized_options = array();
		$imported_options  = array();
		$reset             = false;

		$sanitized_options['last_message'] = 'save';

		$default_options = get_option( 'xts-' . self::$opt_name . '-options' );

		$presets = Presets::get_all();

		// If we are working with preset.
		if ( isset( $options['preset'] ) && $options['preset'] ) {
			$preset_id      = $options['preset'];
			$fields_to_save = explode( ',', $options['fields_to_save'] );

			// Take default options previously stored in the database.
			$options_to_save               = $default_options;
			$options_to_save[ $preset_id ] = array();

			// Create a subarray with preset options. Everything else leave unchanged.
			if ( strlen( $options['fields_to_save'] ) > 0 && is_array( $fields_to_save ) && count( $fields_to_save ) > 0 ) {
				foreach ( $fields_to_save as $option ) {
					$options_to_save[ $preset_id ][ $option ] = $options[ $option ];
				}
				$options_to_save[ $preset_id ]['fields_to_save'] = $options['fields_to_save'];

			}

			$options = $options_to_save;
		}

		// If we submit the form with "import" button then import options from the field.
		if ( isset( $options['import-btn'] ) && $options['import-btn'] ) {
			$import_json = $options['import_export'];

			$imported_options = json_decode( $import_json, true );

			$sanitized_options['last_message'] = 'import';
		}

		// If reset defaults button is pushed.
		if ( isset( $options['reset-defaults'] ) && $options['reset-defaults'] ) {
			$sanitized_options['last_message'] = 'reset';
			$reset                             = true;
		}

		foreach ( self::$_fields as $key => $field ) {
			if ( isset( $imported_options[ $field->get_id() ] ) ) {
				$sanitized_options[ $field->get_id() ] = $field->sanitize( $imported_options[ $field->get_id() ] );
			} elseif ( ! isset( $imported_options[ $field->get_id() ] ) && isset( $options['import-btn'] ) && $options['import-btn'] && isset( $default_options[ $field->get_id() ] ) ) {
				$sanitized_options[ $field->get_id() ] = $default_options[ $field->get_id() ];
			} elseif ( $reset ) {
				$sanitized_options[ $field->get_id() ] = self::get_default( $field->args );
			} else {
  				$sanitized_options[ $field->get_id() ] = isset( $options[ $field->get_id() ] ) ? $field->sanitize( $options[ $field->get_id() ] ) : self::get_default( $field->args );
			}
		}

		$sanitized_options['last_tab'] = isset( $options['last_tab'] ) ? $options['last_tab'] : '';

		unset( $sanitized_options['import_export'] );

		foreach ( $presets as $id => $preset ) {
			$sanitized_options[ $id ] = isset( $options[ $id ] ) ? $options[ $id ] : $default_options[ $id ];
		}

		return $sanitized_options;
	}
}

Options::get_instance();
