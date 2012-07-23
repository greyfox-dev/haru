<?php
/**
 * ViewBlock for generate scripts and styles includes
 *
 * @author vpak
 */
class ${born-properties.lib_name}_FrontOffice_ViewBlock_Static extends Miao_Office_ViewBlock
{
	protected $_list;
	protected $_type;

	/**
	 * (non-PHPdoc)
	 *
	 * @see Miao_Office_ViewBlock::_processData()
	 */
	protected function _processData()
	{
		$params = $this->getProcessParams();
		$this->_type = $params[ 0 ];

		$funcName = '_processData' . ucfirst( $this->_type );
		$this->$funcName();
	}

	/**
	 * Generate list for js
	 */
	protected function _processDataJs()
	{
		$helper = ${born-properties.lib_name}_FrontOffice_DataHelper_JsCssList::getInstance();
		$list = $helper->js();
		$this->_list = Miao_Office_ViewHelper_JsCssList::js( $list );
	}

	/**
	 * Generate list for css
	 */
	protected function _processDataCss()
	{
		$helper = ${born-properties.lib_name}_FrontOffice_DataHelper_JsCssList::getInstance();
		$list = $helper->css();
		$this->_list = Miao_Office_ViewHelper_JsCssList::css( $list );
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see Miao_Office_ViewBlock::_setTemplateVariables()
	 */
	protected function _setTemplateVariables()
	{
		$this->_setTmplVar( 'list', $this->_list );
	}
}