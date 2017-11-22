<?php

class Test_Multi_Device_Switcher_wp_cli extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->multi_device_switcher = new Multi_Device_Switcher();
	}

	/**
	 * @test
	 * @group wp_cli
	 */
	function wp_cli() {
		// $this->assertTrue( class_exists( 'Multi_Device_Switcher_Command' ) );
	}
}
