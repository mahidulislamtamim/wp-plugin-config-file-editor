# Aide :: Config File Editor

WordPress admin plugin to edit your site's `wp-config.php` directly from the dashboard.

## Features

- Adds a **Config File Editor** menu in wp-admin.
- Loads and edits `wp-config.php` using a code editor UI (CodeMirror).
- Restricts access to users with the `manage_options` capability.
- Uses nonce verification to protect save requests.
- Writes changes through the WordPress Filesystem API.
- Shows clear success/error notices for save operations.

## Requirements

- WordPress
- PHP compatible with your WordPress version
- File system credentials/permissions that allow writing `wp-config.php`

## Installation

1. Copy the `aide-config-file-editor` folder into `wp-content/plugins/`.
2. In WordPress admin, open **Plugins**.
3. Activate **Aide :: Config File Editor**.

## Usage

1. Sign in as an administrator.
2. Open **Config File Editor** from the admin menu.
3. Review and edit `wp-config.php`.
4. Click **Save Changes**.

If filesystem credentials are required, WordPress will prompt for them.

## Validation and Safety

- The plugin checks that submitted content is not empty and contains a PHP opening tag (`<?php`).
- A nonce check is required for form submission.
- Invalid PHP syntax can make your site unavailable. Always test carefully and keep a backup before editing.

## Plugin Details

- **Plugin Name:** Aide :: Config File Editor
- **Version:** 1.0.0
- **Author:** Aide247
- **Plugin URI:** https://aide247.com/
- **Text Domain:** `aideconfigfileeditor`

## License

No license file is currently included in this plugin directory.
