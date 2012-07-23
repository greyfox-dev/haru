<?php
require_once 'phing/Task.php';
require_once 'phing/BuildException.php';
require_once 'phing/tasks/ext/SymlinkTask.php';

/**
 *
 * Super installer
 *
 *
 */
class LibLinkTask extends Task
{
	private $_configXml;
	private $_symlinkTask;

	private $_defaultDirsSymlink = array(
		'etc' => 'etc',
		'scripts' => 'scripts' );

	/**
	 * @param field_type $configXml
	 */
	public function setConfig( $configXmlFilename )
	{
		$this->_configXml = $this->_initConfig( $configXmlFilename );
	}

	public function init()
	{
		$this->_symlinkTask = new SymlinkTask();
		$this->_symlinkTask->setProject( $this->project );
		return true;
	}

	public function main()
	{
		$msg = 'Start link';
		$this->log( $msg );

		$xml = $this->_configXml;

		$libs = $xml->libs;
		foreach ( $libs->children() as $lib )
		{
			$libName = $lib->getName();

			if ( $this->_needStandartLinks( $lib ) )
			{
				$this->_linkDefaultDirs( $lib );
				$this->_linkCurrent( $lib );
			}

			$this->_linkCustom( $lib );
		}

		$msg = 'End link';
		$this->log( $msg );
	}

	protected function _initConfig( $configXmlFilename )
	{
		$xml = simplexml_load_file( $configXmlFilename );
		if ( !$xml || !$xml->libs )
		{
			$msg = sprintf( 'Invalid config structure %s',
				$configXmlFilename );

			throw new BuildException( $msg );
		}

		return $xml;
	}

	protected function _linkCustom( SimpleXMLElement $lib )
	{
		$libName = $lib->getName();
		if ( $lib->link && $lib->link )
		{
			$msg = sprintf( "\tBegin custom link instructions on %s", $libName );
			$this->log( $msg );

			foreach ( $lib->link->children() as $item )
			{
				$target = ( string ) $item->src;
				$link = ( string ) $item->dst;
				$this->_link( $target, $link );
			}
			$msg = sprintf( "\tEnd custom link instructions on %s", $libName );
			$this->log( $msg );
		}
		else
		{
			$msg = sprintf( "\tNo custom links instructions on %s", $libName );
			$this->log( $msg );
		}
	}

	protected function _linkDefaultDirs( SimpleXMLElement $lib )
	{
		$libName = $lib->getName();

		$libDeployType = strval( $lib->deploy->type );
		if ( empty( $libDeployType ) || $libDeployType == 'none' )
		{
			$this->log( 'No need default link instructions for ' . $libName );
			return false;
		}

		$msg = sprintf( "\tBegin default link instructions on %s", $libName );
		$this->log( $msg );

		foreach ( $this->_defaultDirsSymlink as $key => $dir )
		{
			$link = $this->_configXml->paths->$key . '/' . strtolower(
				$libName );
			$target = $lib->deploy->dst . '/' . $dir;

			$linkDir = dirname( $link );
			if ( !file_exists( $linkDir ) )
			{
				mkdir( $linkDir );
			}

			$this->_link( $target, $link, false );
		}

		$msg = sprintf( "\tEnd default link instructions on %s", $libName );
		$this->log( $msg );
	}

	protected function _needStandartLinks( SimpleXMLElement $lib )
	{
		$res = true;
		$libName = $lib->getName();

		$libDeployType = strval( $lib->deploy->type );
		if ( empty( $libDeployType ) || $libDeployType == 'none' )
		{
			$res = false;
		}

		return $res;
	}

	protected function _linkCurrent( SimpleXMLElement $lib )
	{
		$libName = $lib->getName();

		$msg = sprintf( "\tBegin current link instructions on %s", $libName );
		$this->log( $msg );

		if ( strval( $lib->deploy->current ) )
		{
			$link = strval( $lib->deploy->current );
		}
		else
		{
			$msg = sprintf( 'Current symlink path not defined for %s',
				$libName );
			//$this->_logError( $msg, false );
			return false;
		}

		$target = strval( $lib->deploy->dst );

		$this->_link( $target, $link );

		$msg = sprintf( "\tEnd current link instructions on %s", $libName );
		$this->log( $msg );
	}

	protected function _link( $target, $link, $exceptionOnError = true )
	{
		$msg = sprintf( 'Try link %s => %s', $link, $target );
		$this->log( $msg );

		if ( !file_exists( $target ) )
		{
			$msg = sprintf(
				'Target %s doesn`t exist.', $target );
			$this->_logError( $msg, $exceptionOnError );
			return false;
		}

		$linkExists = file_exists( $link );
		if ( $linkExists )
		{
			if ( readlink( $link ) == $target )
			{
				$msg = sprintf( '%s already points to %s. Nothing to do.',
					$link, $target );
				$this->log( $msg );
				return true;
			}
			if ( is_link( $link ) )
			{
				unlink( $link );
			}
			else
			{
				$msg = sprintf( '%s already exists and is not symlink.', $link );
				$this->_logError( $msg, $exceptionOnError );
				return false;
			}
		}

		$this->_symlinkTask->setTarget( $target );
		$this->_symlinkTask->setLink( $link );
		$res = $this->_symlinkTask->main();

		return $res;
	}

	protected function _logError( $msg, $exceptionOnError )
	{
		if ( $exceptionOnError )
		{
			throw new BuildException( $msg );
		}
		else
		{
			$this->log( $msg, PROJECT::MSG_WARN );
		}
	}
}