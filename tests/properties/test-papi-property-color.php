<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Unit tests covering property functionality.
 *
 * @package Papi
 */

class WP_Papi_Property_Color extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 *
	 * @since 1.1.0
	 */

	public function setUp() {
		parent::setUp();

		$this->post_id = $this->factory->post->create();

		$this->property = papi_property( array(
			'type'  => 'color',
			'title' => 'The color field',
			'slug'  => 'color_field'
		) );
	}

	/**
	 * Test property options.
	 *
	 * @since 1.1.0
	 */

	public function test_property_options() {
		$this->assertEquals( 'color', $this->property->type );
		$this->assertEquals( 'The color field', $this->property->title );
		$this->assertEquals( 'papi_color_field', $this->property->slug );
	}

	/**
	 * Test save property value.
	 *
	 * @since 1.1.0
	 */

	public function test_save_property_value() {
		$handler = new Papi_Admin_Meta_Boxes();

		// Create post data.
		$_POST = papi_test_create_property_post_data(array(
			'slug'  => $this->property->slug,
			'type'  => $this->property->type,
			'value' => '#000000'
		), $_POST);

		// Save the property using the handler.
		$handler->save_property( $this->post_id );

		// Test get the value with papi_field function.
		$expected = '#000000';
		$actual   = papi_field( $this->post_id, $this->property->slug );

		$this->assertEquals( $expected, $actual );
	}

}
