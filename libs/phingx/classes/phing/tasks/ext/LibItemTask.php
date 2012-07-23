<?php
require_once 'phing/Task.php';
require_once 'phing/BuildException.php';
/**
 *
 * Выделяем из конфига части для работы с элементами libs
 * @author vpak
 *
 */
class LibItemTask extends Task
{
	private $_srcFile;
	private $_dstDir;
	/**
	 * @param field_type $srcFile
	 */
	public function setSrcFile( $srcFile )
	{
		$this->_srcFile = $srcFile;
	}

	/**
	 * @param field_type $dstDir
	 */
	public function setDstDir( $dstDir )
	{
		$this->_dstDir = $dstDir;
	}

	public function main()
	{
		$msg = 'Start make libs item conf.xml';
		$this->log( $msg );

		$this->_checkParams();

		$msg = sprintf( 'Analize srcFile: %s', $this->_srcFile );
		$this->log( $msg );
		$msg = sprintf( 'Dst dir is: %s', $this->_dstDir );
		$this->log( $msg );

		$this->_sliceConfig();
	}

	protected function _sliceConfig()
	{
		$xml = simplexml_load_file( $this->_srcFile );
		if ( !$xml || !$xml->libs )
		{
			throw new BuildException( sprintf( 'Bad xml (%s)', $this->_srcFile ) );
		}

		foreach ( $xml->libs->children() as $child )
		{
			$header = '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
			$fileName = sprintf( '%s/%s.xml', $this->_dstDir, $child->getName() );
			file_put_contents($fileName, $header . $child->asXML() );
			$this->log( 'File created ' . $fileName );
		}
	}

	protected function _checkParams()
	{
		if ( empty( $this->_srcFile ) )
		{
			throw new BuildException( "Missing attribute 'srcFile'" );
		}
		else if ( !file_exists( $this->_srcFile ) || !is_readable(
			$this->_srcFile ) )
		{
			throw new BuildException( sprintf(
				"Src file (%s) does not exists or is not readable",
				$this->_srcFile ) );
		}

		if ( !file_exists( $this->_dstDir ) || !is_dir( $this->_dstDir ) || !is_writable(
			$this->_dstDir ) )
		{
			throw new BuildException( sprintf( 'Bad dstDir param (%s %s)',
				$this->_dstDir, `pwd` ) );
		}
	}
}