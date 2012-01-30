<?php
/*
Plugin Name: Multi Device Switcher
Plugin URI: https://github.com/thingsym/multi-device-switcher
Description: This WordPress plugin allows you to set a separate theme for device (Smart Phone, Tablet PC, Mobile Phone, Game).
Version: 1.0.0
Author: Yousuke Mizuno
Author URI: http://www.thingslabo.com/
License: GPL2
*/

/*
    Copyright 2012 thingsym (http://www.thingslabo.com/)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
*/

class Multi_Device_Switcher {

	function Multi_Device_Switcher() {

		$userAgent = $this->get_options_userAgent();

		if ( $userAgent['smart'] && preg_match( '/' . implode( '|', $userAgent['smart'] ) . '/i', $_SERVER['HTTP_USER_AGENT']) ) {
			$this->device = 'smart';
		}
		elseif ( $userAgent['tablet'] && preg_match( '/' . implode( '|', $userAgent['tablet'] ) . '/i', $_SERVER['HTTP_USER_AGENT']) ) {
			$this->device = 'tablet';
		}
		elseif ( $userAgent['mobile'] && preg_match( '/' . implode( '|', $userAgent['mobile'] ) . '/i', $_SERVER['HTTP_USER_AGENT']) ) {
			$this->device = 'mobile';
		}
		elseif ( $userAgent['game'] && preg_match( '/' . implode( '|', $userAgent['game'] ) . '/i', $_SERVER['HTTP_USER_AGENT']) ) {
			$this->device = 'game';
		}
		else {
			$this->device = '';
		}

		if ($this->device) {
			add_filter('stylesheet', array(&$this, 'get_stylesheet'));
			add_filter('template', array(&$this, 'get_template'));
		}
	}

	function get_options_userAgent() {
		$options = get_option('multi_device_switcher_options');
		$default_options = multi_device_switcher_get_default_options();

		if ( ! isset( $options['userAgent_smart'] ) )
			$options['userAgent_smart'] = $default_options['userAgent_smart'];
		if ( ! isset( $options['userAgent_tablet'] ) )
			$options['userAgent_tablet'] = $default_options['userAgent_tablet'];
		if ( ! isset( $options['userAgent_mobile'] ) )
			$options['userAgent_mobile'] = $default_options['userAgent_mobile'];
		if ( ! isset( $options['userAgent_game'] ) )
			$options['userAgent_game'] = $default_options['userAgent_game'];

		if ( $options['userAgent_smart'] )
			$userAgent['smart'] = preg_split( "/,\s*/", $options['userAgent_smart'] );
		if ( $options['userAgent_tablet'] )
			$userAgent['tablet'] = preg_split( "/,\s*/", $options['userAgent_tablet'] );
		if ( $options['userAgent_mobile'] )
			$userAgent['mobile'] = preg_split( "/,\s*/", $options['userAgent_mobile'] );
		if ( $options['userAgent_game'] )
			$userAgent['game'] = preg_split( "/,\s*/", $options['userAgent_game'] );

		return $userAgent;
	}

	function get_stylesheet($stylesheet = '') {
		$name = $this->get_device_theme();

		if ( empty($name) ) 
			return $stylesheet;

		$theme = get_theme($name);

		if ( empty($theme) ) 
			return $stylesheet;

		if ( isset($theme['Status']) && $theme['Status'] != 'publish' )
			return $stylesheet;

		return $theme['Stylesheet'];
	}

	function get_template($template = '') {
		$name = $this->get_device_theme();

		if ( empty($name) )
			return $template;

		$theme = get_theme($name);
		
		if ( empty( $theme ) )
			return $template;

		if ( isset($theme['Status']) && $theme['Status'] != 'publish' )
			return $template;

		return $theme['Template'];
	}

	function get_device_theme() {
		$options = get_option('multi_device_switcher_options');

		if ($this->device == 'smart') {
			return $options['theme_smartphone'];
		}
		elseif ($this->device == 'tablet') {
			return $options['theme_tablet'];
		}
		elseif ($this->device == 'mobile') {
			return $options['theme_mobile'];
		}
		elseif ($this->device == 'game') {
			return $options['theme_game'];
		}

		return;
	}
}

$multi_device_switcher = new Multi_Device_Switcher;

/**
 * Register the form setting for our multi_device_switcher array.
 *
 * This function is attached to the admin_init action hook.
 *
 * This call to register_setting() registers a validation callback, multi_device_switcher_validate(),
 * which is used when the option is saved, to ensure that our option values are complete, properly
 * formatted, and safe.
 *
 * @since 1.0
 */
function multi_device_switcher_init() {
	// If we have no options in the database, let's add them now.
	if ( false === multi_device_switcher_get_options() )
		add_option( 'multi_device_switcher_options' );

	load_plugin_textdomain('multi-device-switcher', false, 'multi-device-switcher/languages');

	wp_enqueue_style( 'multi-device-switcher-options', WP_PLUGIN_URL . '/multi-device-switcher/multi-device-switcher.css', false, '2011-08-22' );
	wp_enqueue_style( 'thickbox', includes_url() . '/js/thickbox/thickbox.css', false, '20090514' );
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script( 'multi-device-switcher-options', WP_PLUGIN_URL . '/multi-device-switcher/multi-device-switcher.js', array( 'jquery' ), '2011-08-22' );

	register_setting(
		'multi_device_switcher',       // Options group, see settings_fields() call in multi_device_switcher_render_page()
		'multi_device_switcher_options', // Database option, see multi_device_switcher_get_options()
		'multi_device_switcher_validate' // The sanitization callback, see multi_device_switcher_validate()
	);
}

add_action( 'admin_init', 'multi_device_switcher_init' );

/**
 * Change the capability required to save the 'multi_device_switcher' options group.
 *
 * @see multi_device_switcher_init() First parameter to register_setting() is the name of the options group.
 * @see multi_device_switcher_add_page() The edit_theme_options capability is used for viewing the page.
 *
 * By default, the options groups for all registered settings require the manage_options capability.
 * By default, only administrators have either of these capabilities, but the desire here is
 * to allow for finer-grained control for roles and users.
 *
 * @param string $capability The capability used for the page, which is manage_options by default.
 * @return string The capability to actually use.
 */
function multi_device_switcher_page_capability( $capability ) {
	return 'edit_theme_options';
}
add_filter( 'option_page_capability_multi_device_switcher', 'multi_device_switcher_page_capability' );

/**
 * Add our options page to the admin menu, including some help documentation.
 *
 * This function is attached to the admin_menu action hook.
 *
 * @since 1.0
 */
function multi_device_switcher_add_page() {
	load_plugin_textdomain('multi-device-switcher', false, 'multi-device-switcher/languages');

	$theme_page = add_theme_page(
		__( 'Multi Device Switcher', 'multi-device-switcher' ),   // Name of page
		__( 'Multi Device Switcher', 'multi-device-switcher' ),   // Label in menu
		'manage_options',                    // Capability required
		'multi-device-switcher',                         // Menu slug, used to uniquely identify the page
		'multi_device_switcher_render_page' // Function that renders the options page
	);

	if ( ! $theme_page )
		return;

	$help = '';

	if ($help) 
		add_contextual_help( $theme_page, $help );
}
add_action( 'admin_menu', 'multi_device_switcher_add_page' );

/**
 * Returns the default options.
 *
 * @since 1.0
 */
function multi_device_switcher_get_default_options() {
	$default_theme_options = array(
		'theme_smartphone' => 'None',
		'theme_tablet' => 'None',
		'theme_mobile' => 'None',
		'theme_game' => 'None',
		'userAgent_smart' => 'iPhone, iPod, Android, dream, CUPCAKE, Windows Phone, webOS, BlackBerry8707, BlackBerry9000, BlackBerry9300, BlackBerry9500, BlackBerry9530, BlackBerry9520, BlackBerry9550, BlackBerry9700, BlackBerry9800',
		'userAgent_tablet' => 'iPad',
		'userAgent_mobile' => 'DoCoMo, SoftBank, J-PHONE, Vodafone, KDDI, UP.Browser, WILLCOM, emobile, DDIPOCKET, Windows CE, BlackBerry, Symbian, PalmOS, Huawei, IAC, Nokia',
		'userAgent_game' => 'PlayStation Portable, PlayStation Vita, PSP, PS2, PLAYSTATION 3, Nitro, Nintendo 3DS, Nintendo Wii',
	);

	return $default_theme_options;
}

/**
 * Returns the options array.
 *
 * @since 1.0
 */
function multi_device_switcher_get_options() {
	$options = get_option( 'multi_device_switcher_options' );
	$default_options = multi_device_switcher_get_default_options();

	if ( ! isset( $options['theme_smartphone'] ) )
		$options['theme_smartphone'] = $default_options['theme_smartphone'];
	if ( ! isset( $options['theme_tablet'] ) )
		$options['theme_tablet'] = $default_options['theme_tablet'];
	if ( ! isset( $options['theme_mobile'] ) )
		$options['theme_mobile'] = $default_options['theme_mobile'];
	if ( ! isset( $options['theme_game'] ) )
		$options['theme_game'] = $default_options['theme_game'];

	if ( ! isset( $options['userAgent_smart'] ) )
		$options['userAgent_smart'] = $default_options['userAgent_smart'];
	if ( ! isset( $options['userAgent_tablet'] ) )
		$options['userAgent_tablet'] = $default_options['userAgent_tablet'];
	if ( ! isset( $options['userAgent_mobile'] ) )
		$options['userAgent_mobile'] = $default_options['userAgent_mobile'];
	if ( ! isset( $options['userAgent_game'] ) )
		$options['userAgent_game'] = $default_options['userAgent_game'];

	return $options;
}

/**
 * Rendering Options Setting Page.
 *
 * @since 1.0
 */
function multi_device_switcher_render_page() {
	?>
	<div class="wrap" style="width: 60%; float: left;">
		<div id="icon-themes" class="icon32"><br></div>
		<h2><?php printf( __( 'Multi Device Switcher', 'multi-device-switcher' ), get_current_theme() ); ?></h2>
		<?php settings_errors(); ?>

		<form method="post" action="options.php">
			<?php
				settings_fields( 'multi_device_switcher' );
				$options = multi_device_switcher_get_options();
			?>

			<div id="admin-tabs">
			<fieldset id="Theme" class="options">
			<h3 class="label"><?php _e( 'Theme', 'multi-device-switcher' ); ?></h3>
			<table class="form-table">
				<tr><th scope="row"><?php _e( 'Smart Phone Theme', 'multi-device-switcher' ); ?></th>
					<td>

			<?php
				$themes = get_themes();
				$default_theme = get_current_theme();

				if (count($themes) > 1) {
					$theme_names = array_keys($themes);
					natcasesort($theme_names);
					$html = '<select name="multi_device_switcher_options[theme_smartphone]">';

					if (($options['theme_smartphone'] == 'None') || ($options['theme_smartphone'] == '')) {
						$html .= '<option value="None" selected="selected">None</option>';
					}
					else {
						$html .= '<option value="None">None</option>';
					}

					foreach ($theme_names as $theme_name) {
						if ($options['theme_smartphone'] == $theme_name) {
							$html .= '<option value="' . $theme_name . '" selected="selected">' . htmlspecialchars($theme_name) . '</option>';
						}
						else {
							$html .= '<option value="' . $theme_name . '">' . htmlspecialchars($theme_name) . '</option>';
						}
					}
					$html .= '</select>';
				}
				echo $html;
			?>
					</td>
				</tr>
				<tr><th scope="row"><?php _e( 'Tablet PC Theme', 'multi-device-switcher' ); ?></th>
					<td>

			<?php
				$themes = get_themes();
				$default_theme = get_current_theme();

				if (count($themes) > 1) {
					$theme_names = array_keys($themes);
					natcasesort($theme_names);
					$html = '<select name="multi_device_switcher_options[theme_tablet]">';

					if (($options['theme_tablet'] == 'None') || ($options['theme_tablet'] == '')) {
						$html .= '<option value="None" selected="selected">None</option>';
					}
					else {
						$html .= '<option value="None">None</option>';
					}

					foreach ($theme_names as $theme_name) {
						if ($options['theme_tablet'] == $theme_name) {
							$html .= '<option value="' . $theme_name . '" selected="selected">' . htmlspecialchars($theme_name) . '</option>';
						}
						else {
							$html .= '<option value="' . $theme_name . '">' . htmlspecialchars($theme_name) . '</option>';
						}
					}
					$html .= '</select>';
				}
				echo $html;
			?>
					</td>
				</tr>
				<tr><th scope="row"><?php _e( 'Mobile Phone Theme', 'multi-device-switcher' ); ?></th>
					<td>

			<?php
				$themes = get_themes();
				$default_theme = get_current_theme();

				if (count($themes) > 1) {
					$theme_names = array_keys($themes);
					natcasesort($theme_names);
					$html = '<select name="multi_device_switcher_options[theme_mobile]">';

					if (($options['theme_mobile'] == 'None') || ($options['theme_mobile'] == '')) {
						$html .= '<option value="None" selected="selected">None</option>';
					}
					else {
						$html .= '<option value="None">None</option>';
					}

					foreach ($theme_names as $theme_name) {
						if ($options['theme_mobile'] == $theme_name) {
							$html .= '<option value="' . $theme_name . '" selected="selected">' . htmlspecialchars($theme_name) . '</option>';
						}
						else {
							$html .= '<option value="' . $theme_name . '">' . htmlspecialchars($theme_name) . '</option>';
						}
					}
					$html .= '</select>';
				}
				echo $html;
			?>
					</td>
				</tr>
				<tr><th scope="row"><?php _e( 'Game Platforms Theme', 'multi-device-switcher' ); ?></th>
					<td>

			<?php
				$themes = get_themes();
				$default_theme = get_current_theme();

				if (count($themes) > 1) {
					$theme_names = array_keys($themes);
					natcasesort($theme_names);
					$html = '<select name="multi_device_switcher_options[theme_game]">';

					if (($options['theme_game'] == 'None') || ($options['theme_game'] == '')) {
						$html .= '<option value="None" selected="selected">None</option>';
					}
					else {
						$html .= '<option value="None">None</option>';
					}

					foreach ($theme_names as $theme_name) {
						if ($options['theme_game'] == $theme_name) {
							$html .= '<option value="' . $theme_name . '" selected="selected">' . htmlspecialchars($theme_name) . '</option>';
						}
						else {
							$html .= '<option value="' . $theme_name . '">' . htmlspecialchars($theme_name) . '</option>';
						}
					}
					$html .= '</select>';
				}
				echo $html;
			?>
					</td>
				</tr>
			</table>
			</fieldset>

			<fieldset id="UserAgent" class="options">
			<h3 class="label"><?php _e( 'UserAgent', 'multi-device-switcher' ); ?></h3>
			<table class="form-table">
				<tr><th scope="row"><?php _e( 'Smart Phone', 'multi-device-switcher' ); ?></th>
					<td><textarea name="multi_device_switcher_options[userAgent_smart]" rows="4" cols="42"><?php echo $options['userAgent_smart']; ?></textarea></td>
				</tr>
				<tr><th scope="row"><?php _e( 'Tablet PC', 'multi-device-switcher' ); ?></th>
					<td><textarea name="multi_device_switcher_options[userAgent_tablet]" rows="4" cols="42"><?php echo $options['userAgent_tablet']; ?></textarea></td>
				</tr>
				<tr><th scope="row"><?php _e( 'Mobile Phone', 'multi-device-switcher' ); ?></th>
					<td><textarea name="multi_device_switcher_options[userAgent_mobile]" rows="4" cols="42"><?php echo $options['userAgent_mobile']; ?></textarea></td>
				</tr>
				<tr><th scope="row"><?php _e( 'Game Platforms', 'multi-device-switcher' ); ?></th>
					<td><textarea name="multi_device_switcher_options[userAgent_game]" rows="4" cols="42"><?php echo $options['userAgent_game']; ?></textarea></td>
				</tr>
				<tr><th></th>
					<td><input type="submit" name="multi_device_switcher_options[restore_UserAgent]" value="<?php _e( 'Reset Settings to Default UserAgent', 'multi-device-switcher' ); ?>"></td>
				</tr>
			</table>
			</fieldset>
			</div>
			<?php submit_button(); ?>
		</form>
	</div>

	<div id="donate" style="width: 36%; float: left; margin: 2em 0;padding: 0.4em;border: 1px solid #ddd;">
	<h2><?php _e( 'Donationware', 'multi-device-switcher' ); ?></h2>
	<p><?php _e( 'If you like this plugin, please donate to support development and maintenance.', 'multi-device-switcher' ); ?></p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="9L53NELFMHTWW">
<table>
<tr><td><input type="hidden" name="on0" value="Donationware">Donationware</td></tr><tr><td><select name="os0">
	<option value="1. Donate">1. Donate $3.00 USD</option>
	<option value="2. Donate">2. Donate $5.00 USD</option>
	<option value="3. Donate">3. Donate $7.00 USD</option>
	<option value="4. Donate" selected="selected">4. Donate $10.00 USD</option>
	<option value="5. Donate">5. Donate $20.00 USD</option>
	<option value="6. Donate">6. Donate $30.00 USD</option>
	<option value="7. Donate">7. Donate $40.00 USD</option>
	<option value="8. Donate">8. Donate $50.00 USD</option>
	<option value="9. Donate">9. Donate $60.00 USD</option>
	<option value="10. Donate">10. Donate $70.00 USD</option>
</select> </td></tr>
</table>
<input type="hidden" name="currency_code" value="USD">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/ja_JP/i/scr/pixel.gif" width="1" height="1">
</form>
	</div>

	<?php
}

/**
 * Sanitize and validate form input. Accepts an array, return a sanitized array.
 *
 * @see multi_device_switcher_init()
 *
 * @since 1.0
 */
function multi_device_switcher_validate( $input ) {
	$output = $default_options = multi_device_switcher_get_default_options();

	if ( isset( $input['theme_smartphone'] ) )
		$output['theme_smartphone'] = $input['theme_smartphone'];
	if ( isset( $input['theme_tablet'] ) )
		$output['theme_tablet'] = $input['theme_tablet'];
	if ( isset( $input['theme_mobile'] ) )
		$output['theme_mobile'] = $input['theme_mobile'];
	if ( isset( $input['theme_game'] ) )
		$output['theme_game'] = $input['theme_game'];

	if (isset( $input['restore_UserAgent'] )) {
		$output['userAgent_smart'] = $default_options['userAgent_smart'];
		$output['userAgent_tablet'] = $default_options['userAgent_tablet'];
		$output['userAgent_mobile'] = $default_options['userAgent_mobile'];
		$output['userAgent_game'] = $default_options['userAgent_game'];
	}
	else {
		if ( isset( $input['userAgent_smart'] ) )
			$output['userAgent_smart'] = $input['userAgent_smart'];
		if ( isset( $input['userAgent_tablet'] ) )
			$output['userAgent_tablet'] = $input['userAgent_tablet'];
		if ( isset( $input['userAgent_mobile'] ) )
			$output['userAgent_mobile'] = $input['userAgent_mobile'];
		if ( isset( $input['userAgent_game'] ) )
			$output['userAgent_game'] = $input['userAgent_game'];
	}

	return apply_filters( 'multi_device_switcher_validate', $output, $input, $default_options );
}

?>
