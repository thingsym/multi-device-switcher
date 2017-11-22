<?php
/**
 * Widget Name: PC Switcher Widget
 * Plugin URI: https://github.com/thingsym/multi-device-switcher
 * Description: PC Switcher Widget add-on for the Multi Device Switcher. Use this widget to add the PC Switcher to a widget.
 * Version: 1.6.0
 * Author: thingsym
 * Author URI: http://www.thingslabo.com/
 * License: GPL2
 * Text Domain: multi-device-switcher
 * Domain Path: /languages/
 */

/**
 *     Copyright 2013 thingsym (http://www.thingslabo.com/)
 *
 *     This program is free software; you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation; either version 2 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program; if not, write to the Free Software
 *     Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
 */

/**
 * PC Switcher Widget
 *
 * @since 1.2
 */
if ( class_exists( 'Multi_Device_Switcher' ) ) {
	add_action( 'widgets_init', 'pc_switcher_load_widgets' );
}

function pc_switcher_load_widgets() {
	register_widget( 'PC_Switcher' );
}

class PC_Switcher extends WP_Widget {

	function __construct() {
		load_plugin_textdomain( 'multi-device-switcher', false, dirname( plugin_basename( __MULTI_DEVICE_SWITCHER_FILE__ ) ) . '/languages/' );

		$widget_ops = array(
			'classname'   => 'widget_pc_switcher',
			'description' => __( 'Add the PC Switcher to a widget.', 'multi-device-switcher' )
		);
		parent::__construct( 'pc-switcher', __( 'PC Switcher', 'multi-device-switcher' ), $widget_ops );
		$this->alt_option_name = 'widget_pc_switcher';
	}

	function widget( $args, $instance ) {
		if ( ! function_exists( 'multi_device_switcher_add_pc_switcher' ) ) {
			return;
		}

		global $multi_device_switcher;
		$name = $multi_device_switcher->get_device_theme();

		if ( $name && 'None' !== $name ) {
			echo $args['before_widget'];
			multi_device_switcher_add_pc_switcher();
			echo $args['after_widget'];
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		return $instance;
	}

	function form( $instance ) {
	}
}
