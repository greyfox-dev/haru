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
		$result = $this->_runLibs();
		return $result;
	}

	protected function _runLibs()
	{
		$xml = $this->_xmlObj;

		$result = array();

		$libsList = self::getLibPathList( $xml->libs );

		foreach ( $libsList as $libName => $libDir )
		{
			$modules = $xml->libs->$libName->modules;
			if ( $modules )
			{
				foreach ( $modules->children() as $module )
				{
					$moduleName = $module->getName();

					$moduleDir1 = sprintf( '%s/%s/%s', $libDir, $this->_configDirModulesName, $moduleName );
					$moduleDir2 = sprintf( '%s/%s', $libDir, $moduleName );

					$moduleDir = '';
					if ( file_exists( $moduleDir1 ) )
					{
						$moduleDir = $moduleDir1;
					}
					else if ( file_exists( $moduleDir2 ) )
					{
						$moduleDir = $moduleDir2;
					}
					else
					{
						$message = sprintf( 'Module (%s) not found by path %s, %s', $moduleName, $moduleDir1, $moduleDir2 );
						throw new BuildException( $message );
					}

					$configDir = sprintf( '%s/%s', $moduleDir, $this->_configDirName );

					$item = $this->_generateSlice( $configDir, $module );

					$result[ $libName ][ $moduleName ] = $item;
				}
			}
		}
		return $result;
	}

	protected function _getLibs( $xml )
	{
		$libsList = array();
		foreach ( $xml->children() as $lib )
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
		foreach ( $xml->children() as $lib )
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