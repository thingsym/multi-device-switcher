<?php
/**
 * Plugin Name: Multi Device Switcher
 * Plugin URI: https://github.com/thingsym/multi-device-switcher
 * Description: This WordPress plugin allows you to set a separate theme for device (Smart Phone, Tablet PC, Mobile Phone, Game and custom).
 * Version: 1.5.0
 * Author: thingsym
 * Author URI: http://www.thingslabo.com/
 * License: GPL2
 * Text Domain: multi-device-switcher
 * Domain Path: /languages/
 */

/**
 *     Copyright 2012 thingsym (http://www.thingslabo.com/)
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

class Multi_Device_Switcher {
	protected $option_group = 'multi_device_switcher';
	protected $option_name = 'multi_device_switcher_options';

	protected $page_title = 'Multi Device Switcher';
	protected $menu_title = 'Multi Device Switcher';
	protected $menu_slug = 'multi-device-switcher';
	protected $capability = 'manage_options';

	protected $textdomain = 'multi-device-switcher';
	protected $languages_path = 'multi-device-switcher/languages';

	protected $cookie_name_multi_device_switcher = 'multi-device-switcher';
	protected $cookie_name_disable_switcher = 'disable-switcher';
	protected $cookie_name_pc_switcher = 'pc-switcher';

	protected $device = '';

	public function __construct() {
		if ( is_admin() ) {
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_filter( 'option_page_capability_' . $this->option_group, array( $this, 'option_page_capability' ) );
			add_action( 'admin_menu', array( $this, 'add_option_page' ) );
			add_action( 'customize_register', array( $this, 'customize_register' ) );
		}
		else {
			add_filter( 'wp_headers', array( $this, 'add_header_vary' ) );
			add_shortcode( 'multi', array( $this, 'shortcode_display_switcher' ) );
			add_action( 'plugins_loaded', array( $this, 'switch_theme' ) );
		}

		add_action( 'plugins_loaded', array( $this, 'load_file' ) );
	}

	public function switch_theme() {

		if ( isset( $_COOKIE[ $this->cookie_name_disable_switcher ] ) ) {
			setcookie( $this->cookie_name_disable_switcher, null, time() - 3600, '/' );
		}

		if ( $this->is_disable_switcher() ) {
			setcookie( $this->cookie_name_multi_device_switcher, null, time() - 3600, '/' );
			setcookie( $this->cookie_name_disable_switcher, 1, null, '/' );
			return;
		}

		add_action( 'init', array( $this, 'session' ) );

		$userAgent = $this->get_options_userAgent();
		$server_ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';

		foreach ( array_reverse( $userAgent ) as $key => $val ) {
			if ( ! preg_match( '/^custom_switcher_/', $key ) ) {
				continue;
			}
			if ( $userAgent[ $key ] && preg_match( '/' . implode( '|', $userAgent[ $key ] ) . '/i', $server_ua ) ) {
				$this->device = $key;
				break;
			}
		}

		if ( ! $this->device ) {
			if ( $userAgent['game'] && preg_match( '/' . implode( '|', $userAgent['game'] ) . '/i', $server_ua ) ) {
				$this->device = 'game';
			}
			elseif ( $userAgent['tablet'] && preg_match( '/' . implode( '|', $userAgent['tablet'] ) . '/i', $server_ua ) ) {
				$this->device = 'tablet';
			}
			elseif ( $userAgent['smart'] && preg_match( '/' . implode( '|', $userAgent['smart'] ) . '/i', $server_ua ) ) {
				$this->device = 'smart';
			}
			elseif ( $userAgent['mobile'] && preg_match( '/' . implode( '|', $userAgent['mobile'] ) . '/i', $server_ua ) ) {
				$this->device = 'mobile';
			}
		}

		if ( $this->device ) {
			load_plugin_textdomain( $this->textdomain, false, $this->languages_path );

			add_filter( 'stylesheet', array( $this, 'get_stylesheet' ) );
			add_filter( 'template', array( $this, 'get_template' ) );
			add_action( 'wp_footer', array( $this, 'add_pc_switcher' ) );

			setcookie( $this->cookie_name_multi_device_switcher, preg_replace( '/^custom_switcher_/', '', $this->device ), null, '/' );
		}
		else {
			setcookie( $this->cookie_name_multi_device_switcher, null, time() - 3600, '/' );
		}

		if ( isset( $_COOKIE[ $this->cookie_name_pc_switcher ] ) ) {
			remove_filter( 'stylesheet', array( $this, 'get_stylesheet' ) );
			remove_filter( 'template', array( $this, 'get_template' ) );
		}
	}

	public function get_options_userAgent() {
		$options = $this->get_options();

		$userAgent['smart'] = empty( $options['userAgent_smart'] ) ? '' : preg_split( '/,\s*/', $options['userAgent_smart'] );
		$userAgent['tablet'] = empty( $options['userAgent_tablet'] ) ? '' : preg_split( '/,\s*/', $options['userAgent_tablet'] );
		$userAgent['mobile'] = empty( $options['userAgent_mobile'] ) ? '' : preg_split( '/,\s*/', $options['userAgent_mobile'] );
		$userAgent['game'] = empty( $options['userAgent_game'] ) ? '' : preg_split( '/,\s*/', $options['userAgent_game'] );

		foreach ( $options as $key => $val ) {
			if ( ! preg_match( '/^custom_switcher_userAgent_/', $key ) ) {
				continue;
			}

			$custom_switcher_name = preg_replace( '/^custom_switcher_userAgent_/', '', $key );
			$userAgent[ 'custom_switcher_' . $custom_switcher_name ] = empty( $val ) ? '' : preg_split( '/,\s*/', $val );
		}

		return $userAgent;
	}

	public function get_stylesheet( $stylesheet = '' ) {
		$name = $this->get_device_theme();

		if ( empty( $name ) ) {
			return $stylesheet;
		}

		$themes = wp_get_themes();
		foreach ( $themes as $t ) {
			if ( $name === $t->get( 'Name' ) ) {
				$theme = $t;
				break;
			}
		}

		if ( empty( $theme ) ) {
			return $stylesheet;
		}

		if ( 'publish' !== $theme->get( 'Status' ) ) {
			return $stylesheet;
		}

		return $theme['Stylesheet'];
	}

	public function get_template( $template = '' ) {
		$name = $this->get_device_theme();

		if ( empty( $name ) ) {
			return $template;
		}

		$themes = wp_get_themes();
		foreach ( $themes as $t ) {
			if ( $name === $t->get( 'Name' ) ) {
				$theme = $t;
				break;
			}
		}

		if ( empty( $theme ) ) {
			return $template;
		}

		if ( 'publish' !== $theme->get( 'Status' ) ) {
			return $template;
		}

		return $theme['Template'];
	}

	public function get_device_theme() {
		$options = $this->get_options();

		if ( 'smart' === $this->device ) {
			return $options['theme_smartphone'];
		}
		elseif ( 'tablet' === $this->device ) {
			return $options['theme_tablet'];
		}
		elseif ( 'mobile' === $this->device ) {
			return $options['theme_mobile'];
		}
		elseif ( 'game' === $this->device ) {
			return $options['theme_game'];
		}
		else {
			foreach ( $options as $key => $val ) {
				if ( ! preg_match( '/^custom_switcher_theme_/', $key ) ) {
					continue;
				}

				$custom_switcher_name = preg_replace( '/^custom_switcher_theme_/', '', $key );

				if ( 'custom_switcher_' . $custom_switcher_name === $this->device ) {
					return $options[ $key ];
				}
			}
		}

		return;
	}

	public function session() {
		if ( isset( $_GET['pc-switcher'] ) ) {
			setcookie( $this->cookie_name_pc_switcher, $_GET['pc-switcher'] ? 1 : '', null, '/' );

			$uri = preg_replace( '/^(.+?)(\?.*)$/', '$1', $_SERVER['REQUEST_URI'] );

			unset( $_GET['pc-switcher'] );
			if ( ! empty( $_GET ) ) {
				$uri .= '?' . http_build_query( $_GET );
			}

			wp_redirect( esc_url( $uri ) );
			exit;
		}
	}

	public function add_pc_switcher( $pc_switcher = 0 ) {
		$options = $this->get_options();
		$name = $this->get_device_theme();

		if ( $options['pc_switcher'] ) {
			$pc_switcher = 1;
		}

		if ( $pc_switcher && $name && 'None' !== $name ) {
			if ( $options['default_css'] ) {
				wp_enqueue_style( 'pc-switcher-options', plugins_url() . '/multi-device-switcher/pc-switcher.css', false, '2013-03-20' );
			}

			$uri = is_ssl() ? 'https://' : 'http://';
			$uri .= $_SERVER['HTTP_HOST'];

			if ( isset( $_COOKIE[ $this->cookie_name_pc_switcher ] ) ) {
				$uri .= add_query_arg( 'pc-switcher', 0 );
		?>
<div class="pc-switcher"><a href="<?php echo esc_url( $uri ); ?>"><?php esc_html_e( 'Mobile', $this->textdomain ); ?></a><span class="active"><?php esc_html_e( 'PC', $this->textdomain ); ?></span></div>
		<?php
			}
			else {
				$uri .= add_query_arg( 'pc-switcher', 1 );
		?>
<div class="pc-switcher"><span class="active"><?php esc_html_e( 'Mobile', $this->textdomain ); ?></span><a href="<?php echo esc_url( $uri ); ?>"><?php esc_html_e( 'PC', $this->textdomain ); ?></a></div>
		<?php
			}
		}
	}

	public function is_multi_device( $device = '' ) {
		if ( $device === $this->device ) {
			return 1;
		}
		if ( 'custom_switcher_' . $device === $this->device ) {
			return 1;
		}

		return 0;
	}

	public function is_pc_switcher() {
		return isset( $_COOKIE[ $this->cookie_name_pc_switcher ] );
	}

	public function is_disable_switcher( $disable = 0 ) {
		$options = $this->get_options();
		$disable_path = preg_split( '/\R/', $options['disable_path'], -1, PREG_SPLIT_NO_EMPTY );

		foreach ( $disable_path as $path ) {
			if ( $options['enable_regex'] ) {
				if ( preg_match( '/' . $path . '/i', $_SERVER['REQUEST_URI'] ) ) {
					$disable = 1;
					break;
				}
			}
			else {
				if ( preg_match( '/^' . preg_quote( $path , '/' ) . '$/i', $_SERVER['REQUEST_URI'] ) ) {
					$disable = 1;
					break;
				}
			}
		}

		return $disable;
	}

	public function shortcode_display_switcher( $atts, $content = '' ) {
		$atts = shortcode_atts( array(
			'device' => '',
		), $atts );

		if ( empty( $atts['device'] ) && ( $this->is_multi_device( $atts['device'] ) || $this->is_pc_switcher() ) ) {
			return $content;
		}
		elseif ( ! empty( $atts['device'] ) && $this->is_multi_device( $atts['device'] ) && ! $this->is_pc_switcher() ) {
			return $content;
		}

		return '';
	}

	/**
	 * Add HTTP/1.1 Vary header.
	 *
	 * @since 1.1.1
	 *
	 */
	public function add_header_vary( $headers ) {
		$headers['Vary'] = 'User-Agent';
		return $headers;
	}

	/**
	 * Properly enqueue scripts for our multi_device_switcher options page.
	 *
	 * This function is attached to the admin_enqueue_scripts action hook.
	 *
	 * @since 1.0
	 *
	 */
	public function admin_enqueue_scripts( $hook_suffix ) {
		wp_enqueue_script( 'multi-device-switcher-options', plugins_url() . '/multi-device-switcher/multi-device-switcher.js', array( 'jquery', 'jquery-ui-tabs' ), '2011-08-22' );
	}

	/**
	 * Properly enqueue styles for our multi_device_switcher options page.
	 *
	 * This function is attached to the admin_enqueue_styles action hook.
	 *
	 * @since 1.0
	 *
	 */
	public function admin_enqueue_styles( $hook_suffix ) {
		wp_enqueue_style( 'multi-device-switcher-options', plugins_url() . '/multi-device-switcher/multi-device-switcher.css', false, '2011-08-22' );
	}

	/**
	 * Register the form setting for our multi_device_switcher array.
	 *
	 * This function is attached to the admin_init action hook.
	 *
	 * This call to register_setting() registers a validation callback, validate(),
	 * which is used when the option is saved, to ensure that our option values are complete, properly
	 * formatted, and safe.
	 *
	 * @since 1.0
	 */
	public function admin_init() {
		// If we have no options in the database, let's add them now.
		if ( false === $this->get_options() ) {
			add_option( $this->option_name );
		}

		register_setting(
			$this->option_group,
			$this->option_name,
			array( $this, 'validate' )
		);
	}

	/**
	 * Change the capability required to save the 'multi_device_switcher' options group.
	 *
	 * @see admin_init() First parameter to register_setting() is the name of the options group.
	 * @see add_option_page() The edit_theme_options capability is used for viewing the page.
	 *
	 * By default, the options groups for all registered settings require the manage_options capability.
	 * By default, only administrators have either of these capabilities, but the desire here is
	 * to allow for finer-grained control for roles and users.
	 *
	 * @param string $capability The capability used for the page, which is manage_options by default.
	 * @return string The capability to actually use.
	 */
	public function option_page_capability() {
		return $this->capability;
	}

	/**
	 * Add our options page to the admin menu, including some help documentation.
	 *
	 * This function is attached to the admin_menu action hook.
	 *
	 * @since 1.0
	 */
	public function add_option_page() {
		load_plugin_textdomain( $this->textdomain, false, $this->languages_path );

		add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );

		$page_hook = add_theme_page(
			__( $this->page_title, $this->textdomain ),
			__( $this->menu_title, $this->textdomain ),
			$this->option_page_capability(),
			$this->menu_slug,
			array( $this, 'render_option_page' )
		);

		if ( ! $page_hook ) {
			return;
		}

		add_action( 'load-' . $page_hook , array( $this, 'page_hook_suffix' ) );
	}

	/**
	 * Page Hook Suffix
	 *
	 * This function is attached to the load-** action hook.
	 *
	 * @since 1.2.4
	 */
	public function page_hook_suffix() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ) );
	}

	/**
	 * Add the settings link to the plugin page.
	 *
	 * @since 1.2
	 */
	public function plugin_action_links( $links, $file ) {
		if ( plugin_basename( __FILE__ ) !== $file ) {
			return $links;
		}

		$settings_link = '<a href="themes.php?page=multi-device-switcher">' . __( 'Settings', $this->textdomain ) . '</a>';

		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Returns the default options.
	 *
	 * @since 1.0
	 */
	public function get_default_options() {
		$default_theme_options = array(
			'pc_switcher' => 1,
			'default_css' => 1,
			'theme_smartphone' => 'None',
			'theme_tablet' => 'None',
			'theme_mobile' => 'None',
			'theme_game' => 'None',
			'userAgent_smart' => 'iPhone, iPod, Android.*Mobile, dream, CUPCAKE, Windows Phone, IEMobile.*Touch, webOS, BB10.*Mobile, BlackBerry.*Mobile, Mobile.*Gecko',
			'userAgent_tablet' => 'iPad, Kindle, Silk, Android(?!.*Mobile), Windows.*Touch, PlayBook, Tablet.*Gecko',
			'userAgent_mobile' => 'DoCoMo, SoftBank, J-PHONE, Vodafone, KDDI, UP.Browser, WILLCOM, emobile, DDIPOCKET, Windows CE, BlackBerry, Symbian, PalmOS, Huawei, IAC, Nokia',
			'userAgent_game' => 'PlayStation Portable, PlayStation Vita, PSP, PS2, PLAYSTATION 3, PlayStation 4, Nitro, Nintendo 3DS, Nintendo Wii, Nintendo WiiU, Xbox',
			'disable_path' => '',
			'enable_regex' => 0,
		);

		return $default_theme_options;
	}

	/**
	 * Returns the options array.
	 *
	 * @since 1.0
	 */
	public function get_options() {
		$options = get_option( $this->option_name );
		$default_options = $this->get_default_options();

		if ( ! isset( $options['pc_switcher'] ) ) {
			$options['pc_switcher'] = $default_options['pc_switcher'];
		}
		if ( ! isset( $options['default_css'] ) ) {
			$options['default_css'] = $default_options['default_css'];
		}

		if ( ! isset( $options['theme_smartphone'] ) ) {
			$options['theme_smartphone'] = $default_options['theme_smartphone'];
		}
		if ( ! isset( $options['theme_tablet'] ) ) {
			$options['theme_tablet'] = $default_options['theme_tablet'];
		}
		if ( ! isset( $options['theme_mobile'] ) ) {
			$options['theme_mobile'] = $default_options['theme_mobile'];
		}
		if ( ! isset( $options['theme_game'] ) ) {
			$options['theme_game'] = $default_options['theme_game'];
		}

		if ( ! isset( $options['userAgent_smart'] ) ) {
			$options['userAgent_smart'] = $default_options['userAgent_smart'];
		}
		if ( ! isset( $options['userAgent_tablet'] ) ) {
			$options['userAgent_tablet'] = $default_options['userAgent_tablet'];
		}
		if ( ! isset( $options['userAgent_mobile'] ) ) {
			$options['userAgent_mobile'] = $default_options['userAgent_mobile'];
		}
		if ( ! isset( $options['userAgent_game'] ) ) {
			$options['userAgent_game'] = $default_options['userAgent_game'];
		}

		if ( ! isset( $options['disable_path'] ) ) {
			$options['disable_path'] = $default_options['disable_path'];
		}
		if ( ! isset( $options['enable_regex'] ) ) {
			$options['enable_regex'] = $default_options['enable_regex'];
		}

		return $options;
	}

	/**
	 * Rendering Options Setting Page.
	 *
	 * @since 1.0
	 */
	public function render_option_page() {
		?>
	<div class="wrap">
		<div id="icon-themes" class="icon32"><br></div>
		<h2><?php esc_html_e( 'Multi Device Switcher', $this->textdomain ); ?></h2>
		<?php settings_errors(); ?>

		<form method="post" action="options.php">
			<?php
				settings_fields( 'multi_device_switcher' );
				$options = $this->get_options();

				$default_theme = wp_get_theme()->get( 'Name' );
				$themes = wp_get_themes();
				$theme_names = array();

				if ( count( $themes ) ) {
					foreach ( $themes as $t ) {
						$theme_names[] = $t->get( 'Name' );
					}
					natcasesort( $theme_names );
				}
			?>

			<div id="admin-tabs">
			<fieldset id="Theme" class="options">
			<h3 class="label"><?php esc_html_e( 'Theme', $this->textdomain ); ?></h3>
			<table class="form-table">
				<tr><th scope="row"><?php esc_html_e( 'Smart Phone Theme', $this->textdomain ); ?></th>
					<td>

			<?php
				if ( count( $theme_names ) ) {
					$html = '<select name="multi_device_switcher_options[theme_smartphone]">';

					if ( ( 'None' === $options['theme_smartphone'] ) || ( '' === $options['theme_smartphone'] ) ) {
						$html .= '<option value="None" selected="selected">' . __( 'None', $this->textdomain ) . '</option>';
					}
					else {
						$html .= '<option value="None">' . __( 'None', $this->textdomain ) . '</option>';
					}

					foreach ( $theme_names as $theme_name ) {
						if ( $default_theme === $theme_name ) {
							continue;
						}
						if ( $options['theme_smartphone'] === $theme_name ) {
							$html .= '<option value="' . esc_attr( $theme_name ) . '" selected="selected">' . esc_html( $theme_name ) . '</option>';
						}
						else {
							$html .= '<option value="' . esc_attr( $theme_name ) . '">' . esc_html( $theme_name ) . '</option>';
						}
					}
					$html .= '</select>';
				}
				echo $html;
			?>
					</td>
				</tr>
				<tr><th scope="row"><?php esc_html_e( 'Tablet PC Theme', $this->textdomain ); ?></th>
					<td>

			<?php
				if ( count( $theme_names ) ) {
					$html = '<select name="multi_device_switcher_options[theme_tablet]">';

					if ( ( 'None' === $options['theme_tablet'] ) || ( '' === $options['theme_tablet'] ) ) {
						$html .= '<option value="None" selected="selected">' . __( 'None', $this->textdomain ) . '</option>';
					}
					else {
						$html .= '<option value="None">' . __( 'None', $this->textdomain ) . '</option>';
					}

					foreach ( $theme_names as $theme_name ) {
						if ( $default_theme === $theme_name ) {
							continue;
						}
						if ( $options['theme_tablet'] === $theme_name ) {
							$html .= '<option value="' . esc_attr( $theme_name ) . '" selected="selected">' . esc_html( $theme_name ) . '</option>';
						}
						else {
							$html .= '<option value="' . esc_attr( $theme_name ) . '">' . esc_html( $theme_name ) . '</option>';
						}
					}
					$html .= '</select>';
				}
				echo $html;
			?>
					</td>
				</tr>
				<tr><th scope="row"><?php esc_html_e( 'Mobile Phone Theme', $this->textdomain ); ?></th>
					<td>

			<?php
				if ( count( $theme_names ) ) {
					$html = '<select name="multi_device_switcher_options[theme_mobile]">';

					if ( ( 'None' === $options['theme_mobile'] ) || ( '' === $options['theme_mobile'] ) ) {
						$html .= '<option value="None" selected="selected">' . __( 'None', $this->textdomain ) . '</option>';
					}
					else {
						$html .= '<option value="None">' . __( 'None', $this->textdomain ) . '</option>';
					}

					foreach ( $theme_names as $theme_name ) {
						if ( $default_theme === $theme_name ) {
							continue;
						}
						if ( $options['theme_mobile'] === $theme_name ) {
							$html .= '<option value="' . esc_attr( $theme_name ) . '" selected="selected">' . esc_html( $theme_name ) . '</option>';
						}
						else {
							$html .= '<option value="' . esc_attr( $theme_name ) . '">' . esc_html( $theme_name ) . '</option>';
						}
					}
					$html .= '</select>';
				}
				echo $html;
			?>
					</td>
				</tr>
				<tr><th scope="row"><?php esc_html_e( 'Game Platforms Theme', $this->textdomain ); ?></th>
					<td>

			<?php
				if ( count( $theme_names ) ) {
					$html = '<select name="multi_device_switcher_options[theme_game]">';

					if ( ( 'None' === $options['theme_game'] ) || ( '' === $options['theme_game'] ) ) {
						$html .= '<option value="None" selected="selected">' . __( 'None', $this->textdomain ) . '</option>';
					}
					else {
						$html .= '<option value="None">' . __( 'None', $this->textdomain ) . '</option>';
					}

					foreach ( $theme_names as $theme_name ) {
						if ( $default_theme === $theme_name ) {
							continue;
						}
						if ( $options['theme_game'] === $theme_name ) {
							$html .= '<option value="' . esc_attr( $theme_name ) . '" selected="selected">' . esc_html( $theme_name ) . '</option>';
						}
						else {
							$html .= '<option value="' . esc_attr( $theme_name ) . '">' . esc_html( $theme_name ) . '</option>';
						}
					}
					$html .= '</select>';
				}
				echo $html;
			?>
					</td>
				</tr>
			</table>

			<h3><?php esc_html_e( 'Custom Switcher Theme', $this->textdomain ); ?></h3>
			<table class="form-table">

			<?php
				foreach ( $options as $custom_switcher_option => $custom_switcher_theme ) {
					if ( ! preg_match( '/^custom_switcher_theme_/', $custom_switcher_option ) ) {
						continue;
					}

					$custom_switcher_name = preg_replace( '/^custom_switcher_theme_/', '', $custom_switcher_option );
			?>

				<tr><th scope="row"><?php echo esc_html( $custom_switcher_name ); ?></th>
					<td>

			<?php
				if ( count( $theme_names ) ) {
					$html = '<select name="multi_device_switcher_options[' . $custom_switcher_option . ']">';

					if ( ( 'None' === $custom_switcher_theme ) || ( '' === $custom_switcher_theme ) ) {
						$html .= '<option value="None" selected="selected">' . __( 'None', $this->textdomain ) . '</option>';
					}
					else {
						$html .= '<option value="None">' . __( 'None', $this->textdomain ) . '</option>';
					}

					foreach ( $theme_names as $theme_name ) {
						if ( $default_theme === $theme_name ) {
							continue;
						}
						if ( $custom_switcher_theme === $theme_name ) {
							$html .= '<option value="' . esc_attr( $theme_name ) . '" selected="selected">' . esc_html( $theme_name ) . '</option>';
						}
						else {
							$html .= '<option value="' . esc_attr( $theme_name ) . '">' . esc_html( $theme_name ) . '</option>';
						}
					}
					$html .= '</select>';
					$html .= ' <span class="submit"><input type="submit" name="multi_device_switcher_options[delete_custom_switcher_' . $custom_switcher_name . ']" value="' . __( 'Delete', $this->textdomain ) . '" onclick="return confirm(\'' . esc_html( sprintf( __( 'Are you sure you want to delete %1$s ?', $this->textdomain ), $custom_switcher_name ) ) . '\');" class="button"></span>';
				}
				echo $html;
			?>
					</td>
				</tr>

			<?php
				}
			?>

				<tr><th scope="row"><?php esc_html_e( 'Add Custom Switcher', $this->textdomain ); ?></th>
					<td>
						<legend class="screen-reader-text"><span><?php esc_html_e( 'Add Custom Switcher', $this->textdomain ); ?></span></legend>
							<input type="text" name="multi_device_switcher_options[custom_switcher]" id="custom-switcher" value="" size="24">
							<span class="submit"><input type="submit" name="multi_device_switcher_options[add_custom_switcher]" value="<?php esc_html_e( 'Add', $this->textdomain ); ?>" class="button"></span><br>
							<?php esc_html_e( '20 characters max, alphanumeric', $this->textdomain ); ?>
					</td>
				</tr>
			</table>

			</fieldset>

			<fieldset id="UserAgent" class="options">
			<h3 class="label"><?php esc_html_e( 'UserAgent', $this->textdomain ); ?></h3>
			<p><?php esc_html_e( 'Enter Comma-separated values (csv) format.', $this->textdomain ); ?></p>

			<table class="form-table">
				<tr><th scope="row"><?php esc_html_e( 'Smart Phone', $this->textdomain ); ?></th>
					<td><textarea name="multi_device_switcher_options[userAgent_smart]" rows="4" cols="42"><?php echo esc_textarea( $options['userAgent_smart'] ); ?></textarea></td>
				</tr>
				<tr><th scope="row"><?php esc_html_e( 'Tablet PC', $this->textdomain ); ?></th>
					<td><textarea name="multi_device_switcher_options[userAgent_tablet]" rows="4" cols="42"><?php echo esc_textarea( $options['userAgent_tablet'] ); ?></textarea></td>
				</tr>
				<tr><th scope="row"><?php esc_html_e( 'Mobile Phone', $this->textdomain ); ?></th>
					<td><textarea name="multi_device_switcher_options[userAgent_mobile]" rows="4" cols="42"><?php echo esc_textarea( $options['userAgent_mobile'] ); ?></textarea></td>
				</tr>
				<tr><th scope="row"><?php esc_html_e( 'Game Platforms', $this->textdomain ); ?></th>
					<td><textarea name="multi_device_switcher_options[userAgent_game]" rows="4" cols="42"><?php echo esc_textarea( $options['userAgent_game'] ); ?></textarea></td>
				</tr>
				<tr><th></th>
					<td><span class="submit"><input type="submit" name="multi_device_switcher_options[restore_UserAgent]" value="<?php esc_html_e( 'Reset Settings to Default UserAgent', $this->textdomain ); ?>" class="button"></span></td>
				</tr>

			</table>

			<h3><?php esc_html_e( 'Custom Switcher UserAgent', $this->textdomain ); ?></h3>
			<table class="form-table">
			<?php
				foreach ( $options as $custom_switcher_option => $custom_switcher_userAgent ) {
					if ( ! preg_match( '/^custom_switcher_userAgent_/', $custom_switcher_option ) ) {
						continue;
					}

					$custom_switcher_name = preg_replace( '/^custom_switcher_userAgent_/', '', $custom_switcher_option );
			?>

				<tr><th scope="row"><?php echo esc_html( $custom_switcher_name ); ?></th>
					<td><textarea name="multi_device_switcher_options[<?php echo esc_attr( $custom_switcher_option ); ?>]" rows="4" cols="42"><?php echo esc_textarea( $custom_switcher_userAgent ); ?></textarea></td>
				</tr>
			<?php
				}
			?>

			</table>
			</fieldset>

			<fieldset id="PC-Switcher" class="options">
			<h3 class="label"><?php esc_html_e( 'PC Switcher', $this->textdomain ); ?></h3>

			<table class="form-table">
				<tr><th scope="row"><?php esc_html_e( 'Add PC Switcher', $this->textdomain ); ?></th>
					<td>
						<legend class="screen-reader-text"><span><?php esc_html_e( 'Add PC Switcher', $this->textdomain ); ?></span></legend>
							<label><input type="checkbox" name="multi_device_switcher_options[pc_switcher]" id="pc-switcher" value="1"<?php checked( 1, $options['pc_switcher'] ); ?>> <?php esc_html_e( 'Add a PC Switcher to the footer.', $this->textdomain ); ?></label>
					</td>
				</tr>
				<tr><th scope="row"><?php esc_html_e( 'Add default CSS', $this->textdomain ); ?></th>
					<td>
						<legend class="screen-reader-text"><span><?php esc_html_e( 'Add default CSS', $this->textdomain ); ?></span></legend>
							<label><input type="checkbox" name="multi_device_switcher_options[default_css]" id="add-default-css" value="1"<?php checked( 1, $options['default_css'] ); ?>> <?php esc_html_e( 'Add a default CSS.', $this->textdomain ); ?></label>
					</td>
				</tr>
			</table>
			</fieldset>

			<fieldset id="Disable-Switcher" class="options">
			<h3 class="label"><?php esc_html_e( 'Disable Switcher', $this->textdomain ); ?></h3>

			<table class="form-table">
				<tr><th scope="row"><?php esc_html_e( 'Path', $this->textdomain ); ?></th>
					<td>
						<legend class="screen-reader-text"><span><?php esc_html_e( 'Path', $this->textdomain ); ?></span></legend>
							<?php echo esc_html( home_url() ); ?><br>
							<textarea name="multi_device_switcher_options[disable_path]" rows="16" cols="42" wrap="off"><?php echo esc_textarea( $options['disable_path'] ); ?></textarea>
					</td>
				</tr>
				<tr><th scope="row"><?php esc_html_e( 'Regex mode', $this->textdomain ); ?></th>
					<td>
						<legend class="screen-reader-text"><span><?php esc_html_e( 'Regex mode', $this->textdomain ); ?></span></legend>
							<label><input type="checkbox" name="multi_device_switcher_options[enable_regex]" id="enable-regex" value="1"<?php checked( 1, $options['enable_regex'] ); ?>> <?php esc_html_e( 'Enable Regex', $this->textdomain ); ?></label>
					</td>
				</tr>
			</table>
			</fieldset>

			</div>
			<?php submit_button(); ?>
		</form>
	</div>

	<div id="donate">
	<h2><?php esc_html_e( 'Donationware', $this->textdomain ); ?></h2>
	<p><?php esc_html_e( 'If you like this plugin, please donate to support development and maintenance.', $this->textdomain ); ?></p>
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
	 * @see admin_init()
	 *
	 * @since 1.0
	 */
	public function validate( $input ) {
		$output = $default_options = $this->get_default_options();

		if ( isset( $input['theme_smartphone'] ) ) {
			$output['theme_smartphone'] = $input['theme_smartphone'];
		}
		if ( isset( $input['theme_tablet'] ) ) {
			$output['theme_tablet'] = $input['theme_tablet'];
		}
		if ( isset( $input['theme_mobile'] ) ) {
			$output['theme_mobile'] = $input['theme_mobile'];
		}
		if ( isset( $input['theme_game'] ) ) {
			$output['theme_game'] = $input['theme_game'];
		}

		if ( isset( $input['restore_UserAgent'] ) ) {
			$output['userAgent_smart'] = $default_options['userAgent_smart'];
			$output['userAgent_tablet'] = $default_options['userAgent_tablet'];
			$output['userAgent_mobile'] = $default_options['userAgent_mobile'];
			$output['userAgent_game'] = $default_options['userAgent_game'];
		}
		else {
			if ( isset( $input['userAgent_smart'] ) ) {
				$output['userAgent_smart'] = $input['userAgent_smart'];
			}
			if ( isset( $input['userAgent_tablet'] ) ) {
				$output['userAgent_tablet'] = $input['userAgent_tablet'];
			}
			if ( isset( $input['userAgent_mobile'] ) ) {
				$output['userAgent_mobile'] = $input['userAgent_mobile'];
			}
			if ( isset( $input['userAgent_game'] ) ) {
				$output['userAgent_game'] = $input['userAgent_game'];
			}
		}

		foreach ( $input as $key => $val ) {
			if ( ! preg_match( '/^custom_switcher_theme_/', $key ) ) {
				continue;
			}

			$custom_switcher_name = preg_replace( '/^custom_switcher_theme_/', '', $key );

			if ( isset( $input[ 'custom_switcher_theme_' . $custom_switcher_name ] ) ) {
				$output[ 'custom_switcher_theme_' . $custom_switcher_name ] = $input[ 'custom_switcher_theme_' . $custom_switcher_name ];
			}
			if ( isset( $input[ 'custom_switcher_userAgent_' . $custom_switcher_name ] ) ) {
				$output[ 'custom_switcher_userAgent_' . $custom_switcher_name ] = $input[ 'custom_switcher_userAgent_' . $custom_switcher_name ];
			}
		}

		foreach ( $input as $key => $val ) {
			if ( ! preg_match( '/^delete_custom_switcher_/', $key ) ) {
				continue;
			}

			$custom_switcher_name = preg_replace( '/^delete_custom_switcher_/', '', $key );

			unset( $output[ 'custom_switcher_theme_' . $custom_switcher_name ] );
			unset( $output[ 'custom_switcher_userAgent_' . $custom_switcher_name ] );
		}

		if ( isset( $input['add_custom_switcher'] ) && ! empty( $input['custom_switcher'] ) && ! $output[ 'custom_switcher_theme_' . $input['custom_switcher'] ] ) {
			if ( ! in_array( $input['custom_switcher'], array( 'smartphone', 'smart', 'tablet', 'mobile', 'game' ) )
					&& preg_match( '/^[A-Za-z0-9]{1,20}$/', $input['custom_switcher'] ) ) {
				$output[ 'custom_switcher_theme_' . $input['custom_switcher'] ] = 'None';
				$output[ 'custom_switcher_userAgent_' . $input['custom_switcher'] ] = '';
			}
		}

		$output['pc_switcher'] = isset( $input['pc_switcher'] ) ? $input['pc_switcher'] : 0;
		$output['default_css'] = isset( $input['default_css'] ) ? $input['default_css'] : 0;

		$output['disable_path'] = isset( $input['disable_path'] ) ? $input['disable_path'] : '';
		$output['enable_regex'] = isset( $input['enable_regex'] ) ? $input['enable_regex'] : 0;

		return apply_filters( 'multi_device_switcher_validate', $output, $input, $default_options );
	}

	/**
	 * plugin customization options
	 *
	 * @param $wp_customize Theme Customizer object
	 * @return void
	 *
	 * @since 1.3.1
	 */
	public function customize_register( $wp_customize ) {
		load_plugin_textdomain( $this->textdomain, false, $this->languages_path );
		$options = $this->get_options();
		$default_theme_options = $this->get_default_options();
		$default_theme = wp_get_theme()->get( 'Name' );
		$themes = wp_get_themes();

		$theme_names = array();
		$choices = array();

		if ( count( $themes ) ) {
			foreach ( $themes as $t ) {
				$theme_names[] = $t->get( 'Name' );
			}
			natcasesort( $theme_names );

			$choices['None'] = __( 'None', $this->textdomain );
			foreach ( $theme_names as $theme_name ) {
				if ( $default_theme === $theme_name ) {
					continue;
				}
				$choices[ $theme_name ] = $theme_name;
			}
		}

		$switcher = array(
			'theme_smartphone'  => __( 'Smart Phone Theme', $this->textdomain ),
			'theme_tablet'      => __( 'Tablet PC Theme', $this->textdomain ),
			'theme_mobile'      => __( 'Mobile Phone Theme', $this->textdomain ),
			'theme_game'        => __( 'Game Platforms Theme', $this->textdomain ),
		);

		$wp_customize->add_section( 'multi_device_switcher', array(
			'title'      => __( 'Multi Device Switcher', $this->textdomain ),
			'priority'   => 80,
		) );

		foreach ( $switcher as $name => $label ) {
			$wp_customize->add_setting( 'multi_device_switcher_options[' . $name . ']', array(
				'default'        => $default_theme_options[ $name ],
				'type'           => 'option',
				'capability'     => 'edit_theme_options',
			) );

			$wp_customize->add_control( 'multi_device_switcher_options[' . $name . ']', array(
				'label'      => $label,
				'section'    => 'multi_device_switcher',
				'type'       => 'select',
				'choices'    => $choices,
			) );
		}

		foreach ( $options as $custom_switcher_option => $custom_switcher_theme ) {
			if ( ! preg_match( '/^custom_switcher_theme_/', $custom_switcher_option ) ) {
				continue;
			}

			$label = preg_replace( '/^custom_switcher_theme_/', '', $custom_switcher_option );

			$wp_customize->add_setting( 'multi_device_switcher_options[' . $custom_switcher_option . ']', array(
				'default'       => __( 'None', $this->textdomain ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
			) );

			$wp_customize->add_control( 'multi_device_switcher_options[' . $custom_switcher_option . ']', array(
				'label'      => $label,
				'section'    => 'multi_device_switcher',
				'type'       => 'select',
				'choices'    => $choices,
			) );

		}
	}

	public function load_file() {
		/**
		 * include PC Switcher Widget.
		 *
		 * @since 1.2
		 *
		 */
		require_once( dirname( __FILE__ ) . '/pc-switcher-widget.php' );

		/**
		 * include Multi Device Switcher Command
		 *
		 * @since 1.4
		 *
		 */
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			require_once( dirname( __FILE__ ) . '/wp-cli.php' );
		}
	}
}

$multi_device_switcher = new Multi_Device_Switcher();

/**
 * Add PC Switcher.
 *
 * @since 1.2
 *
 */
function multi_device_switcher_add_pc_switcher() {
	global $multi_device_switcher;
	if ( is_object( $multi_device_switcher ) ) {
		$multi_device_switcher->add_pc_switcher( 1 );
	}
}

/**
 * Return boolean whether a particular device.
 *
 * @since 1.2.4
 *
 */
if ( ! function_exists( 'is_multi_device' ) ) :

function is_multi_device( $device = '' ) {
	global $multi_device_switcher;
	if ( is_object( $multi_device_switcher ) ) {
		return $multi_device_switcher->is_multi_device( $device );
	}
}
endif;

/**
 * Return the state of PC Switcher.
 *
 * @since 1.4.1
 *
 */
if ( ! function_exists( 'is_pc_switcher' ) ) :

function is_pc_switcher() {
	global $multi_device_switcher;
	if ( is_object( $multi_device_switcher ) ) {
		return $multi_device_switcher->is_pc_switcher();
	}
}
endif;

/**
 * Return the state of disabled.
 *
 * @since 1.4.1
 *
 */
if ( ! function_exists( 'is_disable_switcher' ) ) :

function is_disable_switcher() {
	global $multi_device_switcher;
	if ( is_object( $multi_device_switcher ) ) {
		return $multi_device_switcher->is_disable_switcher();
	}
}
endif;

?>