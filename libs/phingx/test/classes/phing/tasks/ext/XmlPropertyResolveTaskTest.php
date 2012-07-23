<?php
require_once 'phing/BuildFileTest.php';

class XmlPropertyResolveTaskTest extends BuildFileTest
{
	private $_sourceDir;

	public function setUp()
	{
		$this->_sourceDir = PHING_TEST_BASE . "/etc/tasks/ext/xmlpropertyresolve";
		$this->configureProject( $this->_sourceDir . "/build.xml" );

		$project = $this->getProject();
		$project->setNewProperty( 'phing.test.base', PHING_TEST_BASE );
		$project->setNewProperty( 'phing.test.source.dir', $this->_sourceDir );
		$this->executeTarget( "setup" );
	}

	/**
	 *
	 *
	 *
	 *
	 * @dataProvider providerTestMain
	 *
	 * @param unknown_type $num
	 */
	public function testMain( $num, $exceptionName = '' )
	{
		if ( $exceptionName )
		{
			$this->setExpectedException( $exceptionName );
		}

		$targetName = 'target_' . $num;

		$this->executeTarget( $targetName );

		$actualFileXml = $this->_sourceDir . '/' . $num . '_actual.xml';
		$expectedFileXml = $this->_sourceDir . '/' . $num . '_expected.xml';
		$this->assertXmlFileEqualsXmlFile( $expectedFileXml, $actualFileXml );

		$actual = include $this->_sourceDir . '/' . $num . '_actual.php';
		$expected = include $this->_sourceDir . '/' . $num . '_expected.php';

		$this->assertEquals( $expected, $actual );

		unlink( $expectedFileXml );
	}

	public function providerTestMain()
	{
		$data = array();

		$data[] = array( 0 );
		$data[] = array( 1 );
		$data[] = array( 2 );
		$data[] = array( 3, 'BuildException' );
		$data[] = array( 4 );

		return $data;
	}
}