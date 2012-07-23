<?php
require_once "phing/tasks/ext/XmlPropertyTask.php";
require_once "phing/util/Converter.php";
require_once "phing/util/PropertyResolve.php";

class XmlPropertyResolveTask extends XmlPropertyTask
{
	const TYPE_XML = 'xml';
	const TYPE_PHP = 'php';

	private $_fileType = self::TYPE_XML;
	private $_dstFile;

	public function setType( $type )
	{
		$this->_fileType = $type;
	}

	/**
	 *
	 * @param field_type $_dstFilename
	 */
	public function setDstFile( $dstFile )
	{
		if ( is_string( $dstFile ) )
		{
			$dstFile = new PhingFile( $dstFile );
		}
		$this->_dstFile = $dstFile;
	}

	public function main()
	{
		if ( $this->file === null )
		{
			throw new BuildException( "You must specify file to load properties from", $this->getLocation() );
		}

		$this->_loadFile( $this->file );
	}

	/**
	 * load properties from an XML file.
	 *
	 * @param PhingFile $file
	 */
	protected function _loadFile( PhingFile $file )
	{
		$this->log( "Loading " . $file->getAbsolutePath(), Project::MSG_INFO );

		if ( $file->exists() )
		{
			$data = $this->_getData( $file );
			$obj = new PropertyResolve( $data );
			$properties = $obj->process();
			if ( isset( $properties[ 'config' ][ 'phing' ] ) )
			{
				unset( $properties[ 'config' ][ 'phing' ] );
				if ( empty( $properties[ 'config' ] ) )
				{
					unset( $properties[ 'config' ] );
				}
			}
			$this->_save( $properties );
		}
		else
		{
			$this->log( "Unable to find property file: " . $file->getAbsolutePath() . "... skipped", Project::MSG_WARN );
		}
	}

	protected function _getData( $file )
	{
		$configFilename = $file->getAbsolutePath();
		$data = Tools::loadConfig( $configFilename, 'xml' );

		$properties = $this->project->getProperties();
		foreach ( $properties as $key => &$value )
		{
			$value = $this->project->getProperty( $key );
		}
		$phingData = Converter::ini2array( $properties );
		Converter::normalizeForXml( $phingData );
		$data[ 'config' ][ 'phing' ] = $phingData;

		$result = $data;
		return $result;
	}

	protected function _save( array $properties )
	{
		$filename = $this->_dstFile->getAbsolutePath();
		switch ( $this->_fileType )
		{
			case self::TYPE_XML:
				Tools::saveToXml( $filename, $properties );
				break;
			case self::TYPE_PHP:
				Tools::saveToPhp( $filename, $properties );
				break;
			default:
				$msg = sprintf( "Invalid save file type (%s)", $this->_fileType );
				throw new BuildException( $msg );
		}

		$this->log( "Saved to " . $filename, Project::MSG_INFO );
	}

	protected function _getCountReplace( array $properties )
	{
		$result = 0;
		$unresolved = array();
		foreach ( $properties as $value )
		{
			preg_match_all( $this->_searchRegularString, $value, $matches );
			$cnt = count( $matches[ 0 ] );
			$result += $cnt;

			if ( $cnt > 0 )
			{
				$unresolved = array_merge( $unresolved, $matches[ 0 ] );
			}
		}

		if ( $result > 0 )
		{
			$msg = sprintf( "Attention found (%s) unresolved keys: \n\t - %s", count( $unresolved ), implode( "\n\t - ", $unresolved ) );
			throw new BuildException( $msg );
		}
		return $result;
	}
}