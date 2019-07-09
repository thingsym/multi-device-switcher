<?php

class Test_Multi_Device_Switcher_Functions_Detect_Device extends WP_UnitTestCase {

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
			'userAgent_game' => 'PlayStation Portable, PlayStation Vita, PSP, PS2, PLAYSTATION 3, PlayStation 4, Nitro, Nintendo 3DS, Nintendo Wii, Nintendo WiiU, Xbox',
			'disable_path' => '',
			'enable_regex' => 0,
			'custom_switcher_theme_test' => 'Twenty Sixteen',
			'custom_switcher_userAgent_test' => 'test1,test2',
		);

		update_option( 'multi_device_switcher_options', $options );
	}

	function tearDown() {
		parent::tearDown();
	}

	/**
	 * @test
	 * @group detect_device
	 */
	function detect_device_no_match_device() {
		$this->multi_device_switcher->detect_device();

		$this->assertEmpty( $this->multi_device_switcher->device );
		$this->assertEquals( 10, has_action( 'init', array( $this->multi_device_switcher, 'session' ) ) );
		$this->assertFalse( has_filter( 'wp_headers', array( $this->multi_device_switcher, 'set_cookie_rest_disable_switcher' ) ) );
		$this->assertFalse( has_filter( 'wp_headers', array( $this->multi_device_switcher, 'set_cookie_enable_disable_switcher' ) ) );
	}

	/**
	 * @test
	 * @group detect_device
	 */
	function detect_device_match_device() {
		$GLOBALS['_SERVER']['HTTP_USER_AGENT'] = 'iPhone';
		$this->multi_device_switcher->detect_device();

		$this->assertEquals( 'smart', $this->multi_device_switcher->device );
		$this->assertEquals( 10, has_action( 'init', array( $this->multi_device_switcher, 'session' ) ) );
		$this->assertFalse( has_filter( 'wp_headers', array( $this->multi_device_switcher, 'set_cookie_rest_disable_switcher' ) ) );
		$this->assertFalse( has_filter( 'wp_headers', array( $this->multi_device_switcher, 'set_cookie_enable_disable_switcher' ) ) );

		$GLOBALS['_SERVER']['HTTP_USER_AGENT'] = 'test1';
		$this->multi_device_switcher->detect_device();

		$this->assertEquals( 'custom_switcher_test', $this->multi_device_switcher->device );
		$this->assertEquals( 10, has_action( 'init', array( $this->multi_device_switcher, 'session' ) ) );
		$this->assertFalse( has_filter( 'wp_headers', array( $this->multi_device_switcher, 'set_cookie_rest_disable_switcher' ) ) );
		$this->assertFalse( has_filter( 'wp_headers', array( $this->multi_device_switcher, 'set_cookie_enable_disable_switcher' ) ) );

		unset($GLOBALS['_SERVER']['HTTP_USER_AGENT']);
	}

	/**
	 * @test
	 * @group detect_device
	 */
	function detect_device_enable_disable_switcher() {
		$GLOBALS['_COOKIE']['disable-switcher'] = 1;
		$this->multi_device_switcher->detect_device();

		$this->assertEquals( 10, has_action( 'init', array( $this->multi_device_switcher, 'session' ) ) );
		$this->assertEquals( 10, has_filter( 'wp_headers', array( $this->multi_device_switcher, 'set_cookie_rest_disable_switcher' ) ) );
		$this->assertFalse( has_filter( 'wp_headers', array( $this->multi_device_switcher, 'set_cookie_enable_disable_switcher' ) ) );

		unset($GLOBALS['_COOKIE']['disable-switcher']);
	}

	/**
	 * @test
	 * @group detect_device
	 */
	function detect_device_match_disable_switcher() {
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
			'userAgent_game' => 'PlayStation Portable, PlayStation Vita, PSP, PS2, PLAYSTATION 3, PlayStation 4, Nitro, Nintendo 3DS, Nintendo Wii, Nintendo WiiU, Xbox',
			'disable_path' => "/test\n/abc\n",
			'enable_regex' => 0,
			'custom_switcher_theme_test' => 'Twenty Sixteen',
			'custom_switcher_userAgent_test' => 'test1,test2',
		);

		update_option( 'multi_device_switcher_options', $options );
		$GLOBALS['_SERVER']['REQUEST_URI'] = '/test';
		$this->multi_device_switcher->detect_device();

		$this->assertTrue( $this->multi_device_switcher->is_disable_switcher() );
		$this->assertFalse( has_action( 'init', array( $this->multi_device_switcher, 'session' ) ) );
		$this->assertFalse( has_filter( 'wp_headers', array( $this->multi_device_switcher, 'set_cookie_rest_disable_switcher' ) ) );
		$this->assertEquals( 10, has_filter( 'wp_headers', array( $this->multi_device_switcher, 'set_cookie_enable_disable_switcher' ) ) );

		unset($GLOBALS['_SERVER']['REQUEST_URI']);
	}

}
