<?php

class Test_Multi_Device_Switcher_Cdn extends WP_UnitTestCase {
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
		parent::tearDown();
	}

	/**
	 * @test
	 * @group cdn
	 * @See https://docs.aws.amazon.com/AmazonCloudFront/latest/DeveloperGuide/header-caching.html#header-caching-web-device
	 */
	function detect_device_action_hook_cloudfront() {
		$GLOBALS['_SERVER']['HTTP_USER_AGENT'] = 'Amazon CloudFront';

		$GLOBALS['_SERVER']['CloudFront-Is-Desktop-Viewer'] = true;
		$GLOBALS['_SERVER']['CloudFront-Is-Mobile-Viewer'] = false;
		$GLOBALS['_SERVER']['CloudFront-Is-SmartTV-Viewer'] = false;
		$GLOBALS['_SERVER']['CloudFront-Is-Tablet-Viewer'] = false;

		add_action( 'multi_device_switcher/detect_device', array( $this, '_set_device_cloudfront' ) );
		$this->multi_device_switcher->detect_device();

		$this->assertSame( '', $this->multi_device_switcher->device );

		$GLOBALS['_SERVER']['CloudFront-Is-Desktop-Viewer'] = false;
		$GLOBALS['_SERVER']['CloudFront-Is-Mobile-Viewer'] = true;
		$GLOBALS['_SERVER']['CloudFront-Is-SmartTV-Viewer'] = false;
		$GLOBALS['_SERVER']['CloudFront-Is-Tablet-Viewer'] = false;

		add_action( 'multi_device_switcher/detect_device', array( $this, '_set_device_cloudfront' ) );
		$this->multi_device_switcher->detect_device();

		$this->assertSame( 'smart', $this->multi_device_switcher->device );

		$GLOBALS['_SERVER']['CloudFront-Is-Desktop-Viewer'] = false;
		$GLOBALS['_SERVER']['CloudFront-Is-Mobile-Viewer'] = false;
		$GLOBALS['_SERVER']['CloudFront-Is-SmartTV-Viewer'] = true;
		$GLOBALS['_SERVER']['CloudFront-Is-Tablet-Viewer'] = false;

		add_action( 'multi_device_switcher/detect_device', array( $this, '_set_device_cloudfront' ) );
		$this->multi_device_switcher->detect_device();

		$this->assertSame( 'game', $this->multi_device_switcher->device );

		$GLOBALS['_SERVER']['CloudFront-Is-Desktop-Viewer'] = false;
		$GLOBALS['_SERVER']['CloudFront-Is-Mobile-Viewer'] = true;
		$GLOBALS['_SERVER']['CloudFront-Is-SmartTV-Viewer'] = false;
		$GLOBALS['_SERVER']['CloudFront-Is-Tablet-Viewer'] = true;

		add_action( 'multi_device_switcher/detect_device', array( $this, '_set_device_cloudfront' ) );
		$this->multi_device_switcher->detect_device();

		$this->assertSame( 'tablet', $this->multi_device_switcher->device );

		unset( $GLOBALS['_SERVER']['HTTP_USER_AGENT'] );
	}

	function _set_device_cloudfront() {
		if ( $GLOBALS['_SERVER']['CloudFront-Is-Desktop-Viewer'] === true ) {
			/* pc */
		}
		elseif ( $GLOBALS['_SERVER']['CloudFront-Is-Tablet-Viewer'] === true && $GLOBALS['_SERVER']['CloudFront-Is-Mobile-Viewer'] === true ) {
			$this->multi_device_switcher->device = "tablet";
		}
		elseif ( $GLOBALS['_SERVER']['CloudFront-Is-Mobile-Viewer'] === true ) {
			$this->multi_device_switcher->device = "smart";
		}
		elseif ( $GLOBALS['_SERVER']['CloudFront-Is-SmartTV-Viewer'] === true ) {
			$this->multi_device_switcher->device = "game";
		}
	}

	/**
	 * @test
	 * @group cdn
	 * @See https://docs.fastly.com/guides/vcl-tutorials/delivering-different-content-to-different-devices
	 */
	function detect_device_action_hook_fastly() {

		$GLOBALS['_SERVER']['X-UA-Device'] = "desktop";

		add_action( 'multi_device_switcher/detect_device', array( $this, '_set_device_fastly' ) );
		$this->multi_device_switcher->detect_device();

		$this->assertSame( '', $this->multi_device_switcher->device );

		$GLOBALS['_SERVER']['X-UA-Device'] = "smartphone";

		add_action( 'multi_device_switcher/detect_device', array( $this, '_set_device_fastly' ) );
		$this->multi_device_switcher->detect_device();

		$this->assertSame( 'smart', $this->multi_device_switcher->device );

		$GLOBALS['_SERVER']['X-UA-Device'] = "tablet";

		add_action( 'multi_device_switcher/detect_device', array( $this, '_set_device_fastly' ) );
		$this->multi_device_switcher->detect_device();

		$this->assertSame( 'tablet', $this->multi_device_switcher->device );

		$GLOBALS['_SERVER']['X-UA-Device'] = "mobile";

		add_action( 'multi_device_switcher/detect_device', array( $this, '_set_device_fastly' ) );
		$this->multi_device_switcher->detect_device();

		$this->assertSame( 'mobile', $this->multi_device_switcher->device );
	}

	function _set_device_fastly() {
		if ( $GLOBALS['_SERVER']['X-UA-Device'] === "desktop" ) {
			/* pc */
		}
		elseif ( $GLOBALS['_SERVER']['X-UA-Device'] === "smartphone" ) {
			$this->multi_device_switcher->device = "smart";
		}
		elseif ( $GLOBALS['_SERVER']['X-UA-Device'] === "tablet" ) {
			$this->multi_device_switcher->device = "tablet";
		}
		elseif ( $GLOBALS['_SERVER']['X-UA-Device'] === "mobile" ) {
			$this->multi_device_switcher->device = "mobile";
		}
	}

}
