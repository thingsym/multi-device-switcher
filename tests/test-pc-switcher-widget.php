<?php

class Test_pc_switcher_Pc_Switcher_Widget extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		$this->multi_device_switcher = new Multi_Device_Switcher();

		$options = array(
			'pc_switcher' => 1,
			'default_css' => 1,
			'theme_smartphone' => 'Twenty Sixteen',
			'theme_tablet' => 'Twenty Sixteen',
			'theme_mobile' => 'None',
			'theme_game' => 'None',
			'userAgent_smart' => 'iPhone, iPod, Android.*Mobile, dream, CUPCAKE, Windows Phone, IEMobile.*Touch, webOS, BB10.*Mobile, BlackBerry.*Mobile, Mobile.*Gecko',
			'userAgent_tablet' => 'iPad, Kindle, Silk, Android(?!.*Mobile), Windows.*Touch, PlayBook, Tablet.*Gecko',
			'userAgent_mobile' => 'DoCoMo, SoftBank, J-PHONE, Vodafone, KDDI, UP.Browser, WILLCOM, emobile, DDIPOCKET, Windows CE, BlackBerry, Symbian, PalmOS, Huawei, IAC, Nokia',
			'userAgent_game' => 'PSP, PS2, PLAYSTATION 3, PlayStation (Portable|Vita|4|5), Nitro, Nintendo (3DS|Wii|WiiU|Switch), Xbox',
			'disable_path' => '',
			'enable_regex' => 0,
			'custom_switcher_theme_test' => 'Twenty Sixteen',
			'custom_switcher_userAgent_test' => 'test1,test2',
		);

		update_option( 'multi_device_switcher_options', $options );

		$this->multi_device_switcher->load_plugin_data();
		$GLOBALS['_SERVER']['REQUEST_URI'] = '';

		$this->pc_switcher = new PC_Switcher();
	}

	function tearDown() {
		parent::tearDown();

		$GLOBALS['_SERVER']['REQUEST_URI'] = '';
		unset( $GLOBALS['_COOKIE']['pc-switcher'] );

		global $multi_device_switcher;
		$multi_device_switcher->device = '';
	}

	/**
	 * @test
	 * @group widget
	 */
	function is_class() {
		$this->assertTrue( class_exists( 'PC_Switcher' ) );
	}

	/**
	 * @test
	 * @group widget
	 */
	function constructor() {
		$this->assertSame( 'pc-switcher', $this->pc_switcher->id_base );
		$this->assertSame( 'PC Switcher', $this->pc_switcher->name );

		$this->assertSame( 'widget_pc-switcher', $this->pc_switcher->option_name );
		$this->assertSame( 'widget_pc_switcher', $this->pc_switcher->alt_option_name );

		$this->assertArrayHasKey( 'classname', $this->pc_switcher->widget_options );
		$this->assertSame( 'widget_pc_switcher', $this->pc_switcher->widget_options['classname'] );
		$this->assertArrayHasKey( 'description', $this->pc_switcher->widget_options );
		$this->assertContains( 'Add the PC Switcher to a widget.', $this->pc_switcher->widget_options['description'] );
		$this->assertArrayHasKey( 'customize_selective_refresh', $this->pc_switcher->widget_options );
		$this->assertTrue( $this->pc_switcher->widget_options['customize_selective_refresh'] );

		$this->assertArrayHasKey( 'id_base', $this->pc_switcher->control_options );
		$this->assertSame( 'pc-switcher', $this->pc_switcher->control_options['id_base'] );
	}

	/**
	 * @test
	 * @group widget
	 */
	function widget() {
		$args = array(
			'before_widget' => '<aside id="widget-pc-switcher-1" class="widget widget_pc_switcher">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		);
		$instance = array();

		ob_start();
		$this->pc_switcher->widget( $args, $instance );
		$widget = ob_get_clean();

		$this->assertSame( '', $widget );

		global $multi_device_switcher;
		$multi_device_switcher->device = 'smart';

		ob_start();
		$this->pc_switcher->widget( $args, $instance );
		$widget = ob_get_clean();

		$this->assertRegExp( '/' . preg_quote( 'pc-switcher=1' ) . '/', $widget );
		$this->assertRegExp( '/' . preg_quote( 'class="active">Mobile' ) . '/', $widget );

		$GLOBALS['_COOKIE']['pc-switcher'] = 1;

		ob_start();
		$this->pc_switcher->widget( $args, $instance );
		$widget = ob_get_clean();

		$this->assertRegExp( '/' . preg_quote( 'pc-switcher=0' ) . '/', $widget );
		$this->assertRegExp( '/' . preg_quote( 'class="active">PC' ) . '/', $widget );
	}

	/**
	 * @test
	 * @group widget
	 */
	function widget_switcher_off() {
		$options = array(
			'pc_switcher' => 0,
			'default_css' => 1,
			'theme_smartphone' => 'Twenty Sixteen',
			'theme_tablet' => 'Twenty Sixteen',
			'theme_mobile' => 'None',
			'theme_game' => 'None',
			'userAgent_smart' => 'iPhone, iPod, Android.*Mobile, dream, CUPCAKE, Windows Phone, IEMobile.*Touch, webOS, BB10.*Mobile, BlackBerry.*Mobile, Mobile.*Gecko',
			'userAgent_tablet' => 'iPad, Kindle, Silk, Android(?!.*Mobile), Windows.*Touch, PlayBook, Tablet.*Gecko',
			'userAgent_mobile' => 'DoCoMo, SoftBank, J-PHONE, Vodafone, KDDI, UP.Browser, WILLCOM, emobile, DDIPOCKET, Windows CE, BlackBerry, Symbian, PalmOS, Huawei, IAC, Nokia',
			'userAgent_game' => 'PSP, PS2, PLAYSTATION 3, PlayStation (Portable|Vita|4|5), Nitro, Nintendo (3DS|Wii|WiiU|Switch), Xbox',
			'disable_path' => '',
			'enable_regex' => 0,
			'custom_switcher_theme_test' => 'Twenty Sixteen',
			'custom_switcher_userAgent_test' => 'test1,test2',
		);

		update_option( 'multi_device_switcher_options', $options );

		$args = array(
			'before_widget' => '<aside id="widget-pc-switcher-1" class="widget widget_pc_switcher">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		);
		$instance = array();

		ob_start();
		$this->pc_switcher->widget( $args, $instance );
		$widget = ob_get_clean();

		$this->assertSame( '', $widget );

		global $multi_device_switcher;
		$multi_device_switcher->device = 'smart';

		ob_start();
		$this->pc_switcher->widget( $args, $instance );
		$widget = ob_get_clean();

		$this->assertRegExp( '/' . preg_quote( 'pc-switcher=1' ) . '/', $widget );
		$this->assertRegExp( '/' . preg_quote( 'class="active">Mobile' ) . '/', $widget );

		$GLOBALS['_COOKIE']['pc-switcher'] = 1;

		ob_start();
		$this->pc_switcher->widget( $args, $instance );
		$widget = ob_get_clean();

		$this->assertRegExp( '/' . preg_quote( 'pc-switcher=0' ) . '/', $widget );
		$this->assertRegExp( '/' . preg_quote( 'class="active">PC' ) . '/', $widget );
	}

	/**
	 * @test
	 * @group widget
	 */
	function update() {
		$new_instance = array();
		$expected     = array();

		$validate = $this->pc_switcher->update( $new_instance, array() );

		$this->assertSame( $validate, $expected );
	}

	/**
	 * @test
	 * @group widget
	 */
	function form() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

}
