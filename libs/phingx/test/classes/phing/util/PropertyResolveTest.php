<?php
require_once 'phing/util/PropertyResolve.php';

class PropertyResolveTest extends PHPUnit_Framework_TestCase
{
	protected $_testDir;

	public function setUp()
	{
		$this->_testDir = PHING_TEST_BASE . "/etc/util/propertyresolve";
	}

	/**
	 * @dataProvider providerTestProcess
	 */
	public function testProcess( $num, $exceptionName = '' )
	{
		if ( !empty( $exceptionName ) )
		{
			$this->setExpectedException( $exceptionName );
		}

		$configFilename = $this->_testDir . '/fixture_' . $num . '.php';
		$actualFilename = $this->_testDir . '/actual_' . $num . '.php';

		$obj = PropertyResolve::factory( $configFilename );
		$expected = $obj->process();
		$actual = include $actualFilename;

		$this->assertEquals( $expected, $actual );
	}

	public function testFactory()
	{
		$num = 5;

		$configFilename = $this->_testDir . '/fixture_' . $num . '.xml';
		$actualFilename = $this->_testDir . '/actual_' . $num . '.php';
		$obj = PropertyResolve::factory( $configFilename, 'xml' );
		$expected = $obj->process();
		$actual = include $actualFilename;
		$this->assertEquals( $expected, $actual );
	}

	public function providerTestProcess()
	{
		$data = array();

		$data[] = array( 1 );
		$data[] = array( 2 );
		$data[] = array( 3, 'BuildException' );
		$data[] = array( 4 );
		$data[] = array( 6, 'BuildException' );

		return $data;
	}
}