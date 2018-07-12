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
	 * @group constructor
	 */
	function constructor() {
		$this->assertEquals( 10, has_filter( 'init', array( $this->multi_device_switcher, 'load_textdomain' ) ) );
		$this->assertEquals( 10, has_filter( 'init', array( $this->multi_device_switcher, 'init' ) ) );

		$this->assertEquals( 10, has_action( 'admin_init', array( $this->multi_device_switcher, 'admin_init' ) ) );
		$this->assertEquals( 10, has_action( 'admin_menu', array( $this->multi_device_switcher, 'add_option_page' ) ) );

		$this->assertEquals( 10, has_filter( 'wp_headers', array( $this->multi_device_switcher, 'add_header_vary' ) ) );
		$this->assertEquals( 10, has_action( 'plugins_loaded', array( $this->multi_device_switcher, 'switch_theme' ) ) );

		$this->assertEquals( 10, has_action( 'customize_register', array( $this->multi_device_switcher, 'customize_register' ) ) );
		$this->assertEquals( 10, has_action( 'plugins_loaded', array( $this->multi_device_switcher, 'load_file' ) ) );
	}

	/**
	 * @test
	 * @group constructor
	 */
	function init() {
		$this->multi_device_switcher->init();

		$this->assertEquals( 10, has_filter( 'option_page_capability_multi_device_switcher', array( $this->multi_device_switcher, 'option_page_capability' ) ) );
		$this->assertEquals( 10, has_filter( 'plugin_action_links_' . plugin_basename( __MULTI_DEVICE_SWITCHER_FILE__ ), array( $this->multi_device_switcher, 'plugin_action_links' ) ) );

		$this->assertTrue( shortcode_exists( 'multi' ) );
	}
}
