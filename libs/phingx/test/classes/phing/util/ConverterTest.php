<?php
require_once 'phing/util/Converter.php';

class ConverterTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider providerTestxml2array
	 */
	public function testxml2array( $contents, $actual, $exceptionName = '' )
	{
		if ( $exceptionName )
		{
			$this->setExpectedException( $exceptionName );
		}

		$expected = Converter::xml2array( $contents );
		$this->assertEquals( $expected, $actual );

		//reverse test
		$tmp = Converter::array2xml( $expected );
		$expected = Converter::xml2array( $tmp );
		$this->assertEquals( $expected, $actual );
	}

	public function providerTestxml2array()
	{
		$data = array();

		$source_path = array(
			PHING_TEST_BASE,
			'etc',
			'util',
			'converter',
			'xml2array' );
		$source_path = implode( DIRECTORY_SEPARATOR, $source_path );

		$contents = file_get_contents( $source_path . '/fixture_1.xml' );
		$actual = require $source_path . '/actual_1.php';
		$data[] = array( $contents, $actual );

		$contents = file_get_contents( $source_path . '/fixture_2.xml' );
		$actual = require $source_path . '/actual_2.php';
		$data[] = array( $contents, $actual );

		$contents = file_get_contents( $source_path . '/fixture_3.xml' );
		$actual = require $source_path . '/actual_3.php';
		$data[] = array( $contents, $actual );

		$contents = file_get_contents( $source_path . '/fixture_4.xml' );
		$actual = require $source_path . '/actual_4.php';
		$data[] = array( $contents, $actual );

		$contents = file_get_contents( $source_path . '/fixture_5.xml' );
		$actual = require $source_path . '/actual_5.php';
		$data[] = array( $contents, $actual );

		$contents = file_get_contents( $source_path . '/fixture_6.xml' );
		$actual = require $source_path . '/actual_6.php';
		$data[] = array( $contents, $actual );

		$contents = file_get_contents( $source_path . '/fixture_7.xml' );
		$actual = require $source_path . '/actual_7.php';
		$data[] = array( $contents, $actual );

		return $data;
	}

	/**
	 * @dataProvider providerTestArray2xml
	 */
	public function testArray2xml( $data, $actual, $exceptionName = '' )
	{
		if ( $exceptionName )
		{
			$this->setExpectedException( $exceptionName );
		}

		$expected = Converter::array2xml( $data );
		$this->assertEquals( $expected, $actual );

		//reverse test
		$tmp = Converter::xml2array( $expected );
		$expected = Converter::array2xml( $tmp );
		$this->assertEquals( $expected, $actual );
	}

	public function providerTestArray2xml()
	{
		$data = array();

		$source_path = array(
			PHING_TEST_BASE,
			'etc',
			'util',
			'converter',
			'array2xml' );
		$source_path = implode( DIRECTORY_SEPARATOR, $source_path );

		$ar = include $source_path . '/fixture_1.php';
		$actual = file_get_contents( $source_path . '/actual_1.xml' );
		$data[] = array( $ar, $actual );

		$ar = include $source_path . '/fixture_2.php';
		$actual = file_get_contents( $source_path . '/actual_2.xml' );
		$data[] = array( $ar, $actual );

		$ar = include $source_path . '/fixture_3.php';
		$actual = file_get_contents( $source_path . '/actual_3.xml' );
		$data[] = array( $ar, $actual );

		$ar = include $source_path . '/fixture_4.php';
		$actual = file_get_contents( $source_path . '/actual_4.xml' );
		$data[] = array( $ar, $actual );

		$ar = include $source_path . '/fixture_5.php';
		$actual = file_get_contents( $source_path . '/actual_5.xml' );
		$data[] = array( $ar, $actual );

		return $data;
	}

	/**
	 * @dataProvider providerTestIni2array
	 */
	public function testIni2array( $data, $actual, $exceptionName = '' )
	{
		if ( $exceptionName )
		{
			$this->setExpectedException( $exceptionName );
		}

		$expected = Converter::ini2array( $data );
		$this->assertEquals( $expected, $actual );
	}

	public function providerTestIni2array()
	{
		$data = array();

		$source_path = array(
			PHING_TEST_BASE,
			'etc',
			'util',
			'converter',
			'ini2array' );
		$source_path = implode( DIRECTORY_SEPARATOR, $source_path );

		$ar = include $source_path . '/fixture_1.php';
		$actual = require $source_path . '/actual_1.php';
		$data[] = array( $ar, $actual );

// 		$ar = include $source_path . '/fixture_2.php';
// 		$actual = require $source_path . '/actual_2.php';
// 		$data[] = array( $ar, $actual );

		return $data;
	}
}