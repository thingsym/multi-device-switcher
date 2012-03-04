# Introducing Multi Device Switcher

This WordPress plugin allows you to set a separate theme for device (Smart Phone, Tablet PC, Mobile Phone, Game).
This plugin detects if your site is being viewed by UserAgent and switches to selected theme.

## How do I use it?

1. Unzip files.
2. Upload "multi-device-switcher" to the "/wp-content/plugins/" directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Upload a separate theme to the "/wp-content/themes/" directory.
5. Go to the "Multi Device Switcher" options page through the 'Appearance' menu in WordPress.
6. Configure settings to your needs. Select Theme by Theme option. Add and fix UserAgent by UserAgent option if necessary.
7. Have fun!

## Changelog

* Version 1.0.2
	* added: add file uninstall.php
	* fixed: split admin_enqueue_scripts() into two functions
	* fixed: detects is_admin()
* Version 1.0.1
	* fixed: split multi_device_switcher_init() into two functions
* Version 1.0.0
	* Initial release.
