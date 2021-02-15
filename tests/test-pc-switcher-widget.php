<?php

class Test_pc_switcher_Pc_Switcher_Widget extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->pc_switcher = new PC_Switcher();
	}

	/**
	 * @test
	 * @group widget
	 */
	function is_class() {
		$this->assertTrue( class_exists( 'PC_Switcher' ) );
	}

	/**
	 * @test
	 * @group widget
	 */
	function constructor() {
		$this->assertEquals( 'pc-switcher', $this->pc_switcher->id_base );
		$this->assertEquals( 'PC Switcher', $this->pc_switcher->name );

		$this->assertEquals( 'widget_pc-switcher', $this->pc_switcher->option_name );
		$this->assertEquals( 'widget_pc_switcher', $this->pc_switcher->alt_option_name );

		$this->assertArrayHasKey( 'classname', $this->pc_switcher->widget_options );
		$this->assertEquals( 'widget_pc_switcher', $this->pc_switcher->widget_options['classname'] );
		$this->assertArrayHasKey( 'description', $this->pc_switcher->widget_options );
		$this->assertContains( 'Add the PC Switcher to a widget.', $this->pc_switcher->widget_options['description'] );
		$this->assertArrayHasKey( 'customize_selective_refresh', $this->pc_switcher->widget_options );
		$this->assertTrue( $this->pc_switcher->widget_options['customize_selective_refresh'] );

		$this->assertArrayHasKey( 'id_base', $this->pc_switcher->control_options );
		$this->assertEquals( 'pc-switcher', $this->pc_switcher->control_options['id_base'] );
	}

	/**
	 * @test
	 * @group widget
	 */
	function widget() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	/**
	 * @test
	 * @group widget
	 */
	function update_case() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	/**
	 * @test
	 * @group widget
	 */
	function form() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

}
