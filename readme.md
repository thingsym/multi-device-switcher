# Introducing Multi Device Switcher

This WordPress plugin allows you to set a separate theme for device (Smart Phone, Tablet PC, Mobile Phone, Game and custom).
This plugin detects if your site is being viewed by UserAgent, and switches to selected theme.
The Custom Switcher can add every device.

## Features

- Set a separate theme for device (Smart Phone, Tablet PC, Mobile Phone, Game)
- Add every device by the Custom Switcher

## How do I use it?

1. Unzip files.
2. Upload "multi-device-switcher" to the "/wp-content/plugins/" directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Upload a separate theme to the "/wp-content/themes/" directory.
5. Go to the "Multi Device Switcher" options page through the 'Appearance' menu in WordPress.
6. Configure settings to your needs. Select Theme by Theme option. Add and fix UserAgent by UserAgent option if necessary.
7. Have fun!

## How to add the Custom Switcher

1. Go to the "Multi Device Switcher" options page through the 'Appearance' menu in WordPress.
2. Enter the name of the Custom Switcher (20 characters max, alphanumeric) to the 'Add Custom Switcher'. Push the button 'Add'.
3. Configure settings. Select Theme by Theme option. Add UserAgent by UserAgent option.
4. Have fun!

## Translations

- Japanese (ja) - <a href="http://global.thingslabo.com/blog/">Thingsym</a>

Translating a plugin takes a lot of time, effort, and patience. I really appreciate the hard work from these contributors.

If you have created or updated your own language pack, you can send [gettext PO and MO files](http://codex.wordpress.org/Translating_WordPress) to me. I can bundle it into Multi Device Switcher.

#### The latest PO and MO files

- [POT file](http://plugins.svn.wordpress.org/multi-device-switcher/trunk/languages/multi-device-switcher.pot)
- [PO files](http://plugins.svn.wordpress.org/multi-device-switcher/trunk/languages/)

#### Contact to me

You can send your own language pack to me.

- [multi-device-switcher - GitHub](https://github.com/thingsym/multi-device-switcher)
- [http://global.thingslabo.com/blog/ (en)](http://global.thingslabo.com/blog/)
- [http://blog.thingslabo.com (ja)](http://blog.thingslabo.com)

## Changelog

* Version 1.1.2
	* fixed: fix tabs and buttons
* Version 1.1.1
	* fixed: change the order of the UserAgent detection
	* updated: update default UserAgent
	* added: add HTTP/1.1 Vary header
* Version 1.1.0
	* new features: Custom Switcher
* Version 1.0.4
	* fixed: fix the object model PHP5, __construct() to replace Multi_Device_Switcher
	* fixed: wp_get_themes(), and wp_get_theme() to replace get_themes(), get_theme()
* Version 1.0.3
	* updated: update screenshots
	* fixed: fix reset button
* Version 1.0.2
	* added: add file uninstall.php
	* fixed: split admin_enqueue_scripts() into two functions
	* fixed: detects is_admin()
* Version 1.0.1
	* fixed: split multi_device_switcher_init() into two functions
* Version 1.0.0
	* Initial release.

## Upgrade Notice

* 1.1.2
	* Requires at least version 3.4 of the Wordpress

