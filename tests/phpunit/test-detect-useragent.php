<?php

class Test_Detect_Useragent extends WP_UnitTestCase {
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
			'custom_switcher_theme_tv' => 'Twenty Sixteen',
			'custom_switcher_userAgent_tv' => 'AFTB,AppleTV',
			'custom_switcher_theme_inAppBrowser' => 'Twenty Sixteen',
			'custom_switcher_userAgent_inAppBrowser' => 'Instagram, Line, FBAN, FBAV, MicroMessenger',
		);

		update_option( 'multi_device_switcher_options', $options );
	}

	/**
	 * @test
	 * @group detect_useragent
	 */
	function detect_useragent() {
		$useragent_case = array(
			array(
				'useragent' => 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)',
				'device'    => '',
				'massage'   => 'Desktop',
			),
			array(
				'useragent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36',
				'device'    => '',
				'massage'   => 'Desktop',
			),
			array(
				'useragent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:43.0) Gecko/20100101 Firefox/43.0',
				'device'    => '',
				'massage'   => 'Desktop',
			),
			array(
				'useragent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1 Mobile/15E148 Safari/604.1',
				'device'    => 'smart',
				'massage'   => 'iPhone',
			),
			array(
				'useragent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/91.0.4472.80 Mobile/15E148 Safari/604.1',
				'device'    => 'smart',
				'massage'   => 'iPhone',
			),
			array(
				'useragent' => 'Mozilla/5.0 (Linux; Android 11; Pixel 4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.210 Mobile Safari/537.36',
				'device'    => 'smart',
				'massage'   => 'Android',
			),
			array(
				'useragent' => 'Mozilla/5.0 (Linux; Android 11; Pixel 4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.210 Mobile Safari/537.36',
				'device'    => 'smart',
				'massage'   => 'Android',
			),
			array(
				'useragent' => 'Mozilla/5.0 (Mobile; LGL25; rv:32.0) Gecko/32.0 Firefox/32.0',
				'device'    => 'smart',
				'massage'   => 'Firefox',
			),
			array(
				'useragent' => 'Mozilla/5.0 (BlackBerry; U; BlackBerry 9320; en-GB) AppleWebKit/534.11+ (KHTML, like Gecko) Version/7.1.0.398 Mobile Safari/534.11+',
				'device'    => 'smart',
				'massage'   => 'BlackBerry',
			),
			array(
				'useragent' => 'Mozilla/5.0 (iPad; CPU OS 10_0_1 like Mac OS X) AppleWebKit/602.1.50 (KHTML, like Gecko) Version/10.0 Mobile/14A403 Safari/602.1',
				'device'    => 'tablet',
				'massage'   => 'iPad',
			),
			array(
				'useragent' => 'Mozilla/5.0 (Linux; Android 5.0.2; SM-T530 Build/LRX22G) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.83 Safari/537.36',
				'device'    => 'tablet',
				'massage'   => 'Android',
			),
			array(
				'useragent' => 'Mozilla/5.0 (Linux; U; Android 2.3.4; en-us; Kindle Fire Build/GINGERBREAD) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1',
				'device'    => 'tablet',
				'massage'   => 'Kindle Fire',
			),
			array(
				'useragent' => 'Mozilla/5.0 (Linux; U; Android 2.3.4; en-us; Silk/1.0.146.3-Gen4_12000410) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1 Silk-Accelerated=true',
				'device'    => 'tablet',
				'massage'   => 'Kindle Fire',
			),
			array(
				'useragent' => 'DoCoMo/2.0 F2051(c100;TB;serXXXXXXXXXXXXXXX;iccxxxxxxxxxxxxxxxxxxxx)',
				'device'    => 'mobile',
				'massage'   => 'DoCoMo',
			),
			array(
				'useragent' => 'KDDI-HI31 UP.Browser/6.2.0.5 (GUI) MMP/2.0',
				'device'    => 'mobile',
				'massage'   => 'KDDI',
			),
			array(
				'useragent' => 'Vodafone/1.0/V904SH/SHJ001/SN  Browser/VF-NetFront/3.3 Profile/MIDP-2.0 Configuration/CLDC-1.1',
				'device'    => 'mobile',
				'massage'   => 'SoftBank',
			),
			array(
				'useragent' => 'SoftBank/1.0/910T/TJ001/SN Browser/NetFront/3.3 Profile/MIDP-2.0 Configuration/CLDC-1.1',
				'device'    => 'mobile',
				'massage'   => 'SoftBank',
			),
			array(
				'useragent' => 'Mozilla/5.0 (Nintendo WiiU) AppleWebKit/536.28',
				'device'    => 'game',
				'massage'   => 'Nintendo Wii',
			),
			array(
				'useragent' => 'Mozilla/5.0 (PLAYSTATION 3 4.11) AppleWebKit/531.22.8 (KHTML, like Gecko)',
				'device'    => 'game',
				'massage'   => 'PlayStation 3',
			),
			array(
				'useragent' => 'Mozilla/4.0 (PSP (PlayStation Portable); 2.00)',
				'device'    => 'game',
				'massage'   => 'Playstation Portable',
			),
			array(
				'useragent' => 'Mozilla/5.0 (Playstation Vita 2.02) AppleWebKit/536.26 (KHTML, like Gecko)﻿ Silk/3.2﻿',
				'device'    => 'game',
				'massage'   => 'Playstation Vita',
			),
			array(
				'useragent' => 'Mozilla/5.0 (PlayStation 4 1.52) AppleWebKit/536.26 (KHTML, like Gecko)',
				'device'    => 'game',
				'massage'   => 'PlayStation 4',
			),
			array(
				'useragent' => 'Mozilla/5.0 (PlayStation 5/SmartTV) AppleWebKit/605.1.15 (KHTML, like Gecko)',
				'device'    => 'game',
				'massage'   => 'PlayStation 5',
			),
			array(
				'useragent' => 'Mozilla/5.0 (PlayStation Vita 1.50) AppleWebKit/531.22.8 (KHTML, like Gecko) Silk/3.2',
				'device'    => 'game',
				'massage'   => 'PlayStation Vita',
			),
			array(
				'useragent' => 'Mozilla/5.0 (Nintendo Switch; ShareApplet) AppleWebKit/601.6 (KHTML, like Gecko) NF/4.0.0.5.9 NintendoBrowser/5.1.0.13341',
				'device'    => 'game',
				'massage'   => 'Nintendo Switch',
			),
			array(
				'useragent' => 'Mozilla/5.0 (Nintendo WiiU) AppleWebKit/536.30 (KHTML, like Gecko) NX/3.0.4.2.11 NintendoBrowser/4.3.0.11224.US',
				'device'    => 'game',
				'massage'   => 'Nintendo WiiU',
			),
			array(
				'useragent' => 'Mozilla/5.0 (New Nintendo 3DS like iPhone) AppleWebKit/536.30 (KHTML, like Gecko) NX/3.0.0.5.20 Mobile NintendoBrowser/1.8.10156.US',
				'device'    => 'game',
				'massage'   => 'Nintendo 3DS',
			),
			array(
				'useragent' => 'Mozilla/5.0 (Windows NT 10.0,; Win64; x64; Xbox; Xbox One) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2486.0 Safari/537.36 Edge/13.10553',
				'device'    => 'game',
				'massage'   => 'Xbox',
			),

			// Custom Switcher
			array(
				'useragent' => 'Mozilla/5.0 (Linux; U; Android 4.2.2; en-us; AFTB Build/JDQ39) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30',
				'device'    => 'custom_switcher_tv',
				'massage'   => 'Amazon Fire TV',
			),
			array(
				'useragent' => 'Mozilla/5.0 (Linux; Android 4.2.2; AFTB Build/JDQ39) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.173 Mobile Safari/537.22',
				'device'    => 'custom_switcher_tv',
				'massage'   => 'Amazon Fire TV',
			),
			array(
				'useragent' => 'Mozilla/5.0 (Linux; Android 4.2.2; AFTB Build/JDQ39) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.173 Mobile Safari/537.22 cordova-amazon-fireos/3.4.0 AmazonWebAppPlatform/3.4.0;2.0',
				'device'    => 'custom_switcher_tv',
				'massage'   => 'Amazon Fire TV',
			),
			array(
				'useragent' => 'AppleTV6,2/11.1',
				'device'    => 'custom_switcher_tv',
				'massage'   => 'Apple TV',
			),
			array(
				'useragent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 [FBAN/FBIOS;FBDV/iPhone13,3;FBMD/iPhone;FBSN/iOS;FBSV/14.5;FBSS/3;FBID/phone;FBLC/ja_JP;FBOP/5]',
				'device'    => 'custom_switcher_inAppBrowser',
				'massage'   => 'Facebook',
			),
			array(
				'useragent' => 'Mozilla/5.0 (Linux; Android 11; Pixel 4 Build/RQ2A.210405.005; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/90.0.4430.210 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/324.0.0.48.120;]',
				'device'    => 'custom_switcher_inAppBrowser',
				'massage'   => 'Facebook',
			),
			array(
				'useragent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 Instagram 193.0.0.29.121 (iPhone13,3; iOS 14_5; ja_JP; ja-JP; scale=3.00; 1170x2532; 299401192) NW/3',
				'device'    => 'custom_switcher_inAppBrowser',
				'massage'   => 'Instagram',
			),
			array(
				'useragent' => 'Mozilla/5.0 (Linux; Android 11; Pixel 4 Build/RQ2A.210405.005; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/90.0.4430.210 Mobile Safari/537.36 Instagram 193.0.0.45.120 Android (30/11; 440dpi; 1080x2236; Google/google; Pixel 4; flame; flame; ja_JP; 300078998)',
				'device'    => 'custom_switcher_inAppBrowser',
				'massage'   => 'Instagram',
			),
			array(
				'useragent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 Safari Line/11.10.0',
				'device'    => 'custom_switcher_inAppBrowser',
				'massage'   => 'LINE',
			),
			array(
				'useragent' => 'Mozilla/5.0 (Linux; Android 11; Pixel 4 Build/RQ2A.210405.005; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/90.0.4430.210 Mobile Safari/537.36 Line/11.10.2/IAB',
				'device'    => 'custom_switcher_inAppBrowser',
				'massage'   => 'LINE',
			),
			array(
				'useragent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_6_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 MicroMessenger/8.0.30(0x18001e29) NetType/WIFI Language/zh_TW',
				'device'    => 'custom_switcher_inAppBrowser',
				'massage'   => 'WeChat',
			),
		);

		foreach ( $useragent_case as $case ) {
			$GLOBALS['_SERVER']['HTTP_USER_AGENT'] = $case[ 'useragent' ];
			$this->multi_device_switcher->detect_device();
			$this->assertSame( $case[ 'device' ], $this->multi_device_switcher->device, $case[ 'massage' ] );

			$this->multi_device_switcher->device = '';
			unset( $GLOBALS['_SERVER']['HTTP_USER_AGENT'] );
		}
	}

}
