<?php

class Test_Multi_Device_Switcher_Pc_Switcher extends WP_UnitTestCase {

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
	}

	function tearDown() {
		$GLOBALS['_SERVER']['REQUEST_URI'] = '';
		wp_dequeue_style( 'pc-switcher-options' );
		$this->multi_device_switcher->device = '';
		unset( $GLOBALS['_COOKIE']['pc-switcher'] );

		parent::tearDown();
	}

	/**
	 * @test
	 * @group pc_switcher
	 */
	function enqueue_styles() {
		$this->multi_device_switcher->enqueue_styles();
		$this->assertTrue( wp_style_is( 'pc-switcher-options' ) );
	}

	/**
	 * @test
	 * @group pc_switcher
	 */
	function add_pc_switcher_case_no_switching() {
		wp_dequeue_style( 'pc-switcher-options' );
		ob_start();
		$this->multi_device_switcher->add_pc_switcher();
		$result = ob_get_clean();

		$this->assertSame( '', $result );
		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		wp_dequeue_style( 'pc-switcher-options' );
		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 1 );
		$result = ob_get_clean();

		$this->assertSame( '', $result );
		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		wp_dequeue_style( 'pc-switcher-options' );
		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 0 );
		$result = ob_get_clean();

		$this->assertSame( '', $result );
		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		$GLOBALS['_COOKIE']['pc-switcher'] = 1;
		wp_dequeue_style( 'pc-switcher-options' );
		ob_start();
		$this->multi_device_switcher->add_pc_switcher();
		$result = ob_get_clean();
		unset( $GLOBALS['_COOKIE']['pc-switcher'] );

		$this->assertSame( '', $result );
		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		$GLOBALS['_COOKIE']['pc-switcher'] = 1;
		wp_dequeue_style( 'pc-switcher-options' );
		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 0 );
		$result = ob_get_clean();
		unset( $GLOBALS['_COOKIE']['pc-switcher'] );

		$this->assertSame( '', $result );
		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		$GLOBALS['_COOKIE']['pc-switcher'] = 1;
		wp_dequeue_style( 'pc-switcher-options' );
		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 1 );
		$result = ob_get_clean();
		unset( $GLOBALS['_COOKIE']['pc-switcher'] );

		$this->assertSame( '', $result );
		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );
	}

	/**
	 * @test
	 * @group pc_switcher
	 */
	function add_pc_switcher_case_switching() {
		$this->multi_device_switcher->device = 'smart';

		wp_dequeue_style( 'pc-switcher-options' );
		ob_start();
		$this->multi_device_switcher->add_pc_switcher();
		$result = ob_get_clean();

		$this->assertRegExp( '/' . preg_quote( 'pc-switcher=1' ) . '/', $result );
		$this->assertRegExp( '/' . preg_quote( 'class="active">Mobile' ) . '/', $result );
		$this->assertTrue( wp_style_is( 'pc-switcher-options' ) );

		wp_dequeue_style( 'pc-switcher-options' );
		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 0 );
		$result = ob_get_clean();

		$this->assertRegExp( '/' . preg_quote( 'pc-switcher=1' ) . '/', $result );
		$this->assertRegExp( '/' . preg_quote( 'class="active">Mobile' ) . '/', $result );
		$this->assertTrue( wp_style_is( 'pc-switcher-options' ) );

		wp_dequeue_style( 'pc-switcher-options' );
		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 1 );
		$result = ob_get_clean();

		$this->assertRegExp( '/' . preg_quote( 'pc-switcher=1' ) . '/', $result );
		$this->assertRegExp( '/' . preg_quote( 'class="active">Mobile' ) . '/', $result );
		$this->assertTrue( wp_style_is( 'pc-switcher-options' ) );

		$GLOBALS['_COOKIE']['pc-switcher'] = 1;

		wp_dequeue_style( 'pc-switcher-options' );
		ob_start();
		$this->multi_device_switcher->add_pc_switcher();
		$result = ob_get_clean();
		unset( $GLOBALS['_COOKIE']['pc-switcher'] );

		$this->assertRegExp( '/' . preg_quote( 'pc-switcher=0' ) . '/', $result );
		$this->assertRegExp( '/' . preg_quote( 'class="active">PC' ) . '/', $result );
		$this->assertTrue( wp_style_is( 'pc-switcher-options' ) );

		wp_dequeue_style( 'pc-switcher-options' );
		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 0 );
		$result = ob_get_clean();
		unset( $GLOBALS['_COOKIE']['pc-switcher'] );

		$this->assertRegExp( '/' . preg_quote( 'pc-switcher=1' ) . '/', $result );
		$this->assertRegExp( '/' . preg_quote( 'class="active">Mobile' ) . '/', $result );
		$this->assertTrue( wp_style_is( 'pc-switcher-options' ) );

		wp_dequeue_style( 'pc-switcher-options' );
		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 1 );
		$result = ob_get_clean();
		unset( $GLOBALS['_COOKIE']['pc-switcher'] );

		$this->assertRegExp( '/' . preg_quote( 'pc-switcher=1' ) . '/', $result );
		$this->assertRegExp( '/' . preg_quote( 'class="active">Mobile' ) . '/', $result );
		$this->assertTrue( wp_style_is( 'pc-switcher-options' ) );
	}

	/**
	 * @test
	 * @group pc_switcher
	 */
	function add_pc_switcher_case_no_switching_with_switcher_off() {
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

		wp_dequeue_style( 'pc-switcher-options' );
		ob_start();
		$this->multi_device_switcher->add_pc_switcher();
		$result = ob_get_clean();

		$this->assertSame( '', $result );
		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		wp_dequeue_style( 'pc-switcher-options' );
		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 0 );
		$result = ob_get_clean();

		$this->assertSame( '', $result );
		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		wp_dequeue_style( 'pc-switcher-options' );
		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 1 );
		$result = ob_get_clean();

		$this->assertSame( '', $result );
		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );
	}

	/**
	 * @test
	 * @group pc_switcher
	 */
	function add_pc_switcher_case_switching_with_switcher_off() {
		$this->multi_device_switcher->device = 'smart';

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

		wp_dequeue_style( 'pc-switcher-options' );
		ob_start();
		$this->multi_device_switcher->add_pc_switcher();
		$result = ob_get_clean();

		$this->assertSame( '', $result );
		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		wp_dequeue_style( 'pc-switcher-options' );
		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 0 );
		$result = ob_get_clean();

		$this->assertSame( '', $result );
		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		wp_dequeue_style( 'pc-switcher-options' );
		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 1 );
		$result = ob_get_clean();

		$this->assertRegExp( '/' . preg_quote( 'pc-switcher=1' ) . '/', $result );
		$this->assertRegExp( '/' . preg_quote( 'class="active">Mobile' ) . '/', $result );
		$this->assertTrue( wp_style_is( 'pc-switcher-options' ) );
	}

	/**
	 * @test
	 * @group pc_switcher
	 */
	function add_pc_switcher_case_with_default_css_off() {
		$options = array(
			'pc_switcher' => 1,
			'default_css' => 0,
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

		wp_dequeue_style( 'pc-switcher-options' );

		ob_start();
		$this->multi_device_switcher->add_pc_switcher();
		$result = ob_get_clean();

		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 0 );
		$result = ob_get_clean();

		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 1 );
		$result = ob_get_clean();

		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		$GLOBALS['_COOKIE']['pc-switcher'] = 1;
		ob_start();
		$this->multi_device_switcher->add_pc_switcher();
		$result = ob_get_clean();
		unset( $GLOBALS['_COOKIE']['pc-switcher'] );

		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		$GLOBALS['_COOKIE']['pc-switcher'] = 1;
		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 0 );
		$result = ob_get_clean();
		unset( $GLOBALS['_COOKIE']['pc-switcher'] );

		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		$GLOBALS['_COOKIE']['pc-switcher'] = 1;
		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 1 );
		$result = ob_get_clean();
		unset( $GLOBALS['_COOKIE']['pc-switcher'] );

		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );


		$this->multi_device_switcher->device = 'smart';

		ob_start();
		$this->multi_device_switcher->add_pc_switcher();
		$result = ob_get_clean();

		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 0 );
		$result = ob_get_clean();

		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 1 );
		$result = ob_get_clean();

		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		$GLOBALS['_COOKIE']['pc-switcher'] = 1;
		ob_start();
		$this->multi_device_switcher->add_pc_switcher();
		$result = ob_get_clean();
		unset( $GLOBALS['_COOKIE']['pc-switcher'] );

		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		$GLOBALS['_COOKIE']['pc-switcher'] = 1;
		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 0 );
		$result = ob_get_clean();
		unset( $GLOBALS['_COOKIE']['pc-switcher'] );

		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		$GLOBALS['_COOKIE']['pc-switcher'] = 1;
		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 1 );
		$result = ob_get_clean();
		unset( $GLOBALS['_COOKIE']['pc-switcher'] );

		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );
	}

		/**
	 * @test
	 * @group pc_switcher
	 */
	function add_pc_switcher_case_with_switcher_off_and_default_css_off() {
		$options = array(
			'pc_switcher' => 0,
			'default_css' => 0,
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

		wp_dequeue_style( 'pc-switcher-options' );

		ob_start();
		$this->multi_device_switcher->add_pc_switcher();
		$result = ob_get_clean();

		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 0 );
		$result = ob_get_clean();

		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 1 );
		$result = ob_get_clean();

		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		$GLOBALS['_COOKIE']['pc-switcher'] = 1;
		ob_start();
		$this->multi_device_switcher->add_pc_switcher();
		$result = ob_get_clean();
		unset( $GLOBALS['_COOKIE']['pc-switcher'] );

		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		$GLOBALS['_COOKIE']['pc-switcher'] = 1;
		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 0 );
		$result = ob_get_clean();
		unset( $GLOBALS['_COOKIE']['pc-switcher'] );

		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		$GLOBALS['_COOKIE']['pc-switcher'] = 1;
		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 1 );
		$result = ob_get_clean();
		unset( $GLOBALS['_COOKIE']['pc-switcher'] );

		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );


		$this->multi_device_switcher->device = 'smart';

		ob_start();
		$this->multi_device_switcher->add_pc_switcher();
		$result = ob_get_clean();

		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 0 );
		$result = ob_get_clean();

		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 1 );
		$result = ob_get_clean();

		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		$GLOBALS['_COOKIE']['pc-switcher'] = 1;
		ob_start();
		$this->multi_device_switcher->add_pc_switcher();
		$result = ob_get_clean();
		unset( $GLOBALS['_COOKIE']['pc-switcher'] );

		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		$GLOBALS['_COOKIE']['pc-switcher'] = 1;
		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 0 );
		$result = ob_get_clean();
		unset( $GLOBALS['_COOKIE']['pc-switcher'] );

		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );

		$GLOBALS['_COOKIE']['pc-switcher'] = 1;
		ob_start();
		$this->multi_device_switcher->add_pc_switcher( 1 );
		$result = ob_get_clean();
		unset( $GLOBALS['_COOKIE']['pc-switcher'] );

		$this->assertFalse( wp_style_is( 'pc-switcher-options' ) );
	}

}
