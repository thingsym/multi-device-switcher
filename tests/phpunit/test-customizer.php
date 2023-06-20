<?php

class Test_Multi_Device_Switcher_Customizer extends WP_UnitTestCase {
	public $multi_device_switcher;
	public $wp_customize;

	public function setUp(): void {
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
		$this->assertSame( 'multi_device_switcher', $section->id );
		$this->assertSame( 80, $section->priority );
		$this->assertSame( 'Multi Device Switcher', $section->title );


		$setting = $this->wp_customize->get_setting( 'multi_device_switcher_options[theme_smartphone]' );
		$this->assertSame( 'multi_device_switcher_options[theme_smartphone]', $setting->id );
		$this->assertSame( 'option', $setting->type );
		$this->assertSame( 'switch_themes', $setting->capability );
		$this->assertSame( 'None', $setting->default );
		$this->assertSame( 'refresh', $setting->transport );

		$this->assertSame( 'None', $setting->value() );

		$control = $this->wp_customize->get_control( 'multi_device_switcher_options[theme_smartphone]' );
		$this->assertSame( 'multi_device_switcher', $control->section );
		$this->assertSame( 'select', $control->type );
		$this->assertIsArray( $control->choices );


		$setting = $this->wp_customize->get_setting( 'multi_device_switcher_options[theme_tablet]' );
		$this->assertSame( 'multi_device_switcher_options[theme_tablet]', $setting->id );
		$this->assertSame( 'option', $setting->type );
		$this->assertSame( 'switch_themes', $setting->capability );
		$this->assertSame( 'None', $setting->default );
		$this->assertSame( 'refresh', $setting->transport );

		$this->assertSame( 'None', $setting->value() );

		$control = $this->wp_customize->get_control( 'multi_device_switcher_options[theme_tablet]' );
		$this->assertSame( 'multi_device_switcher', $control->section );
		$this->assertSame( 'select', $control->type );
		$this->assertIsArray( $control->choices );


		$setting = $this->wp_customize->get_setting( 'multi_device_switcher_options[theme_mobile]' );
		$this->assertSame( 'multi_device_switcher_options[theme_mobile]', $setting->id );
		$this->assertSame( 'option', $setting->type );
		$this->assertSame( 'switch_themes', $setting->capability );
		$this->assertSame( 'None', $setting->default );
		$this->assertSame( 'refresh', $setting->transport );

		$this->assertSame( 'None', $setting->value() );

		$control = $this->wp_customize->get_control( 'multi_device_switcher_options[theme_mobile]' );
		$this->assertSame( 'multi_device_switcher', $control->section );
		$this->assertSame( 'select', $control->type );
		$this->assertIsArray( $control->choices );


		$setting = $this->wp_customize->get_setting( 'multi_device_switcher_options[theme_game]' );
		$this->assertSame( 'multi_device_switcher_options[theme_game]', $setting->id );
		$this->assertSame( 'option', $setting->type );
		$this->assertSame( 'switch_themes', $setting->capability );
		$this->assertSame( 'None', $setting->default );
		$this->assertSame( 'refresh', $setting->transport );

		$this->assertSame( 'None', $setting->value() );

		$control = $this->wp_customize->get_control( 'multi_device_switcher_options[theme_game]' );
		$this->assertSame( 'multi_device_switcher', $control->section );
		$this->assertSame( 'select', $control->type );
		$this->assertIsArray( $control->choices );
	}

	/**
	 * @test
	 * @group customizer
	 */
	function save_case() {
		$this->wp_customize->set_post_value( 'multi_device_switcher_options[theme_smartphone]', 'none' );
		$setting = $this->wp_customize->get_setting( 'multi_device_switcher_options[theme_smartphone]' );
		$setting->save();
		$this->assertSame( 'none', $setting->value() );

		$option = $this->multi_device_switcher->get_options( 'theme_smartphone' );
		$this->assertSame( 'none', $option );

		$this->wp_customize->set_post_value( 'multi_device_switcher_options[theme_tablet]', 'none' );
		$setting = $this->wp_customize->get_setting( 'multi_device_switcher_options[theme_tablet]' );
		$setting->save();
		$this->assertSame( 'none', $setting->value() );

		$option = $this->multi_device_switcher->get_options( 'theme_tablet' );
		$this->assertSame( 'none', $option );

		$this->wp_customize->set_post_value( 'multi_device_switcher_options[theme_mobile]', 'none' );
		$setting = $this->wp_customize->get_setting( 'multi_device_switcher_options[theme_mobile]' );
		$setting->save();
		$this->assertSame( 'none', $setting->value() );

		$option = $this->multi_device_switcher->get_options( 'theme_mobile' );
		$this->assertSame( 'none', $option );

		$this->wp_customize->set_post_value( 'multi_device_switcher_options[theme_game]', 'none' );
		$setting = $this->wp_customize->get_setting( 'multi_device_switcher_options[theme_game]' );
		$setting->save();
		$this->assertSame( 'none', $setting->value() );

		$option = $this->multi_device_switcher->get_options( 'theme_game' );
		$this->assertSame( 'none', $option );
	}

}
