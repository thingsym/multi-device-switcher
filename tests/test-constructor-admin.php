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
		$this->assertEquals( 10, has_action( 'admin_init', array( $this->multi_device_switcher, 'admin_init' ) ) );
		$this->assertEquals( 10, has_filter( 'option_page_capability_multi_device_switcher', array( $this->multi_device_switcher, 'option_page_capability' ) ) );
		$this->assertEquals( 10, has_action( 'admin_menu', array( $this->multi_device_switcher, 'add_option_page' ) ) );

		$this->assertFalse( has_filter( 'wp_headers', array( $this->multi_device_switcher, 'add_header_vary' ) ) );
		$this->assertFalse( has_action( 'plugins_loaded', array( $this->multi_device_switcher, 'switch_theme' ) ) );

		$this->assertTrue( shortcode_exists( 'multi' ) );
		$this->assertEquals( 10, has_action( 'customize_register', array( $this->multi_device_switcher, 'customize_register' ) ) );
		$this->assertEquals( 10, has_action( 'plugins_loaded', array( $this->multi_device_switcher, 'load_file' ) ) );
	}
}
