<?php
/**
 * Widget Name: PC Switcher Widget
 * Plugin URI: https://github.com/thingsym/multi-device-switcher
 * Description: PC Switcher Widget add-on for the Multi Device Switcher. Use this widget to add the PC Switcher to a widget.
 * Version: 1.5.0
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
		load_plugin_textdomain( 'multi-device-switcher', false, 'multi-device-switcher/languages' );
		$widget_ops = array( 'classname' => 'widget_pc_switcher', 'description' => __( 'Add the PC Switcher to a widget.', 'multi-device-switcher' ) );
		parent::__construct( 'pc-switcher', __( 'PC Switcher', 'multi-device-switcher' ), $widget_ops );
		$this->alt_option_name = 'widget_pc_switcher';

		add_action( 'save_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( &$this, 'flush_widget_cache' ) );
	}

	function widget( $args, $instance ) {
		if ( ! function_exists( 'multi_device_switcher_add_pc_switcher' ) ) {
			return;
		}

		global $multi_device_switcher;
		$name = $multi_device_switcher->get_device_theme();

		if ( $name && 'None' !== $name ) {

			$cache = wp_cache_get( 'widget_pc_switcher', 'widget' );

			if ( ! is_array( $cache ) ) {
				$cache = array();
			}

			if ( isset( $cache[ $args['widget_id'] ] ) ) {
				echo $cache[ $args['widget_id'] ];
				return;
			}

			ob_start();

			echo $args['before_widget'];
			multi_device_switcher_add_pc_switcher();
			echo $args['after_widget'];

			$cache[ $args['widget_id'] ] = ob_get_flush();
			wp_cache_set( 'widget_pc_switcher', $cache, 'widget' );
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['widget_pc_switcher'] ) ) {
			delete_option( 'widget_pc_switcher' );
		}

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete( 'widget_pc_switcher', 'widget' );
	}

	function form( $instance ) {
	}
}
?>
