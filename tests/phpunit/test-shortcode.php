<?php

class Test_Multi_Device_Switcher_Shortcode extends WP_UnitTestCase {
	public $multi_device_switcher;

	public function setUp(): void {
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
	}

	public function tearDown(): void {
		$GLOBALS['_SERVER']['REQUEST_URI'] = '';
		unset( $GLOBALS['_COOKIE']['pc-switcher'] );

		parent::tearDown();
	}

	/**
	 * @test
	 * @group shortcode
	 */
	function is_shortcode() {
		$this->assertTrue( shortcode_exists( 'multi' ) );
	}

	/**
	 * @test
	 * @group shortcode
	 */
	function shortcode_output_multi() {
		$output = do_shortcode('[multi]pc[/multi]');
		$content = 'pc';
		$this->assertSame( $content, $output );

		// $this->multi_device_switcher->device = 'smart';
		// $GLOBALS['_COOKIE']['pc-switcher'] = 1;

		// global $multi_device_switcher;
		// $multi_device_switcher->device = 'smart';

		// var_dump($this->multi_device_switcher->device);
		// var_dump($this->multi_device_switcher->is_multi_device( 'smart' ));
		// var_dump($this->multi_device_switcher->is_pc_switcher());

		// $output = do_shortcode('[multi device="smart"]smart[/multi]');
		// $content = 'smart';
		// $this->assertSame( $content, $output );

	}

}
