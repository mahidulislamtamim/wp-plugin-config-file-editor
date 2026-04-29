=== Aide :: Config File Editor ===
Contributors: aide247
Tags: wp-config, config, editor, admin, developer-tools
Requires at least: 5.8
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Edit your site's wp-config.php file from the WordPress admin dashboard.

== Description ==

Aide :: Config File Editor provides an admin screen for editing `wp-config.php` without leaving WordPress.

Features:

* Admin menu page for editing `wp-config.php`
* Capability protection (`manage_options`)
* CSRF protection with a WordPress nonce
* Uses WordPress Filesystem API for file writes
* CodeMirror-powered editor for improved editing experience

Important:

* A syntax error in `wp-config.php` can make your site unavailable.
* Always keep a backup before saving changes.

== Installation ==

1. Upload the `aide-config-file-editor` folder to `/wp-content/plugins/`.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Open **Config File Editor** in the admin menu.

== Frequently Asked Questions ==

= Who can use this plugin? =

Only users with the `manage_options` capability (typically administrators).

= Does this plugin edit files other than wp-config.php? =

No. It is designed specifically to edit `wp-config.php`.

= What happens if file permissions prevent saving? =

The plugin will show an error message. You may need valid filesystem credentials or corrected server permissions.

== Screenshots ==

1. Config File Editor screen in wp-admin.

== Changelog ==

= 1.0.0 =

* Initial release.

== Upgrade Notice ==

= 1.0.0 =

Initial release.
