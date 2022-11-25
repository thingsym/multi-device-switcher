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
		$this->assertSame( 'pc-switcher', $this->pc_switcher->id_base );
		$this->assertSame( 'PC Switcher', $this->pc_switcher->name );

		$this->assertSame( 'widget_pc-switcher', $this->pc_switcher->option_name );
		$this->assertSame( 'widget_pc_switcher', $this->pc_switcher->alt_option_name );

		$this->assertArrayHasKey( 'classname', $this->pc_switcher->widget_options );
		$this->assertSame( 'widget_pc_switcher', $this->pc_switcher->widget_options['classname'] );
		$this->assertArrayHasKey( 'description', $this->pc_switcher->widget_options );
		$this->assertContains( 'Add the PC Switcher to a widget.', $this->pc_switcher->widget_options['description'] );
		$this->assertArrayHasKey( 'customize_selective_refresh', $this->pc_switcher->widget_options );
		$this->assertTrue( $this->pc_switcher->widget_options['customize_selective_refresh'] );

		$this->assertArrayHasKey( 'id_base', $this->pc_switcher->control_options );
		$this->assertSame( 'pc-switcher', $this->pc_switcher->control_options['id_base'] );
	}

	/**
	 * @test
	 * @group widget
	 */
	function widget() {
		$args = array(
			'before_widget' => '<aside id="widget-pc-switcher-1" class="widget widget_pc_switcher">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		);
		$instance = array();

		$GLOBALS['_COOKIE']['pc-switcher'] = 1;
		// setcookie( 'pc-switcher', 1 );

		ob_start();
		$this->pc_switcher->widget( $args, $instance );
		$widget = ob_get_clean();

		// var_dump($widget);
		// $this->assertMatchesRegularExpression( '#<h3 class="widget-title">aaaaa</h3>#', $widget );

		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	/**
	 * @test
	 * @group widget
	 */
	function update_case() {
		$new_instance = array();
		$expected     = array();

		$validate = $this->pc_switcher->update( $new_instance, array() );

		$this->assertSame( $validate, $expected );
	}

	/**
	 * @test
	 * @group widget
	 */
	function form() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

}
