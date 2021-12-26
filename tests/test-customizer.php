<?php

class Test_Multi_Device_Switcher_Customizer extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->multi_device_switcher = new Multi_Device_Switcher();

		require_once ABSPATH . WPINC . '/class-wp-customize-manager.php';

		$user_id = self::factory()->user->create(
			array(
				'role' => 'administrator',
			)
		);
		if ( is_multisite() ) {
			grant_super_admin( $user_id );
		}

		wp_set_current_user( $user_id );

		global $wp_customize;
		$this->wp_customize = new WP_Customize_Manager();
		$wp_customize       = $this->wp_customize;

		do_action( 'customize_register', $this->wp_customize );
	}

	/**
	 * @test
	 * @group customizer
	 */
	function customizer() {
		$section = $this->wp_customize->get_section( 'multi_device_switcher' );
		$this->assertEquals( 'multi_device_switcher', $section->id );
		$this->assertEquals( 80, $section->priority );
		$this->assertEquals( 'Multi Device Switcher', $section->title );


		$setting = $this->wp_customize->get_setting( 'multi_device_switcher_options[theme_smartphone]' );
		$this->assertEquals( 'multi_device_switcher_options[theme_smartphone]', $setting->id );
		$this->assertEquals( 'option', $setting->type );
		$this->assertEquals( 'switch_themes', $setting->capability );
		$this->assertEquals( 'None', $setting->default );
		$this->assertEquals( 'refresh', $setting->transport );

		$this->assertEquals( 'None', $setting->value() );

		$control = $this->wp_customize->get_control( 'multi_device_switcher_options[theme_smartphone]' );
		$this->assertEquals( 'multi_device_switcher', $control->section );
		$this->assertEquals( 'select', $control->type );
		$this->assertIsArray( $control->choices );


		$setting = $this->wp_customize->get_setting( 'multi_device_switcher_options[theme_tablet]' );
		$this->assertEquals( 'multi_device_switcher_options[theme_tablet]', $setting->id );
		$this->assertEquals( 'option', $setting->type );
		$this->assertEquals( 'switch_themes', $setting->capability );
		$this->assertEquals( 'None', $setting->default );
		$this->assertEquals( 'refresh', $setting->transport );

		$this->assertEquals( 'None', $setting->value() );

		$control = $this->wp_customize->get_control( 'multi_device_switcher_options[theme_tablet]' );
		$this->assertEquals( 'multi_device_switcher', $control->section );
		$this->assertEquals( 'select', $control->type );
		$this->assertIsArray( $control->choices );


		$setting = $this->wp_customize->get_setting( 'multi_device_switcher_options[theme_mobile]' );
		$this->assertEquals( 'multi_device_switcher_options[theme_mobile]', $setting->id );
		$this->assertEquals( 'option', $setting->type );
		$this->assertEquals( 'switch_themes', $setting->capability );
		$this->assertEquals( 'None', $setting->default );
		$this->assertEquals( 'refresh', $setting->transport );

		$this->assertEquals( 'None', $setting->value() );

		$control = $this->wp_customize->get_control( 'multi_device_switcher_options[theme_mobile]' );
		$this->assertEquals( 'multi_device_switcher', $control->section );
		$this->assertEquals( 'select', $control->type );
		$this->assertIsArray( $control->choices );


		$setting = $this->wp_customize->get_setting( 'multi_device_switcher_options[theme_game]' );
		$this->assertEquals( 'multi_device_switcher_options[theme_game]', $setting->id );
		$this->assertEquals( 'option', $setting->type );
		$this->assertEquals( 'switch_themes', $setting->capability );
		$this->assertEquals( 'None', $setting->default );
		$this->assertEquals( 'refresh', $setting->transport );

		$this->assertEquals( 'None', $setting->value() );

		$control = $this->wp_customize->get_control( 'multi_device_switcher_options[theme_game]' );
		$this->assertEquals( 'multi_device_switcher', $control->section );
		$this->assertEquals( 'select', $control->type );
		$this->assertIsArray( $control->choices );
	}

	/**
	 * @test
	 * @group customizer
	 */
	function save_case() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

}
