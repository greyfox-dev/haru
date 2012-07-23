<?php
require_once 'phing/BuildFileTest.php';

class VerboseExecTaskTest extends BuildFileTest
{
	private $_sourceDir;

	public function setUp()
	{
		$this->_sourceDir = PHING_TEST_BASE . "/etc/tasks/ext/verboseexec";
		$this->configureProject( $this->_sourceDir . "/build.xml" );

		$project = $this->getProject();
		$project->setNewProperty( 'phing.test.base', PHING_TEST_BASE );
		$project->setNewProperty( 'phing.test.source.dir', $this->_sourceDir );
		$this->executeTarget( "setup" );

		//because we use verbose exec task
		ob_start();
	}

	public function tearDown()
	{
		ob_end_clean();
	}

	/**
	 * @dataProvider providerTestMain
	 */
	public function testMain( $num = 0, $messages = array() )
	{
		$targetName = 'target_' . $num;
		$this->executeTarget( $targetName );

		foreach ( $messages as $m )
		{
			$this->assertInLogs( $m );
		}
	}

	public function providerTestMain()
	{
		$data = array();

		$data[] = array( 0, array( 'verbose exec run' ) );
		//$data[] = array( 1, array( 'Looping ... number 1', 'Looping ... number 2', 'Looping ... number 3', 'Looping ... number 4' ) );

		return $data;
	}

	/**
	 * @dataProvider providerTestReturnProperty
	 */
	public function testReturnProperty( $num, $actual )
	{
		$targetName = 'exit_' . $num;
		$this->executeTarget( $targetName );
		$this->assertPropertyEquals( 'test.return', $actual );
	}


	public function providerTestReturnProperty()
	{
		$data = array();

		$data[] = array( 0, 0 );
		$data[] = array( 2, 2 );

		return $data;
	}
}