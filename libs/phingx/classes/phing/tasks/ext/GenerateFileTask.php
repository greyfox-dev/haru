<?php
require_once 'phing/Task.php';

class GenerateFileTask extends Task
{
	protected $_srcFile;
	protected $_dstFile;
	protected $_mode = '0764';
	protected $_searchRegularString = '/#phing:(.*?)#/';

	/**
	 *
	 * Enter description here ...
	 * @var ChmodTask
	 */
	protected $_chmodTask;

	/**
	 * @param field_type $srcFile
	 */
	public function setSrc( $srcFile )
	{
		$this->_srcFile = $srcFile;
	}

	/**
	 * @param field_type $dstFile
	 */
	public function setDst( $dstFile )
	{
		$this->_dstFile = $dstFile;
	}

	/**
	 * @param field_type $mode
	 */
	public function setMode( $mode )
	{
		$this->_mode = $mode;
	}

	public function main()
	{
		$msg = 'Start generate file';
		$this->log( $msg );

		$this->_checkProperties();

		$srcFilename = $this->_getFullPath( $this->_srcFile );
		$content = file_get_contents( $srcFilename );

		preg_match_all( $this->_searchRegularString, $content, $matches );
		$cntMatches = count( $matches[ 0 ] );

		if ( $cntMatches > 0 )
		{
			$properties = $this->getProject()->getProperties();

			$search = $matches[ 1 ];
			$replace = $properties;
			$errors = array();
			$content = $this->_assocStrReplace( $search, $replace, $content, $errors );

			if ( !empty( $errors ) )
			{
				$message = sprintf( 'In file (%s) found unresolved properties:', $srcFilename );
				$message .= sprintf( "\n\t - %s", implode( "\n\t - ", $errors ) );
				throw new BuildException( $message );
			}
		}

		$dstFilename = $this->_getFullPath( $this->_dstFile );
		$check = file_exists( $dstFilename );
		if ( $check )
		{
			unlink( $dstFilename );
		}
		$res = file_put_contents( $dstFilename, $content );
		$this->_changeMode( $dstFilename );
		if ( false === $res )
		{
			$msg = sprintf( "\tSomething wrong (%s)", $dstFilename );
			throw new BuildException( $msg );
		}

		$msg = sprintf( "\tCreated file:" );
		if ( $check )
		{
			$msg = sprintf( "\tReplaced file:" );
		}
		$msg .= sprintf( "\n\t\t - from %s", $srcFilename );
		$msg .= sprintf( "\n\t\t - to %s", $dstFilename );
		$this->log( $msg );

		$msg = 'End generate file';
		$this->log( $msg );
	}

	protected function _changeMode( $dstFile )
	{
		if ( $this->_mode )
		{
			$this->log( $dstFile, Project::MSG_INFO );

			$command = sprintf('chmod %s %s', $this->_mode, $dstFile );
			$this->_exec( $command );
		}
	}

	protected function _exec( $command )
	{
		$task = new ExecTask();
		$task->setProject( $this->project );
		$task->setCommand( $command );
		$task->setCheckreturn( true );
		$task->setLogoutput( true );
		$task->setLevel( 'info' );

		$this->log( $command, Project::MSG_INFO );

		return $task->main();
	}

	protected function _assocStrReplace( $search, $replace, $subject, array &$errors )
	{
		foreach ( $search as $key )
		{
			$value = isset( $replace[ $key ] ) ? $replace[ $key ] : false;
			if ( $value !== false )
			{
				$searchStr = sprintf( '#phing:%s#', $key );
				$subject = str_replace( $searchStr, $value, $subject );
			}
			else
			{
				$errors[] = $key;
			}
		}

		return $subject;
	}

	protected function _checkProperties()
	{
		$srcFilename = $this->_srcFile;
		if ( empty( $srcFilename ) )
		{
			$msg = sprintf( 'Invalid param "src", must be not empty' );
			throw new BuildException( $msg );
		}
		$dstFilename = $this->_dstFile;
		if ( empty( $dstFilename ) )
		{
			$msg = sprintf( 'Invalid param "dst", must be not empty' );
			throw new BuildException( $msg );
		}

		$srcFilename = $this->_getFullPath( $srcFilename );
		$dstFilename = $this->_getFullPath( $dstFilename );
		if ( !file_exists( $srcFilename ) || !is_readable( $srcFilename ) )
		{
			$msg = sprintf( 'File "src" (%s) not found', $srcFilename );
			throw new BuildException( $msg );
		}
	}

	private function _getFullPath( $filename )
	{
		$file = new PhingFile( $filename );
		if ( !$file->isAbsolute() )
		{
			$file = new PhingFile( $this->project->getBasedir(), $filename );
		}
		$result = $file->getPath();
		return $result;
	}
}