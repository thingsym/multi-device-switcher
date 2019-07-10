<?php
/**
 * Uninstall
 *
 * @since 1.0.0
 *
 * @package     Multi_Device_Switcher
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

/**
 * Uninstall.
 *
 * @return void
 *
 * @since 1.0.0
 */
function multi_device_switcher_delete_plugin() {
	delete_option( 'multi_device_switcher_options' );
}

multi_device_switcher_delete_plugin();
