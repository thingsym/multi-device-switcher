<?php
/**
 * Plugin Name: Multi Device Switcher
 * Plugin URI:  https://github.com/thingsym/multi-device-switcher
 * Description: This WordPress plugin allows you to set a separate theme for device (Smart Phone, Tablet PC, Mobile Phone, Game and custom).
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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Core class Multi_Device_Switcher
 *
 * @since 1.0.0
 */
class Multi_Device_Switcher {

	/**
	 * Protected value.
	 *
	 * @access protected
	 *
	 * @var string $option_group   The group name of option
	 */
	protected $option_group = 'multi_device_switcher';

	/**
	 * Protected value.
	 *
	 * @access protected
	 *
	 * @var string $option_name   The option name
	 */
	protected $option_name = 'multi_device_switcher_options';

	/**
	 * Protected value.
	 *
	 * @access protected
	 *
	 * @var string $capability   The types of capability
	 */
	protected $capability = 'switch_themes';

	/**
	 * Protected value.
	 *
	 * @access protected
	 *
	 * @var string $cookie_name_multi_device_switcher
	 */
	protected $cookie_name_multi_device_switcher = 'multi-device-switcher';

	/**
	 * Protected value.
	 *
	 * @access protected
	 *
	 * @var string $cookie_name_disable_switcher
	 */
	protected $cookie_name_disable_switcher = 'disable-switcher';

	/**
	 * Protected value.
	 *
	 * @access protected
	 *
	 * @var string $cookie_name_pc_switcher
	 */
	protected $cookie_name_pc_switcher = 'pc-switcher';

	/**
	 * Protected value.
	 *
	 * @access protected
	 *
	 * @var array $default_options {
	 *   default options
	 *
	 *   @type bool pc_switcher
	 *   @type bool default_css
	 *   @type string theme_smartphone
	 *   @type string theme_tablet
	 *   @type string theme_mobile
	 *   @type string theme_game
	 *   @type string userAgent_smart
	 *   @type string userAgent_tablet
	 *   @type string userAgent_mobile
	 *   @type string userAgent_game
	 *   @type string disable_path
	 *   @type bool enable_regex
	 * }
	 *
	 * @since 1.7.0
	 */
	protected $default_options = array(
		'pc_switcher'      => 1,
		'default_css'      => 1,
		'theme_smartphone' => 'None',
		'theme_tablet'     => 'None',
		'theme_mobile'     => 'None',
		'theme_game'       => 'None',
		'userAgent_smart'  => 'iPhone, iPod, Android.*Mobile, dream, CUPCAKE, Windows Phone, IEMobile.*Touch, webOS, BB10.*Mobile, BlackBerry.*Mobile, Mobile.*Gecko',
		'userAgent_tablet' => 'iPad, Kindle, Silk, Android(?!.*Mobile), Windows.*Touch, PlayBook, Tablet.*Gecko',
		'userAgent_mobile' => 'DoCoMo, SoftBank, J-PHONE, Vodafone, KDDI, UP.Browser, WILLCOM, emobile, DDIPOCKET, Windows CE, BlackBerry, Symbian, PalmOS, Huawei, IAC, Nokia',
		'userAgent_game'   => 'PlayStation Portable, PlayStation Vita, PSP, PS2, PLAYSTATION 3, PlayStation 4, Nitro, Nintendo 3DS, Nintendo Wii, Nintendo WiiU, Xbox',
		'disable_path'     => '',
		'enable_regex'     => 0,
	);

	/**
	 * Public value.
	 *
	 * @access public
	 *
	 * @var string $device
	 */
	public $device = '';

	/**
	 * Constructor
	 *
	 * @access public
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'init' ) );

		if ( ! is_admin() ) {
			add_filter( 'wp_headers', array( $this, 'add_header_vary' ) );
			add_action( 'plugins_loaded', array( $this, 'detect_device' ) );
			add_action( 'plugins_loaded', array( $this, 'switch_theme' ) );
		}

		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_option_page' ) );
		add_action( 'customize_register', array( $this, 'customize_register' ) );
		add_action( 'plugins_loaded', array( $this, 'load_file' ) );
	}

	/**
	 * Initialize.
	 *
	 * Hooks to init
	 *
	 * @access public
	 *
	 * @since 1.6.0
	 */
	public function init() {
		add_filter( 'option_page_capability_' . $this->option_group, array( $this, 'option_page_capability' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __MULTI_DEVICE_SWITCHER_FILE__ ), array( $this, 'plugin_action_links' ) );

		add_shortcode( 'multi', array( $this, 'shortcode_display_switcher' ) );
	}

	/**
	 * Detect device.
	 *
	 * @access public
	 *
	 * @return void
	 *
	 * @since 1.7.0
	 */
	public function detect_device() {
		if ( isset( $_COOKIE[ $this->cookie_name_disable_switcher ] ) ) {
			add_action( 'wp_headers', array( $this, 'set_cookie_rest_disable_switcher' ) );
		}

		if ( $this->is_disable_switcher() ) {
			add_action( 'wp_headers', array( $this, 'set_cookie_enable_disable_switcher' ) );
			return;
		}

		add_action( 'init', array( $this, 'session' ) );

		$server_ua  = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$user_agent = $this->get_options_user_agent();

		foreach ( array_reverse( $user_agent ) as $key => $val ) {
			if ( ! preg_match( '/^custom_switcher_/', $key ) ) {
				continue;
			}
			if ( $user_agent[ $key ] && preg_match( '/' . implode( '|', $user_agent[ $key ] ) . '/i', $server_ua ) ) {
				$this->device = $key;
				break;
			}
		}

		if ( ! $this->device ) {
			if ( $user_agent['game'] && preg_match( '/' . implode( '|', $user_agent['game'] ) . '/i', $server_ua ) ) {
				$this->device = 'game';
			}
			elseif ( $user_agent['tablet'] && preg_match( '/' . implode( '|', $user_agent['tablet'] ) . '/i', $server_ua ) ) {
				$this->device = 'tablet';
			}
			elseif ( $user_agent['smart'] && preg_match( '/' . implode( '|', $user_agent['smart'] ) . '/i', $server_ua ) ) {
				$this->device = 'smart';
			}
			elseif ( $user_agent['mobile'] && preg_match( '/' . implode( '|', $user_agent['mobile'] ) . '/i', $server_ua ) ) {
				$this->device = 'mobile';
			}
		}

		/**
		 * Action hook: multi_device_switcher/detect_device.
		 *
		 * @since 1.7.0
		 */
		do_action( 'multi_device_switcher/detect_device' );
	}

	/**
	 * Switch theme.
	 *
	 * @access public
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function switch_theme() {
		if ( $this->device ) {
			add_filter( 'stylesheet', array( $this, 'get_stylesheet' ) );
			add_filter( 'template', array( $this, 'get_template' ) );
			add_action( 'wp_footer', array( $this, 'add_pc_switcher' ) );
			add_action( 'wp_headers', array( $this, 'set_cookie_switch_theme' ) );
		}
		else {
			add_action( 'wp_headers', array( $this, 'set_cookie_normal_theme' ) );
		}

		if ( isset( $_COOKIE[ $this->cookie_name_pc_switcher ] ) ) {
			remove_filter( 'stylesheet', array( $this, 'get_stylesheet' ) );
			remove_filter( 'template', array( $this, 'get_template' ) );
		}
	}

	/**
	 * Gets UserAgents.
	 *
	 * @access public
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function get_options_user_agent() {
		$options = $this->get_options();

		$user_agent['smart']  = empty( $options['userAgent_smart'] ) ? '' : preg_split( '/,\s*/', $options['userAgent_smart'], -1, PREG_SPLIT_NO_EMPTY );
		$user_agent['tablet'] = empty( $options['userAgent_tablet'] ) ? '' : preg_split( '/,\s*/', $options['userAgent_tablet'], -1, PREG_SPLIT_NO_EMPTY );
		$user_agent['mobile'] = empty( $options['userAgent_mobile'] ) ? '' : preg_split( '/,\s*/', $options['userAgent_mobile'], -1, PREG_SPLIT_NO_EMPTY );
		$user_agent['game']   = empty( $options['userAgent_game'] ) ? '' : preg_split( '/,\s*/', $options['userAgent_game'], -1, PREG_SPLIT_NO_EMPTY );

		foreach ( (array) $options as $key => $val ) {
			if ( ! preg_match( '/^custom_switcher_userAgent_/', $key ) ) {
				continue;
			}

			$custom_switcher_name = preg_replace( '/^custom_switcher_userAgent_/', '', $key );

			$user_agent[ 'custom_switcher_' . $custom_switcher_name ] = empty( $val ) ? '' : preg_split( '/,\s*/', $val, -1, PREG_SPLIT_NO_EMPTY );
		}

		return $user_agent;
	}

	/**
	 * Gets stylesheet.
	 *
	 * @access public
	 *
	 * @param string $stylesheet
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
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

	/**
	 * Gets template.
	 *
	 * @access public
	 *
	 * @param string $template
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
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

	/**
	 * Gets template by device.
	 *
	 * @access public
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
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
			foreach ( (array) $options as $key => $val ) {
				if ( ! preg_match( '/^custom_switcher_theme_/', $key ) ) {
					continue;
				}

				$custom_switcher_name = preg_replace( '/^custom_switcher_theme_/', '', $key );

				if ( 'custom_switcher_' . $custom_switcher_name === $this->device ) {
					return $options[ $key ];
				}
			}
		}

		return '';
	}

	/**
	 * Reset cookie for disable_switcher.
	 *
	 * @access public
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function set_cookie_rest_disable_switcher() {
		setcookie( $this->cookie_name_disable_switcher, '', time() - 3600, '/', '', is_ssl(), false );
	}

	/**
	 * Set enable to cookie for disable_switcher.
	 *
	 * @access public
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function set_cookie_enable_disable_switcher() {
		setcookie( $this->cookie_name_multi_device_switcher, '', time() - 3600, '/', '', is_ssl(), false );
		setcookie( $this->cookie_name_disable_switcher, '1', 0, '/', '', is_ssl(), false );
	}

	/**
	 * Set switched theme to cookie.
	 *
	 * @access public
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function set_cookie_switch_theme() {
		$device = preg_replace( '/^custom_switcher_/', '', $this->device );
		setcookie( $this->cookie_name_multi_device_switcher, (string) $device, 0, '/', '', is_ssl(), false );
	}

	/**
	 * Reset cookie for normal theme.
	 *
	 * @access public
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function set_cookie_normal_theme() {
		setcookie( $this->cookie_name_multi_device_switcher, '', time() - 3600, '/', '', is_ssl(), false );
	}

	/**
	 * Set session to cookie for pc switcher.
	 *
	 * @access public
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function session() {
		if ( isset( $_GET['pc-switcher'] ) ) {
			setcookie( $this->cookie_name_pc_switcher, $_GET['pc-switcher'] ? '1' : '', 0, '/', '', is_ssl(), false );

			$uri = preg_replace( '/^(.+?)(\?.*)$/', '$1', $_SERVER['REQUEST_URI'] );

			unset( $_GET['pc-switcher'] );
			if ( ! empty( $_GET ) ) {
				$uri = $uri . '?' . http_build_query( $_GET );
			}

			wp_redirect( esc_url( $uri ) );
			exit;
		}
	}

	/**
	 * Add pc switcher button.
	 *
	 * @access public
	 *
	 * @param bool $pc_switcher
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function add_pc_switcher( $pc_switcher = 0 ) {
		$options = $this->get_options();
		$name    = $this->get_device_theme();

		if ( $options['pc_switcher'] ) {
			$pc_switcher = 1;
		}

		if ( $pc_switcher && $name && 'None' !== $name ) {
			if ( $options['default_css'] ) {
				wp_enqueue_style(
					'pc-switcher-options',
					plugins_url() . '/multi-device-switcher/pc-switcher.css',
					array(),
					'2013-03-20'
				);
			}

			$uri  = is_ssl() ? 'https://' : 'http://';
			$uri .= $_SERVER['HTTP_HOST'];

			if ( isset( $_COOKIE[ $this->cookie_name_pc_switcher ] ) ) {
				$uri .= add_query_arg( 'pc-switcher', 0 );
				?>
<div class="pc-switcher"><a href="<?php echo esc_url( $uri ); ?>"><?php esc_html_e( 'Mobile', 'multi-device-switcher' ); ?></a><span class="active"><?php esc_html_e( 'PC', 'multi-device-switcher' ); ?></span></div>
				<?php
			}
			else {
				$uri .= add_query_arg( 'pc-switcher', 1 );
				?>
<div class="pc-switcher"><span class="active"><?php esc_html_e( 'Mobile', 'multi-device-switcher' ); ?></span><a href="<?php echo esc_url( $uri ); ?>"><?php esc_html_e( 'PC', 'multi-device-switcher' ); ?></a></div>
				<?php
			}
		}
	}

	/**
	 * Check device.
	 *
	 * @access public
	 *
	 * @param string $device
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function is_multi_device( $device = '' ) {
		if ( $device === $this->device ) {
			return true;
		}
		if ( 'custom_switcher_' . $device === $this->device ) {
			return true;
		}

		return false;
	}

	/**
	 * Whether pc switcher enabled.
	 *
	 * @access public
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function is_pc_switcher() {
		return isset( $_COOKIE[ $this->cookie_name_pc_switcher ] );
	}

	/**
	 * Whether theme switch disabled.
	 *
	 * @access public
	 *
	 * @param bool $disable
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function is_disable_switcher( $disable = false ) {
		$options      = $this->get_options();
		$disable_path = preg_split( '/\R/', $options['disable_path'], -1, PREG_SPLIT_NO_EMPTY );

		foreach ( (array) $disable_path as $path ) {
			if ( $options['enable_regex'] ) {
				if ( preg_match( '/' . $path . '/i', $_SERVER['REQUEST_URI'] ) ) {
					$disable = true;
					break;
				}
			}
			else {
				if ( preg_match( '/^' . preg_quote( (string) $path, '/' ) . '$/i', $_SERVER['REQUEST_URI'] ) ) {
					$disable = true;
					break;
				}
			}
		}

		return $disable;
	}

	/**
	 * Shortcode.
	 *
	 * @access public
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function shortcode_display_switcher( $atts, $content = '' ) {
		$atts = shortcode_atts(
			array(
				'device' => '',
			),
			$atts
		);

		if ( empty( $atts['device'] ) && ( $this->is_multi_device( $atts['device'] ) || $this->is_pc_switcher() ) ) {
			return $content;
		}
		elseif ( ! empty( $atts['device'] ) && $this->is_multi_device( $atts['device'] ) && ! $this->is_pc_switcher() ) {
			return $content;
		}

		return '';
	}

	/**
	 * Add HTTP Vary header.
	 *
	 * @access public
	 *
	 * @param string $headers
	 *
	 * @return string
	 *
	 * @since 1.1.1
	 */
	public function add_header_vary( $headers ) {
		/**
		 * Filter hook: multi_device_switcher/add_header_vary.
		 *
		 * @param string    'User-Agent'     header name.
		 *
		 * @since 1.6.2
		 */
		$headers['Vary'] = apply_filters( 'multi_device_switcher/add_header_vary', 'User-Agent' );
		return $headers;
	}

	/**
	 * Enqueue scripts.
	 *
	 * Hooks to admin_enqueue_scripts.
	 *
	 * @access public
	 *
	 * @since 1.0.0
	 */
	public function admin_enqueue_scripts( $hook_suffix ) {
		wp_enqueue_script(
			'multi-device-switcher-options',
			plugins_url() . '/multi-device-switcher/multi-device-switcher.js',
			array( 'jquery', 'jquery-ui-tabs' ),
			'2011-08-22'
		);
	}

	/**
	 * Enqueue styles.
	 *
	 * Hooks to admin_enqueue_styles.
	 *
	 * @access public
	 *
	 * @since 1.0.0
	 */
	public function admin_enqueue_styles( $hook_suffix ) {
		wp_enqueue_style(
			'multi-device-switcher-options',
			plugins_url() . '/multi-device-switcher/multi-device-switcher.css',
			array(),
			'2011-08-22'
		);
	}

	/**
	 * Register the form setting.
	 *
	 * Hooks to admin_init.
	 *
	 * @access public
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public function register_settings() {
		if ( is_null( $this->get_options() ) ) {
			add_option( $this->option_name );
		}

		register_setting(
			$this->option_group,
			$this->option_name,
			array( $this, 'validate_options' )
		);
	}

	/**
	 * Returns capability.
	 *
	 * @access public
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function option_page_capability() {
		return $this->capability;
	}

	/**
	 * Adds option page.
	 *
	 * @access public
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function add_option_page() {
		$page_hook = add_theme_page(
			__( 'Multi Device Switcher', 'multi-device-switcher' ),
			__( 'Multi Device Switcher', 'multi-device-switcher' ),
			$this->option_page_capability(),
			'multi-device-switcher',
			array( $this, 'render_option_page' )
		);

		if ( ! $page_hook ) {
			return;
		}

		add_action( 'load-' . $page_hook, array( $this, 'page_hook_suffix' ) );
	}

	/**
	 * Page Hook Suffix.
	 *
	 * Hooks to load-{$page_hook}.
	 *
	 * @access public
	 *
	 * @return void
	 *
	 * @since 1.2.4
	 */
	public function page_hook_suffix() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ) );
	}

	/**
	 * Set link to customizer section on the plugins page.
	 *
	 * Hooks to plugin_action_links_{$plugin_file}
	 *
	 * @see https://developer.wordpress.org/reference/hooks/plugin_action_links_plugin_file/
	 *
	 * @access public
	 *
	 * @param array $links An array of plugin action links.
	 *
	 * @return array $links
	 *
	 * @since 1.6.0
	 */
	public function plugin_action_links( $links = array() ) {
		$settings_link = '<a href="themes.php?page=multi-device-switcher">' . __( 'Settings', 'multi-device-switcher' ) . '</a>';

		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Returns the default options.
	 *
	 * @access public
	 *
	 * @return array|null
	 *
	 * @since 1.0.0
	 */
	public function get_default_options() {
		return $this->default_options;
	}

	/**
	 * Returns the options array or value.
	 *
	 * @access public
	 *
	 * @param string $option_name Optional. The option name.
	 *
	 * @return array|null
	 *
	 * @since 1.0.0
	 */
	public function get_options( $option_name = null ) {
		$options = get_option( $this->option_name, $this->default_options );
		$options = array_merge( $this->default_options, $options );

		if ( is_null( $option_name ) ) {
			/**
			 * Filter hook: multi_device_switcher/get_options.
			 *
			 * @param array    $options     The options.
			 *
			 * @since 1.7.0
			 */
			return apply_filters( 'multi_device_switcher/get_options', $options );
		}

		if ( array_key_exists( $option_name, $options ) ) {
			/**
			 * Filter hook: multi_device_switcher/get_option.
			 *
			 * @param mixed    $option           The value of option.
			 * @param string   $option_name      The option name via argument.
			 *
			 * @since 1.7.0
			 */
			return apply_filters( 'multi_device_switcher/get_option', $options[ $option_name ], $option_name );
		}

		return null;
	}

	/**
	 * Load textdomain
	 *
	 * @access public
	 *
	 * @return void
	 *
	 * @since 1.6.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'multi-device-switcher',
			false,
			dirname( plugin_basename( __MULTI_DEVICE_SWITCHER_FILE__ ) ) . '/languages/'
		);
	}

	/**
	 * Display option page.
	 *
	 * @access public
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function render_option_page() {
		?>
<div class="wrap">
<div id="icon-themes" class="icon32"><br></div>
<h2><?php esc_html_e( 'Multi Device Switcher', 'multi-device-switcher' ); ?></h2>
		<?php settings_errors(); ?>

<form method="post" action="options.php">
		<?php
		settings_fields( 'multi_device_switcher' );
		$options = $this->get_options();

		$default_theme = wp_get_theme()->get( 'Name' );
		$themes        = wp_get_themes();
		$theme_names   = array();

		if ( count( $themes ) ) {
			foreach ( $themes as $t ) {
				$theme_names[] = $t->get( 'Name' );
			}
			natcasesort( $theme_names );
		}
		?>

<div id="admin-tabs">
<fieldset id="Theme" class="options">
<h3 class="label"><?php esc_html_e( 'Theme', 'multi-device-switcher' ); ?></h3>
<table class="form-table">
	<tr><th scope="row"><?php esc_html_e( 'Smart Phone Theme', 'multi-device-switcher' ); ?></th>
		<td>

		<?php
		$html = '';
		if ( count( $theme_names ) ) {
			$html = '<select name="multi_device_switcher_options[theme_smartphone]">';

			if ( ( 'None' === $options['theme_smartphone'] ) || ( '' === $options['theme_smartphone'] ) ) {
				$html .= '<option value="None" selected="selected">' . __( 'None', 'multi-device-switcher' ) . '</option>';
			}
			else {
				$html .= '<option value="None">' . __( 'None', 'multi-device-switcher' ) . '</option>';
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
	<tr><th scope="row"><?php esc_html_e( 'Tablet PC Theme', 'multi-device-switcher' ); ?></th>
		<td>

		<?php
		if ( count( $theme_names ) ) {
			$html = '<select name="multi_device_switcher_options[theme_tablet]">';

			if ( ( 'None' === $options['theme_tablet'] ) || ( '' === $options['theme_tablet'] ) ) {
				$html .= '<option value="None" selected="selected">' . __( 'None', 'multi-device-switcher' ) . '</option>';
			}
			else {
				$html .= '<option value="None">' . __( 'None', 'multi-device-switcher' ) . '</option>';
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
	<tr><th scope="row"><?php esc_html_e( 'Mobile Phone Theme', 'multi-device-switcher' ); ?></th>
		<td>

		<?php
		if ( count( $theme_names ) ) {
			$html = '<select name="multi_device_switcher_options[theme_mobile]">';

			if ( ( 'None' === $options['theme_mobile'] ) || ( '' === $options['theme_mobile'] ) ) {
				$html .= '<option value="None" selected="selected">' . __( 'None', 'multi-device-switcher' ) . '</option>';
			}
			else {
				$html .= '<option value="None">' . __( 'None', 'multi-device-switcher' ) . '</option>';
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
	<tr><th scope="row"><?php esc_html_e( 'Game Platforms Theme', 'multi-device-switcher' ); ?></th>
		<td>

		<?php
		if ( count( $theme_names ) ) {
			$html = '<select name="multi_device_switcher_options[theme_game]">';

			if ( ( 'None' === $options['theme_game'] ) || ( '' === $options['theme_game'] ) ) {
				$html .= '<option value="None" selected="selected">' . __( 'None', 'multi-device-switcher' ) . '</option>';
			}
			else {
				$html .= '<option value="None">' . __( 'None', 'multi-device-switcher' ) . '</option>';
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

<h3><?php esc_html_e( 'Custom Switcher Theme', 'multi-device-switcher' ); ?></h3>
<table class="form-table">

		<?php
		foreach ( (array) $options as $custom_switcher_option => $custom_switcher_theme ) {
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
					$html .= '<option value="None" selected="selected">' . __( 'None', 'multi-device-switcher' ) . '</option>';
				}
				else {
					$html .= '<option value="None">' . __( 'None', 'multi-device-switcher' ) . '</option>';
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
				$html .= ' <span class="submit"><input type="submit" name="multi_device_switcher_options[delete_custom_switcher_' . $custom_switcher_name . ']" value="' . __( 'Delete', 'multi-device-switcher' ) . '" onclick="return confirm(\'' . esc_html( sprintf( __( 'Are you sure you want to delete %1$s ?', 'multi-device-switcher' ), $custom_switcher_name ) ) . '\');" class="button"></span>';
			}
			echo $html;
			?>
		</td>
	</tr>

			<?php
		}
		?>

	<tr><th scope="row"><?php esc_html_e( 'Add Custom Switcher', 'multi-device-switcher' ); ?></th>
		<td>
			<legend class="screen-reader-text"><span><?php esc_html_e( 'Add Custom Switcher', 'multi-device-switcher' ); ?></span></legend>
				<input type="text" name="multi_device_switcher_options[custom_switcher]" id="custom-switcher" value="" size="24">
				<span class="submit"><input type="submit" name="multi_device_switcher_options[add_custom_switcher]" value="<?php esc_html_e( 'Add', 'multi-device-switcher' ); ?>" class="button"></span><br>
				<?php esc_html_e( '20 characters max, alphanumeric', 'multi-device-switcher' ); ?>
		</td>
	</tr>
</table>

</fieldset>

<fieldset id="UserAgent" class="options">
<h3 class="label"><?php esc_html_e( 'UserAgent', 'multi-device-switcher' ); ?></h3>
<p><?php esc_html_e( 'Enter Comma-separated values (csv) format.', 'multi-device-switcher' ); ?></p>

<table class="form-table">
	<tr><th scope="row"><?php esc_html_e( 'Smart Phone', 'multi-device-switcher' ); ?></th>
		<td><textarea name="multi_device_switcher_options[userAgent_smart]" rows="4" cols="42"><?php echo esc_textarea( $options['userAgent_smart'] ); ?></textarea></td>
	</tr>
	<tr><th scope="row"><?php esc_html_e( 'Tablet PC', 'multi-device-switcher' ); ?></th>
		<td><textarea name="multi_device_switcher_options[userAgent_tablet]" rows="4" cols="42"><?php echo esc_textarea( $options['userAgent_tablet'] ); ?></textarea></td>
	</tr>
	<tr><th scope="row"><?php esc_html_e( 'Mobile Phone', 'multi-device-switcher' ); ?></th>
		<td><textarea name="multi_device_switcher_options[userAgent_mobile]" rows="4" cols="42"><?php echo esc_textarea( $options['userAgent_mobile'] ); ?></textarea></td>
	</tr>
	<tr><th scope="row"><?php esc_html_e( 'Game Platforms', 'multi-device-switcher' ); ?></th>
		<td><textarea name="multi_device_switcher_options[userAgent_game]" rows="4" cols="42"><?php echo esc_textarea( $options['userAgent_game'] ); ?></textarea></td>
	</tr>
	<tr><th></th>
		<td><span class="submit"><input type="submit" name="multi_device_switcher_options[restore_UserAgent]" value="<?php esc_html_e( 'Reset Settings to Default UserAgent', 'multi-device-switcher' ); ?>" class="button"></span></td>
	</tr>

</table>

<h3><?php esc_html_e( 'Custom Switcher UserAgent', 'multi-device-switcher' ); ?></h3>
<table class="form-table">
		<?php
		foreach ( (array) $options as $custom_switcher_option => $custom_switcher_user_agent ) {
			if ( ! preg_match( '/^custom_switcher_userAgent_/', $custom_switcher_option ) ) {
				continue;
			}

			$custom_switcher_name = preg_replace( '/^custom_switcher_userAgent_/', '', $custom_switcher_option );
			?>

	<tr><th scope="row"><?php echo esc_html( $custom_switcher_name ); ?></th>
		<td><textarea name="multi_device_switcher_options[<?php echo esc_attr( $custom_switcher_option ); ?>]" rows="4" cols="42"><?php echo esc_textarea( $custom_switcher_user_agent ); ?></textarea></td>
	</tr>
			<?php
		}
		?>

</table>
</fieldset>

<fieldset id="PC-Switcher" class="options">
<h3 class="label"><?php esc_html_e( 'PC Switcher', 'multi-device-switcher' ); ?></h3>

<table class="form-table">
	<tr><th scope="row"><?php esc_html_e( 'Add PC Switcher', 'multi-device-switcher' ); ?></th>
		<td>
			<legend class="screen-reader-text"><span><?php esc_html_e( 'Add PC Switcher', 'multi-device-switcher' ); ?></span></legend>
				<label><input type="checkbox" name="multi_device_switcher_options[pc_switcher]" id="pc-switcher" value="1"<?php checked( 1, $options['pc_switcher'] ); ?>> <?php esc_html_e( 'Add a PC Switcher to the footer.', 'multi-device-switcher' ); ?></label>
		</td>
	</tr>
	<tr><th scope="row"><?php esc_html_e( 'Add default CSS', 'multi-device-switcher' ); ?></th>
		<td>
			<legend class="screen-reader-text"><span><?php esc_html_e( 'Add default CSS', 'multi-device-switcher' ); ?></span></legend>
				<label><input type="checkbox" name="multi_device_switcher_options[default_css]" id="add-default-css" value="1"<?php checked( 1, $options['default_css'] ); ?>> <?php esc_html_e( 'Add a default CSS.', 'multi-device-switcher' ); ?></label>
		</td>
	</tr>
</table>
</fieldset>

<fieldset id="Disable-Switcher" class="options">
<h3 class="label"><?php esc_html_e( 'Disable Switcher', 'multi-device-switcher' ); ?></h3>

<table class="form-table">
	<tr><th scope="row"><?php esc_html_e( 'Path', 'multi-device-switcher' ); ?></th>
		<td>
			<legend class="screen-reader-text"><span><?php esc_html_e( 'Path', 'multi-device-switcher' ); ?></span></legend>
				<?php echo esc_html( home_url() ); ?><br>
				<textarea name="multi_device_switcher_options[disable_path]" rows="16" cols="42" wrap="off"><?php echo esc_textarea( $options['disable_path'] ); ?></textarea>
		</td>
	</tr>
	<tr><th scope="row"><?php esc_html_e( 'Regex mode', 'multi-device-switcher' ); ?></th>
		<td>
			<legend class="screen-reader-text"><span><?php esc_html_e( 'Regex mode', 'multi-device-switcher' ); ?></span></legend>
				<label><input type="checkbox" name="multi_device_switcher_options[enable_regex]" id="enable-regex" value="1"<?php checked( 1, $options['enable_regex'] ); ?>> <?php esc_html_e( 'Enable Regex', 'multi-device-switcher' ); ?></label>
		</td>
	</tr>
</table>
</fieldset>

</div>
		<?php submit_button(); ?>
</form>
</div>

<div id="donate">
<h2><?php esc_html_e( 'Donationware', 'multi-device-switcher' ); ?></h2>
<p><?php esc_html_e( 'If you like this plugin, please donate to support development and maintenance.', 'multi-device-switcher' ); ?></p>
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
	 * Validate options.
	 *
	 * @access public
	 *
	 * @param array $input
	 *
	 * @return array
	 */
	public function validate_options( $input ) {
		$output = $this->default_options;

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
			$output['userAgent_smart']  = $this->default_options['userAgent_smart'];
			$output['userAgent_tablet'] = $this->default_options['userAgent_tablet'];
			$output['userAgent_mobile'] = $this->default_options['userAgent_mobile'];
			$output['userAgent_game']   = $this->default_options['userAgent_game'];
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

		if ( isset( $input['add_custom_switcher'] ) && ! empty( $input['custom_switcher'] ) && ! isset( $output[ 'custom_switcher_theme_' . $input['custom_switcher'] ] ) ) {
			if ( ! in_array( $input['custom_switcher'], array( 'smartphone', 'smart', 'tablet', 'mobile', 'game' ) )
					&& preg_match( '/^[A-Za-z0-9]{1,20}$/', $input['custom_switcher'] ) ) {
				$output[ 'custom_switcher_theme_' . $input['custom_switcher'] ]     = 'None';
				$output[ 'custom_switcher_userAgent_' . $input['custom_switcher'] ] = '';
			}
		}

		$output['pc_switcher'] = isset( $input['pc_switcher'] ) ? $input['pc_switcher'] : 0;
		$output['default_css'] = isset( $input['default_css'] ) ? $input['default_css'] : 0;

		$output['disable_path'] = isset( $input['disable_path'] ) ? $input['disable_path'] : '';
		$output['enable_regex'] = isset( $input['enable_regex'] ) ? $input['enable_regex'] : 0;

		/**
		 * Filter hook: multi_device_switcher/validate_options.
		 *
		 * @param array    $options     The options.
		 * @param array    $input       The options.
		 * @param array    $default     The options.
		 *
		 * @since 1.7.0
		 */
		return apply_filters( 'multi_device_switcher/validate_options', $output, $input, $this->default_options );
	}

	/**
	 * Register customize options to Customizer.
	 *
	 * @access public
	 *
	 * @param object $wp_customize
	 *
	 * @return void
	 *
	 * @since 1.3.1
	 */
	public function customize_register( $wp_customize ) {
		$options               = $this->get_options();
		$default_theme_options = $this->default_options;
		$default_theme         = wp_get_theme()->get( 'Name' );
		$themes                = wp_get_themes();

		$theme_names = array();
		$choices     = array();

		if ( count( $themes ) ) {
			foreach ( $themes as $t ) {
				$theme_names[] = $t->get( 'Name' );
			}
			natcasesort( $theme_names );

			$choices['None'] = __( 'None', 'multi-device-switcher' );
			foreach ( $theme_names as $theme_name ) {
				if ( $default_theme === $theme_name ) {
					continue;
				}
				$choices[ $theme_name ] = $theme_name;
			}
		}

		$switcher = array(
			'theme_smartphone' => __( 'Smart Phone Theme', 'multi-device-switcher' ),
			'theme_tablet'     => __( 'Tablet PC Theme', 'multi-device-switcher' ),
			'theme_mobile'     => __( 'Mobile Phone Theme', 'multi-device-switcher' ),
			'theme_game'       => __( 'Game Platforms Theme', 'multi-device-switcher' ),
		);

		$wp_customize->add_section(
			'multi_device_switcher',
			array(
				'title'    => __( 'Multi Device Switcher', 'multi-device-switcher' ),
				'priority' => 80,
			)
		);

		foreach ( $switcher as $name => $label ) {
			$wp_customize->add_setting(
				'multi_device_switcher_options[' . $name . ']',
				array(
					'default'    => $default_theme_options[ $name ],
					'type'       => 'option',
					'capability' => $this->capability,
				)
			);

			$wp_customize->add_control(
				'multi_device_switcher_options[' . $name . ']',
				array(
					'label'   => $label,
					'section' => 'multi_device_switcher',
					'type'    => 'select',
					'choices' => $choices,
				)
			);
		}

		foreach ( (array) $options as $custom_switcher_option => $custom_switcher_theme ) {
			if ( ! preg_match( '/^custom_switcher_theme_/', $custom_switcher_option ) ) {
				continue;
			}

			$label = preg_replace( '/^custom_switcher_theme_/', '', $custom_switcher_option );

			$wp_customize->add_setting(
				'multi_device_switcher_options[' . $custom_switcher_option . ']',
				array(
					'default'    => __( 'None', 'multi-device-switcher' ),
					'type'       => 'option',
					'capability' => $this->capability,
				)
			);

			$wp_customize->add_control(
				'multi_device_switcher_options[' . $custom_switcher_option . ']',
				array(
					'label'   => $label,
					'section' => 'multi_device_switcher',
					'type'    => 'select',
					'choices' => $choices,
				)
			);

		}
	}

	/**
	 * Load files.
	 *
	 * @access public
	 *
	 * @since 1.0.0
	 */
	public function load_file() {
		/**
		 * Include PC Switcher Widget.
		 *
		 * @since 1.2
		 */
		require_once dirname( __MULTI_DEVICE_SWITCHER_FILE__ ) . '/pc-switcher-widget.php';

		/**
		 * Include Multi Device Switcher Command
		 *
		 * @since 1.4
		 */
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			require_once dirname( __MULTI_DEVICE_SWITCHER_FILE__ ) . '/wp-cli.php';
		}
	}
}

define( '__MULTI_DEVICE_SWITCHER_FILE__', __FILE__ );

if ( class_exists( 'Multi_Device_Switcher' ) ) {
	global $multi_device_switcher;
	$multi_device_switcher = new Multi_Device_Switcher();
};

/**
 * Add PC Switcher.
 *
 * @since 1.2
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
 */
if ( ! function_exists( 'is_disable_switcher' ) ) :

	function is_disable_switcher() {
		global $multi_device_switcher;
		if ( is_object( $multi_device_switcher ) ) {
			return $multi_device_switcher->is_disable_switcher();
		}
	}
endif;

/**
 * Returns the default options.
 *
 * @since 1.5.3
 */
if ( ! function_exists( 'multi_device_switcher_get_default_options' ) ) :

	function multi_device_switcher_get_default_options() {
		global $multi_device_switcher;
		if ( is_object( $multi_device_switcher ) ) {
			return $multi_device_switcher->get_default_options();
		}
	}
endif;
