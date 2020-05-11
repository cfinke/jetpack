<?php
/**
 * This file contains the unit tests for the Grunion_Contact_Form_Field_Data class.
 *
 * @package jetpack
 */

require_jetpack_file( 'modules/contact-form/class-grunion-contact-form-field-data.php' );

/**
 * Test class for Grunion_Contact_Form_Field_Data class.
 *
 * @covers Grunion_Contact_Form_Field_Data
 */
class WP_Test_Grunion_Contact_Form_Field_Data extends WP_UnitTestCase {

	const TEST_ATTRS = array(
		'label'       => 'test label',
		'type'        => 'email',
		'required'    => '1',
		'options'     => '',
		'id'          => 'test_id',
		'default'     => '',
		'values'      => '',
		'placeholder' => '',
		'class'       => '',
	);

	const TEST_VALUE = 'test value';

	/**
	 * This method is called before each unit test.
	 */
	public function setUp() {
		parent::setUp();

		$field        = new Grunion_Contact_Form_Field( self::TEST_ATTRS );
		$field->value = self::TEST_VALUE;

		$this->form_field_data = new Grunion_Contact_Form_Field_Data( $field );
	}

	/**
	 * Tests the Grunion_Contact_Form_Field_Data::get_attributes() method.
	 *
	 * @covers Grunion_Contact_Form_Field_Data::get_attributes
	 */
	public function test_get_attributes() {
		$this->assertEquals( self::TEST_ATTRS, $this->form_field_data->get_attributes() );
	}

	/**
	 * Tests the Grunion_Contact_Form_Field_Data::get_value() method.
	 *
	 * @covers Grunion_Contact_Form_Field_Data::get_value
	 */
	public function test_get_value() {
		$this->assertEquals( self::TEST_VALUE, $this->form_field_data->get_value() );
	}

	/**
	 * Tests the Grunion_Contact_Form_Field_Data::get_attribute() method with a valid
	 * attribute name.
	 *
	 * @covers Grunion_Contact_Form_Field_Data::get_attribute
	 */
	public function test_get_attribute_valid_attr() {
		$this->assertEquals( self::TEST_ATTRS['id'], $this->form_field_data->get_attribute( 'id' ) );
	}

	/**
	 * Tests the Grunion_Contact_Form_Field_Data::get_attribute() method with a null input
	 *
	 * @covers Grunion_Contact_Form_Field_Data::get_attribute
	 */
	public function test_get_attribute_null_input() {
		$this->assertNull( $this->form_field_data->get_attribute( null ) );
	}

	/**
	 * Tests the Grunion_Contact_Form_Field_Data::get_attribute() method with an invalid
	 * attribute name.
	 *
	 * @covers Grunion_Contact_Form_Field_Data::get_attribute
	 */
	public function test_get_attribute_invalid_attr() {
		$this->assertNull( $this->form_field_data->get_attribute( 'not_an_attribute' ) );
	}
}
