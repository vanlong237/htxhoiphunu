<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );}

/**
 * Generate css file
 */

class WOODMART_Cssgenerator {

	private $_generator_url = 'https://woodmart.xtemos.com/';

	private $_options = array();

	function __construct() {
		$this->_notices = WOODMART_Registry()->notices;

		// add_action('init', array($this, 'init'));
		$this->_options = woodmart_get_config( 'css-parts' );

	}

	private function _get_options_from_section( $section ) {
		return array_filter(
			$this->_options,
			function( $el ) use ( $section ) {
				return $el['section'] == $section;
			}
		);
	}

	public function init() {

	}

	private function _checked( $opt ) {

		$checked = $opt['checked'];

		if ( isset( $_POST['generate-css'] ) ) {
			$checked = isset( $_POST[ $opt['id'] ] ) && $_POST[ $opt['id'] ];
		}

		$css_data = $this->_get_data();

		if ( ! empty( $css_data ) && is_array( $css_data ) ) {
			$checked = isset( $css_data[ $opt['id'] ] ) && $css_data[ $opt['id'] ];
		}

		checked( $checked );
	}

	private function _render_option( $opt ) {
		?>
			<div class="css-checkbox <?php echo esc_attr( $opt['id'] ); ?>" data-parent="<?php echo ( isset( $opt['parent'] ) ) ? $opt['parent'] : 'none'; ?>">
				<input type="checkbox" id="<?php echo esc_attr( $opt['id'] ); ?>" name="<?php echo esc_attr( $opt['id'] ); ?>" <?php $this->_checked( $opt ); ?> <?php disabled( isset( $opt['disabled'] ) && $opt['disabled'], true ); ?> value="true">
				<label for="<?php echo esc_attr( $opt['id'] ); ?>"><?php echo esc_html( $opt['title'] ); ?>
				<?php if ( isset( $opt['image'] ) || isset( $opt['description'] ) ) : ?>
					<div class="css-tooltip">
						<?php if ( isset( $opt['image'] ) ) : ?>
							<div class="css-tooltip-image">
								<img src="<?php echo esc_attr( $opt['image'] ); ?>">
							</div>
						<?php endif ?>
						<?php if ( isset( $opt['description'] ) ) : ?>
							<p class="css-description"><?php echo esc_html( $opt['description'] ); ?></p>
						<?php endif ?>
					</div>
				<?php endif; ?>
				</label>
				<?php $this->_render_children( $opt['id'] ); ?>
			</div>
		<?php
	}

	private function _render_children( $id ) {
		$children = $this->_get_children( $id );

		if ( empty( $children ) ) {
			return;
		}

		echo '<div class="css-checkbox-children">';

		foreach ( $children as $id => $option ) {
			if ( 'wc-product' === $id ) {
				echo '<h4>WooCommerce extra features</h4>';
			}
			$this->_render_option( $option );
		}

		echo '</div>';
	}

	private function _get_children( $id ) {
		return array_filter(
			$this->_options,
			function( $el ) use ( $id ) {
				return isset( $el['parent'] ) && $el['parent'] == $id;
			}
		);
	}

	private function _render_section( $name ) {
		foreach ( $this->_get_options_from_section( $name ) as $id => $option ) {
			if ( ! isset( $option['parent'] ) ) {
				$this->_render_option( $option );
			}
		}
	}

	public function form() {
		$this->init();
		$this->process_form();
		$this->_notices->show_msgs();

		$file  = get_option( 'woodmart-generated-css-file' );
		$theme = wp_get_theme( get_template() );
		?>

		<div class="xtemos-loader-wrapper">
			<div class="xtemos-loader">
				<div class="xtemos-loader-el"></div>
				<div class="xtemos-loader-el"></div>
			</div>
			<p>Generating... It may take a few minutes...</p>
		</div>

		<h3><?php esc_html_e( 'Custom CSS file generator', 'woodmart' ); ?></h3>
		<p>
			This interface gives you an ability to reduce CSS file size by reducing amount of code
			loaded on each page. Just untick all features, options and plugins that you will not use
			and this CSS code will not be loaded. You can make your CSS file up to 90% smaller and it
			depends on a number of options and features you use.
		</p>

		<form action="" method="post" class="woodmart-form woodmart-generator-form">
			<div class="woodmart-row woodmart-four-columns">
				<div class="woodmart-column">
					<div class="woodmart-column-inner">
						<div class="css-options-box woodmart-wp-options">
							<h4>WordPress styles</h4>

							<?php $this->_render_section( 'Wordpress' ); ?>
						</div>
						<div class="css-options-box">
							<h4>Portfolio</h4>

							<?php $this->_render_section( 'Portfolio' ); ?>
						</div>
						<div class="css-options-box">
							<h4>Additional plugins</h4>

							<?php $this->_render_section( 'Additional plugins' ); ?>
						</div>
						<div class="css-options-box">
							<h4>Extra features</h4>

							<?php $this->_render_section( 'Extra features' ); ?>
						</div>
					</div>
				</div>
				<div class="woodmart-column woomart-woo-column">
					<div class="woodmart-column-inner">
						<div class="css-options-box">
							<h4>WooCommerce styles</h4>

							<?php $this->_render_section( 'WooCommerce styles' ); ?>

						</div>
					</div>
				</div>
				<div class="woodmart-column">
					<div class="woodmart-column-inner css-tooltip-right">
						<div class="css-options-box woodmart-wpb-options">
							<h4>Elements</h4>

							<?php $this->_render_section( 'WPBakery elements' ); ?>
						</div>
						<div class="css-options-box">
							<h4>Extra configuration</h4>

							<?php $this->_render_section( 'Extra configuration' ); ?>
						</div>
					</div>
				</div>
			</div>
			
			<input type="hidden" name="css-data">

			<input class="button-primary" name="generate-css" type="submit" value="<?php esc_attr_e( 'Generate file', 'woodmart' ); ?>" />

		</form>

		<?php if ( isset( $file['name'] ) ) : ?>
			<?php
			$uploads   = wp_upload_dir();
			$file_path = $uploads['path'] . '/' . $file['name'];
			$file_url  = $uploads['url'] . '/' . $file['name'];
			
			$data      = file_exists( $file_path ) ? get_file_data( $file_path, array( 'Version' => 'Version' ) ) : array();
			$version   = isset( $data['Version'] ) ? $data['Version'] : 'unknown';
			?>

			<div class="css-file-information">

				<h3>Custom CSS file is <span>generated</span></h3>

				<table>
					<tr>
						<th>
							File:
						</th>
						<td>
							<a href="<?php echo esc_url( $file_url ); ?>" target="_blank"><?php echo esc_html( $file['name'] ); ?></a>
						</td>
					</tr>
					<tr>
						<th>
							CSS Version:
						</th>
						<td>
							<strong><?php echo $version; ?></strong>
						</td>
					</tr>
					<tr>
						<th>
							Theme version:
						</th>
						<td>
							<strong><?php echo esc_html( $theme->get( 'Version' ) ); ?></strong>
						</td>
					</tr>
				</table>
				
				<div class="css-file-actions">
					<?php if ( version_compare( $version, $theme->get( 'Version' ), '<' ) ) : ?>
						<input class="button-primary css-update-button" name="deactivate-css" type="submit" value="<?php esc_attr_e( 'Update', 'woodmart' ); ?>" />
					<?php endif ?>
					<a href="<?php echo admin_url( 'admin.php?page=woodmart_dashboard&tab=css&deactivate-css=1' ); ?>" class="button-primary" name="deactivate-css" type="submit">
						<?php esc_attr_e( 'Delete', 'woodmart' ); ?>
					</a>
				</div>
			</div>

		<?php endif; ?>

		<?php
	}

	public function process_form() {
		$uploads = wp_upload_dir();

		if ( isset( $_GET['deactivate-css'] ) ) {
			$file = get_option( 'woodmart-generated-css-file' );

			if ( isset( $file['name'] ) ) {
				$file_path = $uploads['path'] . '/' . $file['name'];
				unlink( $file_path );
			}

			delete_option( 'woodmart-generated-css-file' );
			delete_option( 'woodmart-css-data' );

		}

		if ( ! isset( $_POST['generate-css'] ) || empty( $_POST['generate-css'] ) ) {
			return;
		}

		$data         = $_POST['css-data'];
		$old_browsers = apply_filters( 'woodmart_css_generator_vendors_prefixes', false ) ? 'true' : 'false';

		if ( function_exists( 'woodmart_decompress' ) && ! $json = woodmart_decompress( $data ) ) {
			$this->_notices->add_warning( 'Wrong data sent. Try to resend the form.' );
			return;
		}

		if ( ! $css_data = json_decode( $json, true ) ) {
			$this->_notices->add_warning( 'Wrong JSON data format. Try to resend the form.' );
			return;
		}

		/* you can safely run request_filesystem_credentials() without any issues and don't need to worry about passing in a URL */
		$creds = request_filesystem_credentials( false, '', false, false, array_keys( $_POST ) );

		if ( ! $creds ) {
			return;
		}

		/* initialize the API */
		if ( ! WP_Filesystem( $creds ) ) {
			/* any problems and we exit */
			$this->_notices->add_warning( 'Can\'t access your file system. The FTP access is wrong.' );
			return false;
		}

		global $wp_filesystem;

		$response = wp_remote_get( $this->_generator_url . '?generate_css=' . $data . '&old_browsers=' . $old_browsers . '&builder=' . woodmart_get_opt( 'page_builder', 'wpb' ) . '&theme_url=' . urlencode( get_template_directory_uri() ) . '/', array( 'timeout' => 30 ) );

		if ( isset( $_GET['xtemos_debug'] ) ) {
			ar( $response );
		}

		if ( ! is_array( $response ) ) {
			$this->_notices->add_warning( 'Can\'t call xtemos server to generate the file.' );
			return false;
		}

		$header = $response['headers']; // array of http header lines
		$css    = $response['body']; // use the content

		$t         = time();
		$file_name = 'style-' . $t . '.css';

		$file_path = $uploads['path'] . '/' . $file_name;

		$res = $wp_filesystem->put_contents(
			$file_path,
			$css
		);

		if ( $res ) {

			$upload = array(
				'name' => $file_name,
			);

			$file = get_option( 'woodmart-generated-css-file' );

			if ( isset( $file['name'] ) ) {
				$file_path = $uploads['path'] . '/' . $file['name'];
				$wp_filesystem->delete( $file_path );
			}

			update_option( 'woodmart-generated-css-file', $upload );
			update_option( 'woodmart-css-data', $css_data );

			$this->_notices->add_success( 'New CSS file is generated and saved.' );

		} else {
			$this->_notices->add_warning( 'Can\'t move file to uploads folder with wp_filesystem class.' );
			return false;
		}

	}

	private function _get_data() {
		$css_data = get_option( 'woodmart-css-data', array() );

		return $css_data;
	}

}
