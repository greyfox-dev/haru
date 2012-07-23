<?php
class XmlConstruct extends XMLWriter
{
	/**
	 * Constructor.
	 *
	 * @param string $prm_rootElementName
	 *        	A root element's name of a current xml document
	 * @param string $prm_xsltFilePathPath
	 *        	of a XSLT file.
	 * @access public
	 * @param
	 *        	null
	 */
	public function __construct()
	{
		$this->openMemory();
		$this->setIndent( true );
		$this->setIndentString( "\t" );
		$this->startDocument( '1.0', 'UTF-8' );
	}

	/**
	 * Construct elements and texts from an array.
	 *
	 * The array should contain an attribute's name in index part
	 * and a attribute's text in value part.
	 *
	 * @access public
	 * @param array $prm_array Contains attributes and texts
	 * @return null
	 */
	public function fromArray( $prmArray )
	{
		if ( is_array( $prmArray ) )
		{
			foreach ( $prmArray as $index => $element )
			{
				if ( is_array( $element ) )
				{
					if ( is_numeric( key( $element ) ) )
					{
						foreach ( $element as $text )
						{
							if ( is_array( $text ) )
							{
								$this->startElement( $index );
								$this->fromArray( $text );
								$this->endElement();
							}
							else
							{
								$this->setElement( $index, $text );
							}
						}
					}
					else
					{
						$this->startElement( $index );
						$this->fromArray( $element );
						$this->endElement();
					}
				}
				else
				{
					$this->setElement( $index, $element );
				}
			}
		}
	}

	/**
	 * Set an element with a text to a current xml document.
	 *
	 * @access public
	 * @param string $prm_elementName An element's name
	 * @param string $prm_ElementText An element's text
	 * @return null
	 */
	public function setElement( $prmElementName, $prmElementText )
	{
		$this->startElement( $prmElementName );
		$this->text( $prmElementText );
		$this->endElement();
	}

	/**
	 * Return the content of a current xml document.
	 *
	 * @access public
	 * @param
	 *        	null
	 * @return string Xml document
	 */
	public function getDocument()
	{
		$this->endElement();
		$this->endDocument();
		return $this->outputMemory();
	}
}
