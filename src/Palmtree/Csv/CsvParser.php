<?php

namespace Palmtree\Csv;

/**
 * Class CsvParser
 * @package Palmtree\Csv
 */
class CsvParser implements \Iterator {
	/**
	 * @var array
	 */
	public static $defaultArgs = array(
		'file'         => '',
		'hasHeaders'   => true,
		'delimiter'    => ',',
		'enclosure'    => '"',
		'escape'       => '\\',
		'normalize'    => false,
		'falseyValues' => array( 'false', 'off', 'no', '0', 'disabled' ),
		'truthyValues' => array( 'true', 'on', 'yes', '1', 'enabled' ),
	);

	/**
	 * @var resource
	 */
	protected $fileHandle;
	/**
	 * @var array
	 */
	protected $headers = array();
	/**
	 * @var int
	 */
	protected $index = 0;
	/**
	 * @var
	 */
	protected $line;

	/**
	 * @var array
	 */
	protected $args = array();

	/**
	 * @var array
	 */
	protected $newLines = array( "\r\n", "\r", "\n" );

	/**
	 * CSV constructor.
	 */
	public function __construct( $args = array() ) {
		if ( is_string( $args ) ) {
			$args = array( 'file' => $args );
		}

		$this->args = array_replace_recursive( self::$defaultArgs, $args );

		ini_set( 'auto_detect_line_endings', '1' );

		$this->fileHandle = fopen( $this->args['file'], 'r' );

		if ( ! $this->fileHandle ) {
			throw new \Exception( "Could not open file '{$this->args['file']}' for reading." );
		}
	}

	/**
	 *
	 */
	public function __destruct() {
		if ( $this->fileHandle ) {
			fclose( $this->fileHandle );
		}
	}

	/**
	 * @return array
	 */
	protected function getNextLine() {
		return fgetcsv( $this->fileHandle, null, $this->args['delimiter'] );
	}

	/**
	 * @return array
	 */
	public function current() {
		$line       = $this->line;
		$this->line = array();

		foreach ( $line as $key => $cell ) {
			if ( $this->args['hasHeaders'] && isset( $this->headers[ $key ] ) ) {
				$key = $this->headers[ $key ];
			}

			$this->line[ $key ] = $this->formatCell( $cell );
		}

		return $this->line;
	}

	/**
	 *
	 */
	public function next() {
		$this->index++;
	}

	/**
	 * @return int
	 */
	public function key() {
		return $this->index;
	}

	/**
	 * @return bool
	 */
	public function valid() {
		$this->line = $this->getNextLine();

		return $this->line !== false && $this->line !== null;
	}

	/**
	 *
	 */
	public function rewind() {
		rewind( $this->fileHandle );

		if ( $this->args['hasHeaders'] ) {
			$this->headers = $this->getNextLine();
		}
	}

	/**
	 * @param      $cell
	 * @param bool $toLower
	 *
	 * @return mixed
	 */
	protected function formatCell( $cell, $toLower = false ) {
		$cell = trim( $cell );
		$cell = str_replace( $this->newLines, "\n", mb_convert_encoding( $cell, 'UTF-8', mb_detect_encoding( $cell ) ) );

		if ( $toLower ) {
			$cell = mb_strtolower( $cell );
		}

		if ( $this->args['normalize'] ) {
			$cell = $this->normalizeValue( $cell );
		}

		return $cell;
	}

	/**
	 * @param $value
	 *
	 * @return mixed
	 */
	protected function normalizeValue( $value ) {
		// Number
		if ( is_numeric( $value ) ) {
			return $value + 0;
		}

		// Boolean
		$valueLowered = mb_strtolower( $value );
		if ( in_array( $valueLowered, $this->args['truthyValues'] ) ) {
			return true;
		}

		if ( in_array( $valueLowered, $this->args['falseyValues'] ) ) {
			return false;
		}

		return $value;
	}
}
