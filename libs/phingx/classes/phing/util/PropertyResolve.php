<?php
require_once 'phing/BuildException.php';
require_once 'phing/util/Converter.php';
require_once 'phing/util/Tools.php';
/**
 * При одиночном рекурсивном проходе остаются незамещенные переменные.
 * Поэтому рекурсивный проход выполяется по количеству замен.
 * Выход из рекурсии осуществляется после первой замены.
 */
class PropertyResolve
{
	/**
	 * Максимальная количество символов в значении переменной.
	 */
	const STRLEN_MAX_VALUE = 10000;
	const CICLE_MAX_VALUE = 100;

	/**
	 * Имена xpath для поиска и замены
	 *
	 * @var array
	 */
	private $_checkRecursiveList = array();

	/**
	 * По этой выражению ищем выражения для замены
	 *
	 * @var string
	 */
	private $_searchRegularString = '/\$\{([a-zA-Z0-9_\-\.]*[^}])\}/';

	private $_configFilename;
	private $_data;


	static public function factory( $configFilename, $type = 'php' )
	{
		$data = Tools::loadConfig( $configFilename, $type );
		$result = new self( $data );
		return $result;
	}

	public function __construct( array $data )
	{
		$this->_data = $data;
	}

	public function process()
	{
		// вычисляем динамически т. к. могут быть многоуровневые замены
		$i = 0;
		while ( 1 )
		{
			$i++;
			$countReplace = $this->_getCountReplace();
			if ( !$countReplace )
			{
				break;
			}
			if ( $i >= self::CICLE_MAX_VALUE )
			{
				$message = sprintf( 'A lot of replace iteration, more than %s ', self::CICLE_MAX_VALUE );
				throw new BuildException( $message );
			}

			$this->_process( $this->_data );
		}

		return $this->_data;
	}

	protected function _process( &$data )
	{
		if ( is_array( $data ) )
		{
			foreach ( $data as &$item )
			{
				$this->_process( $item );
			}
		}
		else
		{
			$this->_replace( $data );
			$this->_resetRecursiveArray();
		}
	}

	protected function _replace( &$value )
	{
		preg_match_all( $this->_searchRegularString, $value, $matches );
		if ( !isset( $matches[ 1 ] ) )
		{
			return false;
		}
		if ( $this->_checkRecursive( $matches[ 1 ] ) )
		{
			$message = sprintf( 'Обнаружена рекурсивная зависимость. path: %s', implode( '->', $this->_checkRecursiveList ) );
			throw new BuildException( $message );
		}

		$result = false;
		$i = 0;
		if ( array_key_exists( $i, $matches[ 1 ] ) )
		{
			$path = $matches[ 1 ][ $i ];
			$this->_addInCheckRecursiveArray( $path );
			$replaceValue = $this->_path( explode( '.', $path ), $this->_data );

			if ( null === $replaceValue )
			{
				$message = sprintf( 'Invalid path (%s)', $path );
				throw new BuildException( $message );
			}

			$value = str_replace( $matches[ 0 ][ $i ], $replaceValue, $value );
			$result = true;
		}

		unset( $matches );
		unset( $replaceValue );

		return $result;
	}

	protected function _getCountReplace()
	{
		$string = $this->_getXml();
		$matches = array();
		preg_match_all( $this->_searchRegularString, $string, $matches );
		$result = count( $matches[ 0 ] );
		return $result;
	}

	protected function _getXml()
	{
		$result = Converter::array2xml( $this->_data );
		return $result;
	}

	protected function _path( array $pathList, array $search )
	{
		$key = array_shift( $pathList );

		$result = null;
		if ( array_key_exists( $key, $search ) )
		{
			if ( empty( $pathList ) )
			{
				$result = $search[ $key ];
			}
			else if ( !empty( $search ) )
			{
				$result = $this->_path( $pathList, $search[ $key ] );
			}
		}
		return $result;
	}

	/**
	 * Проверяет есть ли рекурсивная зависимость
	 *
	 * @param unknown_type $xpath
	 * @return unknown
	 */
	protected function _checkRecursive( $matches )
	{
		if ( count( array_intersect( $matches, $this->_checkRecursiveList ) ) )
		{
			return true;
		}
		return false;
	}

	protected function _addInCheckRecursiveArray( $path )
	{
		$this->_checkRecursiveList[] = $path;
	}

	protected function _resetRecursiveArray()
	{
		$this->_checkRecursiveList = array();
	}
}