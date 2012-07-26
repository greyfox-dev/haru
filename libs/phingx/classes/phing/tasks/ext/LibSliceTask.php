<?php
require_once 'phing/Task.php';
require_once 'phing/BuildException.php';
require_once "phing/util/Configure/Slice.php";

class LibSliceTask extends Task
{
	protected $_filename;

	public function setFilename( $filename )
	{
		$this->_filename = $filename;
	}

	public function main()
	{
		$msg = 'Start slice ...';
		$this->log( $msg );

		$filename = $this->_filename;
		if ( empty( $filename ) )
		{
			throw new BuildException( "Missing attribute 'filename'" );
		}
		if ( !file_exists( $filename ) )
		{
			throw new BuildException( sprintf( "File (%s) not found",
				$filename ) );
		}

		$obj = new Configure_Slice( $filename );
		$result = $obj->run();

		foreach ( $result as $libName => $item )
		{
			$msg = sprintf( 'Generated %s files for lib %s:',
				count( $item ) , $libName );
			$this->log( $msg );
			$msg = "\t - " . implode( "\n\t\t - ", $item );
				$this->log( $msg );
		}

		$msg = 'End slice';
		$this->log( $msg );
	}
}