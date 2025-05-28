<?php
/*
 *@package aideconfigfileeditor
 */
/*
Plugin Name: Aide - Config File Editor
Plugin URI: https://aidecorp.com/
Description: This plugin is develop for edit wp-config.php file from wordprss admin panel.
Author: Aide Corporation
Version: 1.0.0
Last Updated : "May 28, 2025",
Author URI: https://aidecorp.com/
Text Domain: aideconfigfileeditor
Domain Path: /languages
*/

defined("ABSPATH") or die("Hey, you can not access this file, you silly human!");


// Add a menu item
add_action('admin_menu', 'my_custom_file_editor_menu');
function my_custom_file_editor_menu() {
    add_menu_page(
        'Config File Editor',  // Page Title
        'Config File Editor',  // Menu Title
        'edit_themes', // permission | manage_options, edit_themes
        'aide-config-file-editor',  // Slug
        'aide_config_file_editor_init', // Callback function
        'dashicons-edit' // menu icon
    );
}


// Initialize the Editor
function aide_config_file_editor_init() {

    // Check permission 
    if (!current_user_can('edit_themes')) {
        wp_die(__('You do not have sufficient permissions to edit templates for this site.'));
    }

    $file = ABSPATH . 'wp-config.php';

    if (isset($_POST['newcontent']) && isset($_POST['_wpnonce'])) {

        if (!wp_verify_nonce($_POST['_wpnonce'], 'edit-config_' . $file)) {
            wp_die(__('Security check failed.'));
        }

        // Sanitize content — since it's code, we mainly strip harmful control characters
        $new_content = stripslashes($_POST['newcontent']);

        // Extra precaution: prevent writing if wp-config.php is empty or malformed
        if (empty($new_content) || strpos($new_content, '<?php') === false) {
            echo '<div class="notice notice-error"><p><strong>Invalid content. PHP opening tag required.</strong></p></div>';
        } else {
            // Save the new content
            if (is_writable($file)) {
                file_put_contents($file, $new_content);
                echo '<div class="notice notice-success"><p><strong>File edited successfully.</strong></p></div>';
            } else {
                echo '<div class="notice notice-error"><p><strong>Cannot write to file. Check file permissions.</strong></p></div>';
            }
        }
    }

    // Enqueue WordPress code editor and get settings
    $settings = wp_enqueue_code_editor(array('type' => 'application/x-httpd-php'));
    wp_enqueue_script('wp-theme-plugin-editor');
    wp_enqueue_style('wp-codemirror');

    ?>
    <div class="wrap">
        <h1>Edit wp-config.php</h1>
        <form id="aide-config-file-editor-form" action="" method="post">
            <?php wp_nonce_field('edit-config_' . $file); ?>
            <textarea cols="70" rows="25" name="newcontent" id="aide-config-file-eidtor" aria-describedby="aide-config-file-eidtor-description"><?php
                echo esc_textarea(file_get_contents($file));
            ?></textarea>
            <p class="description" id="aide-config-file-eidtor-description">Edit your <code>wp-config.php</code> file with caution.</p>
            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
            </p>
        </form>
    </div>
    <script>
    jQuery(document).ready(function($) {
        var cm_settings = <?php echo json_encode($settings); ?>;
        var editor = wp.codeEditor.initialize($('#aide-config-file-eidtor'), cm_settings);
        editor.codemirror.setSize(null, 600); // Width: auto, Height: 600px
    });
    </script>
    <?php
}

?>
