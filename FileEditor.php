<?php
/**
 * Plugin Name:       Aide - Config File Editor
 * Plugin URI:        https://aide247.com/
 * Description:       Edit the site's `wp-config.php` file from the WordPress admin.
 * Version:           1.0.0
 * Author:            Aide247
 * Author URI:        https://aide247.com/
 * Text Domain:       aideconfigfileeditor
 * Domain Path:       /languages
 *
 * @package aideconfigfileeditor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Slug used for the admin menu page.
 */
const AIDE_CONFIG_FILE_EDITOR_SLUG = 'aide-config-file-editor';

/**
 * Nonce action for saving the config file.
 */
const AIDE_CONFIG_FILE_EDITOR_NONCE_ACTION = 'aide_config_file_editor_save';

add_action( 'admin_menu', 'aide_config_file_editor_register_menu' );

/**
 * Register the admin menu entry.
 *
 * This tool edits `wp-config.php`, so access is restricted to administrators.
 *
 * @return void
 */
function aide_config_file_editor_register_menu(): void {
	add_menu_page(
		__( 'Config File Editor', 'aideconfigfileeditor' ),
		__( 'Config File Editor', 'aideconfigfileeditor' ),
		'manage_options',
		AIDE_CONFIG_FILE_EDITOR_SLUG,
		'aide_config_file_editor_render_page',
		'dashicons-edit'
	);
}

/**
 * Render the config editor screen and handle submissions.
 *
 * @return void
 */
function aide_config_file_editor_render_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to edit this file.', 'aideconfigfileeditor' ) );
	}

	$file = trailingslashit( ABSPATH ) . 'wp-config.php';

	if ( ! file_exists( $file ) ) {
		echo '<div class="notice notice-error"><p><strong>' . esc_html__( 'wp-config.php was not found.', 'aideconfigfileeditor' ) . '</strong></p></div>';
		return;
	}

	// Handle POST back to this page (same screen) with a nonce for CSRF protection.
	if ( isset( $_POST['newcontent'] ) ) {
		check_admin_referer( AIDE_CONFIG_FILE_EDITOR_NONCE_ACTION );

		// `wp-config.php` is PHP source, so we do not "sanitize" it into safe text; we only normalize slashes and validate basic structure.
		$new_content = (string) wp_unslash( $_POST['newcontent'] );

		$valid = ( '' !== trim( $new_content ) ) && ( false !== strpos( $new_content, '<?php' ) );
		if ( ! $valid ) {
			echo '<div class="notice notice-error"><p><strong>' . esc_html__( 'Invalid content. A PHP opening tag (`<?php`) is required.', 'aideconfigfileeditor' ) . '</strong></p></div>';
		} else {
			$write_result = aide_config_file_editor_write_file( $file, $new_content );
			if ( true === $write_result ) {
				echo '<div class="notice notice-success"><p><strong>' . esc_html__( 'File saved successfully.', 'aideconfigfileeditor' ) . '</strong></p></div>';
			} else {
				echo '<div class="notice notice-error"><p><strong>' . esc_html( $write_result ) . '</strong></p></div>';
			}
		}
	}

	$current_content = (string) file_get_contents( $file );

	// Enqueue the built-in CodeMirror editor for a better editing UX.
	$settings = wp_enqueue_code_editor(
		array(
			'type' => 'application/x-httpd-php',
		)
	);
	wp_enqueue_script( 'wp-theme-plugin-editor' );
	wp_enqueue_style( 'wp-codemirror' );

	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Edit wp-config.php', 'aideconfigfileeditor' ); ?></h1>

		<form id="aide-config-file-editor-form" action="" method="post">
			<?php wp_nonce_field( AIDE_CONFIG_FILE_EDITOR_NONCE_ACTION ); ?>

			<textarea
				cols="70"
				rows="25"
				name="newcontent"
				id="aide-config-file-editor"
				aria-describedby="aide-config-file-editor-description"
			><?php echo esc_textarea( $current_content ); ?></textarea>

			<p class="description" id="aide-config-file-editor-description">
				<?php echo esc_html__( 'Edit your wp-config.php file with caution. A syntax error can bring your site down.', 'aideconfigfileeditor' ); ?>
			</p>

			<p class="submit">
				<input type="submit" class="button button-primary" value="<?php echo esc_attr__( 'Save Changes', 'aideconfigfileeditor' ); ?>">
			</p>
		</form>
	</div>

	<?php if ( ! empty( $settings ) ) : ?>
		<script>
			jQuery(function($) {
				var cm_settings = <?php echo wp_json_encode( $settings ); ?>;
				var editor = wp.codeEditor.initialize($('#aide-config-file-editor'), cm_settings);
				editor.codemirror.setSize(null, 600);
			});
		</script>
	<?php endif; ?>
	<?php
}

/**
 * Write content to a file using the WordPress filesystem API.
 *
 * @param string $file_path Absolute path to the file.
 * @param string $contents  File contents.
 * @return true|string True on success, or an error message on failure.
 */
function aide_config_file_editor_write_file( string $file_path, string $contents ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';

	$credentials = request_filesystem_credentials( admin_url() );
	if ( false === $credentials ) {
		return __( 'Filesystem credentials are required to write wp-config.php.', 'aideconfigfileeditor' );
	}

	if ( ! WP_Filesystem( $credentials ) ) {
		return __( 'Could not initialize the WordPress filesystem.', 'aideconfigfileeditor' );
	}

	global $wp_filesystem;

	if ( ! $wp_filesystem || ! is_object( $wp_filesystem ) ) {
		return __( 'WordPress filesystem is unavailable.', 'aideconfigfileeditor' );
	}

	if ( ! $wp_filesystem->put_contents( $file_path, $contents, FS_CHMOD_FILE ) ) {
		return __( 'Unable to write to wp-config.php. Please check file permissions.', 'aideconfigfileeditor' );
	}

	return true;
}
