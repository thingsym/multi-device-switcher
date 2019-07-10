<?php
/**
 * Widget Name: PC Switcher Widget
 * Plugin URI:  https://github.com/thingsym/multi-device-switcher
 * Description: PC Switcher Widget add-on for the Multi Device Switcher. Use this widget to add the PC Switcher to a widget.
 * Version:     1.7.0
 * Author:      thingsym
 * Author URI:  http://www.thingslabo.com/
 * License:     GPL2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: multi-device-switcher
 * Domain Path: /languages/
 *
 * @package     Multi_Device_Switcher
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

/**
 * Core class PC_Switcher
 *
 * @since 1.0.0
 */
class PC_Switcher extends WP_Widget {

	public function __construct() {
		load_plugin_textdomain( 'multi-device-switcher', false, dirname( plugin_basename( __MULTI_DEVICE_SWITCHER_FILE__ ) ) . '/languages/' );

		$widget_ops = array(
			'classname'   => 'widget_pc_switcher',
			'description' => __( 'Add the PC Switcher to a widget.', 'multi-device-switcher' ),
		);
		parent::__construct( 'pc-switcher', __( 'PC Switcher', 'multi-device-switcher' ), $widget_ops );
		$this->alt_option_name = 'widget_pc_switcher';
	}

	public function widget( $args, $instance ) {
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

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		return $instance;
	}

	public function form( $instance ) {
	}
}
