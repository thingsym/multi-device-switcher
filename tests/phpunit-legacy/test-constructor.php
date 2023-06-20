<?php

class Test_Multi_Device_Switcher_Constructor extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		global $current_screen;
		$current_screen = NULL;

		$this->multi_device_switcher = new Multi_Device_Switcher();
	}

	/**
	 * @test
	 * @group basic
	 */
	function public_variable() {
		$this->assertSame( 'multi_device_switcher', $this->multi_device_switcher->option_group );
		$this->assertSame( 'multi_device_switcher_options', $this->multi_device_switcher->option_name );
		$this->assertSame( 'switch_themes', $this->multi_device_switcher->capability );
		$this->assertSame( 'multi-device-switcher', $this->multi_device_switcher->cookie_name_multi_device_switcher );
		$this->assertSame( 'disable-switcher', $this->multi_device_switcher->cookie_name_disable_switcher );
		$this->assertSame( 'pc-switcher', $this->multi_device_switcher->cookie_name_pc_switcher );

		$expected = array(
			'pc_switcher'      => 1,
			'default_css'      => 1,
			'theme_smartphone' => 'None',
			'theme_tablet'     => 'None',
			'theme_mobile'     => 'None',
			'theme_game'       => 'None',
			'userAgent_smart'  => 'iPhone, iPod, Android.*Mobile, dream, CUPCAKE, Windows Phone, IEMobile.*Touch, webOS, BB10.*Mobile, BlackBerry.*Mobile, Mobile.*Gecko',
			'userAgent_tablet' => 'iPad, Kindle, Silk, Android(?!.*Mobile), Windows.*Touch, PlayBook, Tablet.*Gecko',
			'userAgent_mobile' => 'DoCoMo, SoftBank, J-PHONE, Vodafone, KDDI, UP.Browser, WILLCOM, emobile, DDIPOCKET, Windows CE, BlackBerry, Symbian, PalmOS, Huawei, IAC, Nokia',
			'userAgent_game'   => 'PSP, PS2, PLAYSTATION 3, PlayStation (Portable|Vita|4|5), Nitro, Nintendo (3DS|Wii|WiiU|Switch), Xbox',
			'disable_path'     => '',
			'enable_regex'     => 0,
		);
		$this->assertSame( $expected, $this->multi_device_switcher->default_options );

		$this->assertSame( '', $this->multi_device_switcher->device );

		$this->assertIsArray( $this->multi_device_switcher->plugin_data );
		$this->assertEmpty( $this->multi_device_switcher->plugin_data );
	}

	/**
	 * @test
	 * @group constructor
	 */
	function constructor() {
		$this->assertSame( 10, has_action( 'plugins_loaded', array( $this->multi_device_switcher, 'load_plugin_data' ) ) );
		$this->assertSame( 10, has_filter( 'plugins_loaded', array( $this->multi_device_switcher, 'load_textdomain' ) ) );
		$this->assertSame( 10, has_filter( 'plugins_loaded', array( $this->multi_device_switcher, 'init' ) ) );

		$this->assertSame( 10, has_action( 'admin_init', array( $this->multi_device_switcher, 'register_settings' ) ) );
		$this->assertSame( 10, has_action( 'admin_menu', array( $this->multi_device_switcher, 'add_option_page' ) ) );

		$this->assertSame( 10, has_filter( 'wp_headers', array( $this->multi_device_switcher, 'add_header_vary' ) ) );
		$this->assertSame( 10, has_action( 'plugins_loaded', array( $this->multi_device_switcher, 'switch_theme' ) ) );

		$this->assertSame( 10, has_action( 'customize_register', array( $this->multi_device_switcher, 'customizer' ) ) );
		$this->assertSame( 10, has_action( 'plugins_loaded', array( $this->multi_device_switcher, 'load_file' ) ) );
	}

	/**
	 * @test
	 * @group constructor
	 */
	function init() {
		$this->multi_device_switcher->init();

		$this->assertSame( 10, has_filter( 'option_page_capability_multi_device_switcher', array( $this->multi_device_switcher, 'option_page_capability' ) ) );
		$this->assertSame( 10, has_filter( 'plugin_action_links_' . plugin_basename( __MULTI_DEVICE_SWITCHER_FILE__ ), array( $this->multi_device_switcher, 'plugin_action_links' ) ) );
		$this->assertSame( 10, has_filter( 'plugin_row_meta', array( $this->multi_device_switcher, 'plugin_metadata_links' ) ) );

		$this->assertTrue( shortcode_exists( 'multi' ) );
	}

	/**
	 * @test
	 * @group constructor
	 */
	function pc_switcher() {
		$this->assertTrue( class_exists( 'PC_Switcher' ) );
		$this->assertSame( 10, has_action( 'widgets_init', 'pc_switcher_load_widgets' ) );
	}
}
