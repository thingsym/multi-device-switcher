<?php

class Test_Multi_Device_Switcher_Functions extends WP_UnitTestCase {

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
	 * @group functions
	 */
	function get_options_user_agent() {
		$userAgent = $this->multi_device_switcher->get_options_user_agent();

		$expected = array(
			'smart' => array('iPhone', 'iPod', 'Android.*Mobile', 'dream', 'CUPCAKE', 'Windows Phone', 'IEMobile.*Touch', 'webOS', 'BB10.*Mobile', 'BlackBerry.*Mobile', 'Mobile.*Gecko'),
			'tablet' => array('iPad', 'Kindle', 'Silk', 'Android(?!.*Mobile)', 'Windows.*Touch', 'PlayBook', 'Tablet.*Gecko'),
			'mobile' => array('DoCoMo', 'SoftBank', 'J-PHONE', 'Vodafone', 'KDDI', 'UP.Browser', 'WILLCOM', 'emobile', 'DDIPOCKET', 'Windows CE', 'BlackBerry', 'Symbian', 'PalmOS', 'Huawei', 'IAC', 'Nokia'),
			'game' => array('PlayStation Portable', 'PlayStation Vita', 'PSP', 'PS2', 'PLAYSTATION 3', 'PlayStation 4', 'Nitro', 'Nintendo 3DS', 'Nintendo Wii', 'Nintendo WiiU', 'Xbox'),
			'custom_switcher_test' => array('test1', 'test2'),
		);

		$this->assertTrue( is_array($userAgent) );
		$this->assertEquals( $expected, $userAgent );
	}

	/**
	 * @test
	 * @group functions
	 */
	function get_stylesheet() {
		$this->assertEquals( '', $this->multi_device_switcher->get_stylesheet() );

		$this->multi_device_switcher->device = 'smart';
		$this->assertEquals( 'twentysixteen', $this->multi_device_switcher->get_stylesheet( $stylesheet = '' ) );

		$this->multi_device_switcher->device = 'custom_switcher_test';
		$this->assertEquals( 'twentysixteen', $this->multi_device_switcher->get_stylesheet() );
	}

	/**
	 * @test
	 * @group functions
	 */
	function get_template() {
		$this->assertEquals( '', $this->multi_device_switcher->get_template() );

		$this->multi_device_switcher->device = 'smart';
		$this->assertEquals( 'twentysixteen', $this->multi_device_switcher->get_template() );

		$this->multi_device_switcher->device = 'custom_switcher_test';
		$this->assertEquals( 'twentysixteen', $this->multi_device_switcher->get_template() );
	}

	/**
	 * @test
	 * @group functions
	 */
	function get_device_theme() {
		$this->assertEmpty( $this->multi_device_switcher->get_device_theme() );

		$this->multi_device_switcher->device = 'smart';
		$this->assertEquals( 'Twenty Sixteen', $this->multi_device_switcher->get_device_theme() );

		$this->multi_device_switcher->device = 'custom_switcher_test';
		$this->assertEquals( 'Twenty Sixteen', $this->multi_device_switcher->get_device_theme() );
	}

	/**
	 * @test
	 * @group functions
	 */
	function session() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );

		// $this->assertEquals( '', $this->multi_device_switcher->session() );
	}

	/**
	 * @test
	 * @group functions
	 */
	function add_pc_switcher() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );

		// $this->multi_device_switcher->device = 'smart';
		// $this->assertEquals( '', $this->multi_device_switcher->add_pc_switcher() );
		// $this->assertTrue( wp_style_is( 'pc-switcher-options' ) );
	}

	/**
	 * @test
	 * @group functions
	 */
	function is_multi_device() {
		$this->assertTrue( $this->multi_device_switcher->is_multi_device() );
		$this->assertFalse( $this->multi_device_switcher->is_multi_device( 'smart' ) );

		$this->multi_device_switcher->device = 'smart';
		$this->assertTrue( $this->multi_device_switcher->is_multi_device( 'smart' ) );

		$this->multi_device_switcher->device = 'custom_switcher_test';
		$this->assertTrue( $this->multi_device_switcher->is_multi_device( 'test' ) );
	}

	/**
	 * @test
	 * @group functions
	 */
	function is_pc_switcher() {
		$this->assertFalse( $this->multi_device_switcher->is_pc_switcher() );

		$GLOBALS['_COOKIE']['pc-switcher'] = 1;
		$this->assertTrue( $this->multi_device_switcher->is_pc_switcher() );
		unset($GLOBALS['_COOKIE']['pc-switcher']);
	}

	/**
	 * @test
	 * @group functions
	 */
	function is_disable_switcher() {
		$this->assertFalse( $this->multi_device_switcher->is_disable_switcher() );

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
		$this->assertTrue( $this->multi_device_switcher->is_disable_switcher() );

		$GLOBALS['_SERVER']['REQUEST_URI'] = '/abc';
		$this->assertTrue( $this->multi_device_switcher->is_disable_switcher() );

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
			'disable_path' => "^\/te\nbc$\n",
			'enable_regex' => 1,
			'custom_switcher_theme_test' => 'Twenty Sixteen',
			'custom_switcher_userAgent_test' => 'test1,test2',
		);

		update_option( 'multi_device_switcher_options', $options );

		$GLOBALS['_SERVER']['REQUEST_URI'] = '/test';
		$this->assertTrue( $this->multi_device_switcher->is_disable_switcher() );

		$GLOBALS['_SERVER']['REQUEST_URI'] = '/abc';
		$this->assertTrue( $this->multi_device_switcher->is_disable_switcher() );

		unset($GLOBALS['_SERVER']['REQUEST_URI']);
	}

	/**
	 * @test
	 * @group functions
	 */
	function shortcode_display_switcher() {
		$atts = array(
			'device' => '',
		);
		$content = '';
		$this->assertEquals( $content, $this->multi_device_switcher->shortcode_display_switcher( $atts, $content ) );

		$atts = array(
			'device' => '',
		);
		$content = "test";
		$this->assertEquals( $content, $this->multi_device_switcher->shortcode_display_switcher( $atts, $content ) );

		$this->multi_device_switcher->device = 'smart';
		$atts = array(
			'device' => 'smart',
		);
		$content = "test";
		$this->assertEquals( $content, $this->multi_device_switcher->shortcode_display_switcher( $atts, $content ) );

		$GLOBALS['_COOKIE']['pc-switcher'] = 1;
		$this->multi_device_switcher->device = 'smart';
		$atts = array(
			'device' => 'smart',
		);
		$content = "test";
		$this->assertEquals( '', $this->multi_device_switcher->shortcode_display_switcher( $atts, $content ) );
		unset($GLOBALS['_COOKIE']['pc-switcher']);
	}

	/**
	 * @test
	 * @group functions
	 */
	function add_header_vary() {
		$headers = $this->multi_device_switcher->add_header_vary( array() );
		$this->assertEquals( 'User-Agent', $headers['Vary'] );

		add_filter( 'multi_device_switcher/add_header_vary', function( $value ) {
			return 'Accept-Encoding';
		} );

		$headers = $this->multi_device_switcher->add_header_vary( array() );
		$this->assertEquals( 'Accept-Encoding', $headers['Vary'] );

		add_filter( 'multi_device_switcher/add_header_vary', function( $value ) {
			return null;
		} );

		$headers = $this->multi_device_switcher->add_header_vary( array() );
		$this->assertNull( $headers['Vary'] );
	}

	/**
	 * @test
	 * @group functions
	 */
	function admin_enqueue() {
		$this->multi_device_switcher->load_plugin_data();

		$this->multi_device_switcher->admin_enqueue_scripts( '' );
		$this->assertTrue( wp_script_is( 'multi-device-switcher-options' ) );

		$this->multi_device_switcher->admin_enqueue_styles( '' );
		$this->assertTrue( wp_style_is( 'multi-device-switcher-options' ) );
	}

	/**
	 * @test
	 * @group functions
	 */
	function admin_init() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	/**
	 * @test
	 * @group functions
	 */
	function option_page_capability() {
		$this->assertEquals( 'switch_themes', $this->multi_device_switcher->option_page_capability() );
	}

	/**
	 * @test
	 * @group functions
	 */
	function add_option_page() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );

		$this->multi_device_switcher->add_option_page();

		// $this->assertEquals( 10, has_action( 'load-appearance_page_multi-device-switcher', array( $this->multi_device_switcher, 'page_hook_suffix' ) ) );
	}

	/**
	 * @test
	 * @group functions
	 */
	function page_hook_suffix() {
		$this->multi_device_switcher->page_hook_suffix();

		$this->assertEquals( 10, has_action( 'admin_enqueue_scripts', array( $this->multi_device_switcher, 'admin_enqueue_scripts' ) ) );
		$this->assertEquals( 10, has_action( 'admin_enqueue_scripts', array( $this->multi_device_switcher, 'admin_enqueue_styles' ) ) );
	}

	/**
	 * @test
	 * @group functions
	 */
	function plugin_action_links() {
		$links = $this->multi_device_switcher->plugin_action_links( array() );
		$this->assertContains( 'themes.php?page=multi-device-switcher', $links[0] );
	}

	/**
	 * @test
	 * @group functions
	 */
	function render_option_page() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	/**
	 * @test
	 * @group functions
	 */
	public function plugin_metadata_links() {
		$links = $this->multi_device_switcher->plugin_metadata_links( array(), plugin_basename( __MULTI_DEVICE_SWITCHER_FILE__ ) );
		$this->assertContains( '<a href="https://github.com/sponsors/thingsym">Become a sponsor</a>', $links );
	}

	/**
	 * @test
	 * @group functions
	 */
	function customize_register() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	/**
	 * @test
	 * @group functions
	 */
	function load_file() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );

		// $this->multi_device_switcher->load_file();
		// var_dump(get_included_files());
	}

	/**
	 * @test
	 * @group functions
	 */
	function load_plugin_data() {
		$this->assertTrue( $this->multi_device_switcher->load_plugin_data() );
	}

}
