<?php

class Test_Multi_Device_Switcher_Constructor_Admin extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		$screen = WP_Screen::get( 'dashboard' );
		global $current_screen;
		$current_screen = $screen;

		$this->multi_device_switcher = new Multi_Device_Switcher();
	}

	/**
	 * @test
	 * @group constructor
	 */
	function constructor() {
		$this->assertEquals( 10, has_filter( 'init', array( $this->multi_device_switcher, 'load_textdomain' ) ) );
		$this->assertEquals( 10, has_filter( 'init', array( $this->multi_device_switcher, 'init' ) ) );

		$this->assertEquals( 10, has_action( 'admin_init', array( $this->multi_device_switcher, 'register_settings' ) ) );
		$this->assertEquals( 10, has_action( 'admin_menu', array( $this->multi_device_switcher, 'add_option_page' ) ) );

		$this->assertFalse( has_filter( 'wp_headers', array( $this->multi_device_switcher, 'add_header_vary' ) ) );
		$this->assertFalse( has_action( 'plugins_loaded', array( $this->multi_device_switcher, 'switch_theme' ) ) );

		$this->assertEquals( 10, has_action( 'customize_register', array( $this->multi_device_switcher, 'customizer' ) ) );
		$this->assertEquals( 10, has_action( 'plugins_loaded', array( $this->multi_device_switcher, 'load_file' ) ) );
	}
}
