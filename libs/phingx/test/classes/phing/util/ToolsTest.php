<?php
require_once 'phing/util/Tools.php';

class ToolsTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider providerTestMerge
	 */
	public function testMerge( $paArray1, $paArray2, $actual )
	{
		$expected = Tools::arrayMergeRecursive( $paArray1, $paArray2 );
		$this->assertEquals( $expected, $actual );
	}

	public function providerTestMerge()
	{
		$data = array();

		$paArray1 = array(
			'config' => array( 'item' => array( '1', '2', '3', '4' ) ) );
		$paArray2 = array( 'config' => '' );
		$actual = array(
			'config' => array( 'item' => array( '1', '2', '3', '4' ) ) );
		$data[] = array( $paArray1, $paArray2, $actual );

		$paArray1 = array( 'config' => '' );
		$paArray2 = array(
			'config' => array( 'item' => array( '1', '2', '3', '4' ) ) );
		$actual = array(
			'config' => array( 'item' => array( '1', '2', '3', '4' ) ) );
		$data[] = array( $paArray1, $paArray2, $actual );

		$paArray1 = array( 'a', 'b' );
		$paArray2 = array( 1, 2 );
		$actual = array( 1, 2 );
		$data[] = array( $paArray1, $paArray2, $actual );

		$paArray1 = array( 'a', 'b' );
		$paArray2 = array( 1 );
		$actual = array( 1, 'b' );
		$data[] = array( $paArray1, $paArray2, $actual );

		$paArray1 = array( 'a', 'b' );
		$paArray2 = array();
		$actual = array( 'a', 'b' );
		$data[] = array( $paArray1, $paArray2, $actual );

		$paArray1 = array();
		$paArray2 = array( 'a', 'b' );
		$actual = array( 'a', 'b' );
		$data[] = array( $paArray1, $paArray2, $actual );

		$paArray1 = array( 'item_1' => array( 1, 2, 3 ) );
		$paArray2 = array( 'item_1' => array( 'a', 'b' ), 'item_2' => '123' );
		$actual = array( 'item_1' => array( 'a', 'b', 3 ), 'item_2' => '123' );
		$data[] = array( $paArray1, $paArray2, $actual );

		$paArray1 = array(
			'item_1' => array( 1, 2, 3 ),
			'item_2' => array( 1, 2 ) );
		$paArray2 = array( 'item_1' => array( 'a', 'b' ), 'item_2' => '' );
		$actual = array(
			'item_1' => array( 'a', 'b', 3 ),
			'item_2' => array( 1, 2 ) );
		$data[] = array( $paArray1, $paArray2, $actual );

		$paArray1 = array(
			'configure' => array(
				'files' => array( 'item' => 'something' ) ) );
		$paArray2 = array(
			'configure' => array(
				'files' => array( 'item' => array( 0 => 'user1', 1 => 'user2' ) ) ) );
		$actual = array(
			'configure' => array(
				'files' => array( 'item' => array( 0 => 'user1', 1 => 'user2' ) ) ) );
		$data[] = array( $paArray1, $paArray2, $actual );

		return $data;
	}
}