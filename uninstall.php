<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

function multi_device_switcher_delete_plugin() {
	delete_option( 'multi_device_switcher_options' );
}

multi_device_switcher_delete_plugin();

?>