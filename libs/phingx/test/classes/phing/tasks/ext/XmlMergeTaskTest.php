<?php
require_once 'phing/BuildFileTest.php';

class XmlMergeTaskTest extends BuildFileTest
{
	private $_sourceDir;

	public function setUp()
	{
		$this->_sourceDir = PHING_TEST_BASE . "/etc/tasks/ext/xmlmerge";
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
	 * @dataProvider providerTestMain
	 *
	 * @param unknown_type $num
	 */
	public function testMain( $num, $type = 'php', $exceptionName = '' )
	{
		if ( $exceptionName )
		{
			$this->setExpectedException( $exceptionName );
		}

		$targetName = 'merge' . $num;

		$this->executeTarget( $targetName );
		$this->assertInLogs( "Start xmlmerge files" );
		$this->assertInLogs( "Complete xmlmerge" );

		$actualFilename = $this->_sourceDir . '/' . $num . '_actual.' . $type;
		$expectedFilename = $this->_sourceDir . '/' . $num . '_expected.' . $type;

		$this->assertFileExists( $expectedFilename );
		$this->assertFileExists( $actualFilename );

		if ( $type == 'php' )
		{
			$expected = include $expectedFilename;
			$actual = include $actualFilename;

			$this->assertEquals( $expected, $actual );
		}
		else if ( $type == 'xml' )
		{
			$this->assertXmlFileEqualsXmlFile( $expectedFilename, $actualFilename );
		}
		unlink( $expectedFilename );
	}

	public function providerTestMain()
	{
		$data = array();

		$data[] = array( 0 );
		$data[] = array( 1 );
		$data[] = array( 2 );
		$data[] = array( 3 );
		$data[] = array( 4 );
		$data[] = array( 5, 'php', 'BuildException' );
		$data[] = array( 6, 'xml' );
		$data[] = array( 7 );
		$data[] = array( 8 );
		$data[] = array( 9 );
		$data[] = array( 10, 'xml' );

		return $data;
	}
}