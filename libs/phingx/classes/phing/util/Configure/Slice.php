<?php
require_once 'phing/BuildException.php';
require_once 'phing/util/Converter.php';
/**
 * Берем главную секцию, сохраняем
 * Находим библиотеки (libs), анализируя "главную" секцию
 * Пробегаем по модулям библиотеки, сохраняем конфиги, если модуля нет Exception
 *
 * @author vpak
 *
 */
class Configure_Slice
{

	private $_rootTagName = 'config';

	private $_configDirModulesName = 'modules';

	private $_configDirName = 'data';

	private $_fileXmlBasename = 'config.xml';

	private $_filePhpBasename = 'config.php';

	private $_xmlObj = null;

	public function __construct( $xmlPhpPropertiesFilename )
	{
		assert( is_string( $xmlPhpPropertiesFilename ) );

		$xml = simplexml_load_file( $xmlPhpPropertiesFilename );

		if ( !$xml || !$xml->libs )
		{
			throw new BuildException( 'Invalid config (no build or no build->libs sections: ' . $xmlPhpPropertiesFilename . ')' );
		}

		$this->_xmlObj = $xml;
	}

	public function run()
	{
		$result1 = $this->_runMain();
		$result2 = $this->_runLibs();
		$result = array_merge_recursive( $result1, $result2 );
		return $result;
	}

	protected function _runMain()
	{
		$xml = $this->_xmlObj;
		$config = clone $xml;
		$libDir = ( string ) $config->paths->root;
		unset( $config->libs );
		$result[ 'Main' ] = $this->_generateSlice( $libDir . '/data', $config );
		return $result;
	}

	protected function _runLibs()
	{
		$xml = $this->_xmlObj;

		$result = array();
		$libsList = self::getLibPathList( $xml->libs );
		foreach ( $libsList as $libName => $libDir )
		{
			$config = clone $xml->libs->$libName;
			unset( $config->configure );
			unset( $config->link );
			if ( $config->modules )
			{
				$modules = clone $config->modules;
				unset( $config->modules );
				foreach ( ( array ) $modules->children() as $node )
				{
					$this->_sxmlAppend( $config, $node );
				}
			}
			$result[ $libName ] = $this->_generateSlice( $libDir . '/data', $config );
		}
		return $result;
	}

	protected function _sxmlAppend( SimpleXMLElement $to, SimpleXMLElement $from )
	{
		$toDom = dom_import_simplexml( $to );
		$fromDom = dom_import_simplexml( $from );
		$toDom->appendChild( $toDom->ownerDocument->importNode( $fromDom, true ) );
	}

	protected function _generateSlice( $dstDir, $xml )
	{
		$phpFileName = sprintf( '%s/%s', $dstDir, $this->_filePhpBasename );
		$xmlFileName = sprintf( '%s/%s', $dstDir, $this->_fileXmlBasename );

		if ( !file_exists( $dstDir ) )
		{
			mkdir( $dstDir, 0777, true );
		}

		$xmlString = $xml->asXml();
		$resPhpSave = $this->_saveToPhp( $phpFileName, $xmlString );

		$xmlString = '<?xml version="1.0" encoding="UTF-8"?>' . chr( 10 ) . $xmlString;
		$resXmlSave = file_put_contents( $xmlFileName, $xmlString );

		return array( $phpFileName, $xmlFileName );
	}

	protected function _saveToPhp( $filename, $xmlString )
	{
		$data = Converter::xml2array( $xmlString );
		$content = sprintf( '<?php return %s;', var_export( $data, true ) );
		$result = file_put_contents( $filename, $content );
		return $result;
	}

	public static function getLibPathList( $xml )
	{
		$libsList = array();
		foreach ( ( array ) $xml->children() as $lib )
		{
			$libName = $lib->getName();
			$libDstDir = strval( $lib->deploy->dst );

			if ( $libName && $libDstDir )
			{
				$libsList[ $libName ] = $libDstDir;
			}
		}

		return $libsList;
	}
}