<?php
require_once 'phing/util/XmlConstruct.php';

class Converter
{
	static public function xml2array( $contents, $priority = 'tag' )
	{
		if ( !$contents )
			return array();

		if ( !function_exists( 'xml_parser_create' ) )
		{
			throw new Exception( "'xml_parser_create()' function not found!" );
		}

		/**
		 * Get the XML parser of PHP - PHP must have this module for the parser
		 * to work
		 *
		 * @var unknown_type
		 */
		$parser = xml_parser_create( '' );
		xml_parser_set_option( $parser, XML_OPTION_TARGET_ENCODING, "UTF-8" );
		xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
		xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 1 );
		$res = xml_parse_into_struct( $parser, trim( $contents ), $xml_values );
		if ( 0 === $res )
		{
			$error = xml_error_string( xml_get_error_code( $parser ) );
			xml_parser_free( $parser );
			throw new BuildException( sprintf( 'Invalid xml: %s', $error ) );
		}
		xml_parser_free( $parser );

		// Initializations
		$xmlArray = array();
		$parents = array();
		$opened_tags = array();
		$arr = array();

		$current = &$xmlArray;

		$repeated_tag_index = array();
		foreach ( $xml_values as $data )
		{
			$tag = $data[ 'tag' ];
			$type = $data[ 'type' ];
			$level = $data[ 'level' ];
			$value = ( isset( $data[ 'value' ] ) ) ? $data[ 'value' ] : '';
			$attributes = isset($data['attributes']) ? $data['attributes'] : array();

			$result = array();
			$attributes_data = array();

			if ( isset( $value ) )
			{
				if ( $priority == 'tag' )
				{
					$result = $value;
				}
				else
				{
					$result[ 'value' ] = $value;
				}
			}

			$index = $tag . '_' . $level;

			// See tag status and do the needed.
			if ( $type == "open" )
			{ // The starting of the tag '<tag>'
				$parent[ $level - 1 ] = &$current;
				if ( !is_array( $current ) or ( !in_array( $tag, array_keys( $current ) ) ) )
				{
					// Insert New tag
					$current[ $tag ] = $result;
					$repeated_tag_index[ $index ] = 1;
					$current = &$current[ $tag ];
				}
				else
				{ // There was another element with the same tag name

					if ( is_array( $current[ $tag ] ) && isset( $current[ $tag ][ 0 ] ) )
					{
						// If there is a 0th element it is already an array
						$current[ $tag ][ $repeated_tag_index[ $index ] ] = $result;
						$repeated_tag_index[ $index ]++;
					}
					else
					{
						/**
						 * This section will make the value an array if multiple
						 * tags with the same name appear together
						 */
						$current[ $tag ] = array( $current[ $tag ], $result );
						$repeated_tag_index[ $index ] = 2;
					}
					$last_item_index = $repeated_tag_index[ $index ] - 1;
					if ( is_array( $current[ $tag ] ) )
					{
						$current = &$current[ $tag ][ $last_item_index ];
					}
				}
			}
			elseif ( $type == "complete" )
			{ // Tags that ends in 1 line '<tag />'
			  // See if the key is already taken.
				if ( !isset( $current[ $tag ] ) )
				{ // New Key
					$current[ $tag ] = empty ($attributes) ? $result : $attributes;
					$repeated_tag_index[ $index ] = 1;
				}
				else
				{
					if ( isset( $current[ $tag ][ 0 ] ) and is_array( $current[ $tag ] ) )
					{
						$current[ $tag ][ $repeated_tag_index[ $index ] ] = empty ($attributes) ? $result : $attributes;;
						$repeated_tag_index[ $index ]++;
					}
					else
					{
						$current[ $tag ] = array( $current[ $tag ], empty ($attributes) ? $result : $attributes );
						$repeated_tag_index[ $index ] = 2;
					}
				}
			}
			elseif ( $type == 'close' )
			{ // End of tag '</tag>'
				$current = &$parent[ $level - 1 ];
			}
		}
		return ( $xmlArray );
	}

	static public function array2xml( array $data )
	{
		$obj = new XmlConstruct();
		$obj->fromArray( $data );
		$result = trim( $obj->getDocument() );
		return $result;
	}

	/**
	 * Special method for phing properties
	 *
	 * @param array $data
	 */
	static public function ini2array( $data, array $keys = array() )
	{
		$result = array();
		if ( is_array( $data ) )
		{
			foreach ( $data as $key => $value )
			{
				$keys = explode( '.', $key );
				$result = array_merge_recursive( $result, self::ini2array( $value, $keys ) );
			}
		}
		else if ( count( $keys ) )
		{
			$key = array_shift( $keys );
			if ( count( $keys ) )
			{
				$result[ $key ] = self::ini2array( $data, $keys );
			}
			else
			{
				$result[ $key ] = $data;
			}
		}
		return $result;
	}

	/**
	 * Need because xml doesn't support numeric tags
	 *
	 * @param array $data
	 */
	static public function normalizeForXml( &$data )
	{
		if ( is_array( $data ) )
		{
			ksort( $data );

			$keys = array_keys( $data );
			foreach ( $keys as $key => $value )
			{
				if ( !is_numeric( $value ) )
				{
					unset( $keys[ $key ] );
				}
			}

			$i = null;
			if ( $keys )
			{
				$i = max( $keys ) + 1;
			}
			foreach ( $data as $key => &$value )
			{
				if ( !is_null( $i ) )
				{
					if ( !is_numeric( $key ) )
					{
						$data[ $i++ ] = array( $key => $value );
						unset( $data[ $key ] );
					}
				}

				if ( is_array( $value ) )
				{
					self::normalizeForXml( $value );
				}
			}
		}
	}
}
