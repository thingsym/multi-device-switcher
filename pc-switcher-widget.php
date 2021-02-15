<?php
/**
 * Widget Name: PC Switcher Widget
 * Plugin URI:  https://github.com/thingsym/multi-device-switcher
 * Description: PC Switcher Widget add-on for the Multi Device Switcher. Use this widget to add the PC Switcher to a widget.
 * Version:     1.8.0
 * Author:      thingsym
 * Author URI:  https://www.thingslabo.com/
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

/**
 * Register PC_Switcher.
 *
 * @since 1.0.0
 */
function pc_switcher_load_widgets() {
	register_widget( 'PC_Switcher' );
}

/**
 * Core class PC_Switcher
 *
 * @since 1.0.0
 */
class PC_Switcher extends WP_Widget {

	/**
	 * Sets up a new widget instance.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {
		load_plugin_textdomain( 'multi-device-switcher', false, dirname( plugin_basename( __MULTI_DEVICE_SWITCHER_FILE__ ) ) . '/languages/' );

		$widget_ops = array(
			'classname'                   => 'widget_pc_switcher',
			'description'                 => __( 'Add the PC Switcher to a widget.', 'multi-device-switcher' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'pc-switcher', __( 'PC Switcher', 'multi-device-switcher' ), $widget_ops );
		$this->alt_option_name = 'widget_pc_switcher';
	}

	/**
	 * Outputs the content for the widget instance.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current widget instance.
	 */
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

	/**
	 * Handles updating settings for the current Archives widget instance.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form() method.
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		return $instance;
	}

	/**
	 * Outputs the settings form for the widget.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
	}
}
