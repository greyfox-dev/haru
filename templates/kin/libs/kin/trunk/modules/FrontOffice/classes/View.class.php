<?php
class ${born-properties.lib_name}_FrontOffice_View extends Miao_Office_View
{
	protected function _initializeBlock()
	{
		$this->_addBlock( 'Scripts', array(
			'${born-properties.lib_name}_FrontOffice_ViewBlock_Static',
			'js' ), array( 'js.tpl' ) );

		$this->_addBlock( 'Styles', array(
			'${born-properties.lib_name}_FrontOffice_ViewBlock_Static',
			array( 'css' ) ), array( 'js.tpl' ) );
	}
}