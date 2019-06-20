<?php

class Test_Multi_Device_Switcher_Options extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->multi_device_switcher = new Multi_Device_Switcher();
	}

	/**
	 * @test
	 * @group options
	 */
	function get_options_default() {
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

		$actual  = $this->multi_device_switcher->get_options();

		$this->assertTrue( is_array( $actual ) );
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * @test
	 * @group options
	 */
	function get_options_case_1() {
		$options = array();
		update_option( 'multi_device_switcher_options', $options );

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

		$actual = $this->multi_device_switcher->get_options();

		$this->assertTrue( is_array( $actual ) );
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * @test
	 * @group options
	 */
	public function get_options_case_filters() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

}
