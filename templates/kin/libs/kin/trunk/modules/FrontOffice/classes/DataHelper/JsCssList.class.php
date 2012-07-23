<?php
class ${born-properties.lib_name}_FrontOffice_DataHelper_JsCssList extends Miao_Office_DataHelper_JsCssList
{
	/**
	 *
	 * @return ${born-properties.lib_name}_FrontOffice_DataHelper_JsCssList
	 */
	static public function getInstance()
	{
		return parent::_getInstance( __CLASS__ );
	}

	public function js( $compress = false )
	{
		$fileList = $this->getResourceList( self::TYPE_JS );
		$fileList = $this->_client->getJs( $fileList, $compress );
		$result = $this->_makeLinks( $fileList );
		return $result;
	}

	public function css( $compress = false )
	{
		$fileList = $this->getResourceList( self::TYPE_CSS );
		$fileList = $this->_client->getCss( $fileList, $compress );
		$result = $this->_makeLinks( $fileList );
		return $result;
	}

	protected function __construct()
	{
		$minify = ( bool ) Miao_Config::Libs( __CLASS__ )->get( 'minify' );
		$dhUrl = ${born-properties.lib_name}_FrontOffice_DataHelper_Url::getInstance();
		$dstFolder = Miao_Path::getDefaultInstance()->getModuleRoot( '${born-properties.lib_name}_FrontOffice' ) . '/public/static';

		parent::__construct( $dhUrl, $dstFolder, $minify );
	}

	protected function _init()
	{
		$this->_addResource( 'jquery-1.7.2.js', self::TYPE_JS );
		$this->_addResource( 'bootstrap.js', self::TYPE_JS );

		$this->_addResource( 'skin/bootstrap.css', self::TYPE_CSS );
		$this->_addResource( 'skin/bootstrap-responsive.css', self::TYPE_CSS );
	}
}