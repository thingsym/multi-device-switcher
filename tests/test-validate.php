<?php

class Test_Multi_Device_Switcher_Validate extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->multi_device_switcher = new Multi_Device_Switcher();
	}

	/**
	 * @test
	 * @group validate
	 */
	function validate_case_none() {
		$input = array();
		$expected = array(
			'pc_switcher' => 0,
			'default_css' => 0,
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

		$actual = $this->multi_device_switcher->validate_options( $input );

		$this->assertTrue( is_array( $actual ) );
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * @test
	 * @group validate
	 */
	function validate_case_1() {
		$input = array(
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
		$expected = array(
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

		$actual = $this->multi_device_switcher->validate_options( $input );

		$this->assertTrue( is_array( $actual ) );
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * @test
	 * @group validate
	 */
	function validate_restore_useragent_case_1() {
		$input = array(
			'pc_switcher' => 1,
			'default_css' => 1,
			'theme_smartphone' => 'None',
			'theme_tablet' => 'None',
			'theme_mobile' => 'None',
			'theme_game' => 'None',
			'userAgent_smart' => 'iPhone, iPod, Android.*Mobile',
			'userAgent_tablet' => 'iPad, Kindle',
			'userAgent_mobile' => 'DoCoMo, SoftBank, J-PHONE, Vodafone',
			'userAgent_game' => 'PlayStation Portable, PlayStation Vita, PSP, PS2, PLAYSTATION 3, PlayStation 4',
			'disable_path' => '',
			'enable_regex' => 0,
			'restore_UserAgent' => 'Reset Settings to Default UserAgent',
		);
		$expected = array(
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

		$actual = $this->multi_device_switcher->validate_options( $input );

		$this->assertTrue( is_array( $actual ) );
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * @test
	 * @group validate
	 */
	function validate_add_custom_switcher_theme_case_1() {
		$input = array(
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
			'add_custom_switcher' => 'Add',
			'custom_switcher' => 'test',
		);
		$expected = array(
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
			'custom_switcher_theme_test' => 'None',
			'custom_switcher_userAgent_test' => '',
		);

		$actual = $this->multi_device_switcher->validate_options( $input );

		$this->assertTrue( is_array( $actual ) );
		$this->assertEquals( $expected, $actual );
		$this->assertArrayHasKey( 'custom_switcher_theme_test', $actual );
		$this->assertArrayHasKey( 'custom_switcher_userAgent_test', $actual );
		$this->assertEquals( 'None', $actual['custom_switcher_theme_test'] );
		$this->assertEquals( '', $actual['custom_switcher_userAgent_test'] );
	}

	/**
	 * @test
	 * @group validate
	 */
	function validate_add_custom_switcher_theme_case_2() {
		$input = array(
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
			'add_custom_switcher' => 'Add',
			'custom_switcher' => 'smart',
		);
		$expected = array(
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

		$actual = $this->multi_device_switcher->validate_options( $input );

		$this->assertTrue( is_array( $actual ) );
		$this->assertEquals( $expected, $actual );
		$this->assertFalse( isset( $actual['custom_switcher_theme_test'] ) );
		$this->assertFalse( isset( $actual['custom_switcher_userAgent_test'] ) );
	}

	/**
	 * @test
	 * @group validate
	 */
	function validate_add_custom_switcher_theme_case_3() {
		$input = array(
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
			'add_custom_switcher' => 'Add',
			'custom_switcher' => 'testTESTtestTESTtestTEST',
		);
		$expected = array(
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

		$actual = $this->multi_device_switcher->validate_options( $input );

		$this->assertTrue( is_array( $actual ) );
		$this->assertEquals( $expected, $actual );
		$this->assertFalse( isset( $actual['custom_switcher_theme_test'] ) );
		$this->assertFalse( isset( $actual['custom_switcher_userAgent_test'] ) );
	}

	/**
	 * @test
	 * @group validate
	 */
	function validate_delete_custom_switcher_theme_case_1() {
		$input = array(
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
			'delete_custom_switcher_test' => 'Delete',
			'custom_switcher_theme_test' => 'None',
			'custom_switcher_userAgent_test' => 'test1,test2',
		);
		$expected = array(
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

		$actual = $this->multi_device_switcher->validate_options( $input );

		$this->assertTrue( is_array( $actual ) );
		$this->assertEquals( $expected, $actual );
		$this->assertFalse( isset( $actual['custom_switcher_theme_test'] ) );
		$this->assertFalse( isset( $actual['custom_switcher_userAgent_test'] ) );
	}

	/**
	 * @test
	 * @group validate
	 */
	function validate_add_custom_switcher_useragent_case_1() {
		$input = array(
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
			'custom_switcher_theme_test' => 'Twenty Sixteen',
			'custom_switcher_userAgent_test' => 'test1,test2',
		);
		$expected = array(
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
			'custom_switcher_theme_test' => 'Twenty Sixteen',
			'custom_switcher_userAgent_test' => 'test1,test2',
		);

		$actual = $this->multi_device_switcher->validate_options( $input );

		$this->assertTrue( is_array( $actual ) );
		$this->assertEquals( $expected, $actual );
		$this->assertEquals( 'Twenty Sixteen', $actual['custom_switcher_theme_test'] );
		$this->assertEquals( 'test1,test2', $actual['custom_switcher_userAgent_test'] );
	}

	/**
	 * @test
	 * @group validate
	 */
	function validate_case_via_filter() {
		$input = array();
		$expected = 'test';

		add_filter( 'multi_device_switcher/validate_options', function( $output, $input, $default_options ) {
			return 'test';
		}, 10, 3 );

		$actual = $this->multi_device_switcher->validate_options( $input );

		$this->assertEquals( $expected, $actual );
	}

}
