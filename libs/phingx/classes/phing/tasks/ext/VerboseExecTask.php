<?php
require_once 'phing/Task.php';
require_once 'phing/BuildException.php';
/**
 * Решение проблемы с выполнение exec.
 * С помощью данного task мы тут же видим что выдает нам STD_OUT
 */
class VerboseExecTask extends Task
{
	/**
	 * Command to execute.
	 *
	 * @var string
	 */
	protected $_command;

	/**
	 * The command to use.
	 *
	 * @param mixed $command
	 *        	String or string-compatible (e.g. w/ __toString()).
	 *
	 * @return void
	 */
	public function setCommand( $command )
	{
		$this->_command = "" . $command;
	}

	/**
	 * Property name to set with return value from exec call.
	 *
	 * @var string
	 */
	protected $_returnProperty;

	/**
	 * The name of property to set to return value from exec() call.
	 *
	 * @param string $prop
	 *        	Property name
	 *
	 * @return void
	 */
	public function setReturnProperty( $prop )
	{
		$this->_returnProperty = $prop;
	}

	/**
	 * Whether to check the return code.
	 *
	 * @var boolean
	 */
	protected $_checkreturn = true;

	/**
	 * Whether to check the return code.
	 *
	 * @param boolean $checkreturn
	 *        	If the return code shall be checked
	 *
	 * @return void
	 */
	public function setCheckreturn( $checkreturn )
	{
		$this->_checkreturn = ( bool ) $checkreturn;
	}

	public function main()
	{
		$cmd = trim( $this->_command );

		if ( empty( $cmd ) )
		{
			$msg = 'Empty command';
			throw new BuildException( $msg );
		}

		$cmd = escapeshellcmd( $cmd );
		$res = $this->_doWork( $cmd );

		if ( $this->_returnProperty )
		{
			$this->project->setProperty( $this->_returnProperty, $res );
		}

		if ( $this->_checkreturn && -1 == $res || 255 == $res )
		{
			$msg = 'Command execute return error code';
			throw new BuildException( $msg );
		}
	}

	protected function _doWork( $cmd )
	{
		$return = -1;

		$msg = sprintf( 'Exec: %s', $cmd );
		$this->log( $msg );
		passthru( $cmd, $return );
		return $return;
	}
}