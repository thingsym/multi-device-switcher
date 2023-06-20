<?php

class Test_Multi_Device_Switcher_Constructor_Admin extends WP_UnitTestCase {
	public $multi_device_switcher;

	public function setUp(): void {
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
		$this->assertSame( 10, has_filter( 'plugins_loaded', array( $this->multi_device_switcher, 'load_textdomain' ) ) );
		$this->assertSame( 10, has_filter( 'plugins_loaded', array( $this->multi_device_switcher, 'init' ) ) );

		$this->assertSame( 10, has_action( 'admin_init', array( $this->multi_device_switcher, 'register_settings' ) ) );
		$this->assertSame( 10, has_action( 'admin_menu', array( $this->multi_device_switcher, 'add_option_page' ) ) );

		$this->assertFalse( has_filter( 'wp_headers', array( $this->multi_device_switcher, 'add_header_vary' ) ) );
		$this->assertFalse( has_action( 'plugins_loaded', array( $this->multi_device_switcher, 'switch_theme' ) ) );

		$this->assertSame( 10, has_action( 'customize_register', array( $this->multi_device_switcher, 'customizer' ) ) );
		$this->assertSame( 10, has_action( 'plugins_loaded', array( $this->multi_device_switcher, 'load_file' ) ) );
	}
}
