<?php
namespace Palmtree;

/**
 * Class Csv
 * @package Palmtree
 */
class Csv {
	/**
	 * @var string
	 */
	public $newLine = "\r\n";

	/**
	 * @var string
	 */
	public $enclosure = '"';
	/**
	 * @var string
	 */
	public $delimiter = ',';

	/**
	 * @var array
	 */
	private $newLines = array( "\r\n", "\r", "\n" );

	/**
	 * @var string
	 */
	private $output = '';
	/**
	 * @var string
	 */
	private $headers = '';

	/**
	 * @var string
	 */
	public $filename;

	/**
	 * @var
	 */
	public $normalize = false;

	/**
	 * CSV constructor.
	 */
	public function __construct() {
		ini_set( 'auto_detect_line_endings', '1' );
	}

	/**
	 * @param array $rows
	 */
	public function addRows( $rows ) {
		foreach ( $rows as $key => $row ) {
			$this->addRow( $row );
		}
	}

	/**
	 * @param string $row
	 */
	public function addRow( $row ) {
		$this->output .= $this->enclosure .
		                 implode( $this->enclosure . $this->delimiter . $this->enclosure, $this->escape( $row ) ) .
		                 $this->enclosure .
		                 $this->newLine;
	}

	/**
	 * @param array $headers
	 */
	public function addHeaders( $headers ) {
		foreach ( $headers as $header ) {
			$this->addHeader( $header );
		}
	}

	/**
	 * @param string $header
	 */
	public function addHeader( $header ) {
		$this->headers .= $this->enclosure .
		                  $this->escape( $header ) .
		                  $this->enclosure .
		                  $this->delimiter;
	}

	/**
	 * @return void
	 */
	public function download() {
		$filename = ( ( empty( $this->filename ) ) ? time() : $this->filename ) . '.csv';

		$output = '';

		if ( ! empty( $this->headers ) ) {
			$output .= rtrim( $this->headers, $this->delimiter ) . $this->newLine;
		}

		$output .= rtrim( $this->output );

		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Description: File Transfer' );

		header( 'Content-Transfer-Encoding: Binary' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . mb_strlen( $output ) );

		print $output;
		exit;
	}

	/**
	 * Parses a CSV file into an array.
	 *
	 * Converts all values to UTF-8 and trims them.
	 *
	 * @param string $file       Path to the file to parse.
	 * @param bool   $hasHeaders Whether the file has headers which should be used as array indexing.
	 *
	 * @return array|bool
	 */
	public function parseFile( $file, $hasHeaders = true ) {
		if ( ! ( $handle = fopen( $file, 'r' ) ) ) {
			return array();
		}

		$data    = array();
		$headers = array();

		$rowIndex = 1;
		$index    = 0;
		while ( ( $row = fgetcsv( $handle, null, ',' ) ) !== false ) {
			// Set up headers if it's the first row.
			if ( $hasHeaders && $rowIndex === 1 ) {
				foreach ( $row as $cell ) {
					$cell = $this->formatCell( $cell );

					if ( ! empty( $cell ) ) {
						$headers[] = $cell;
					}
				}

				continue;
			}

			$data[ $index ] = array();

			foreach ( $row as $key => $cell ) {
				$cell = $this->formatCell( $cell );

				if ( $hasHeaders && isset( $headers[ $key ] ) ) {
					$line_key = $headers[ $key ];

					$data[ $index ][ $line_key ] = $cell;
				} else {
					$data[ $index ][] = $cell;
				}
			}

			$index++;

			$rowIndex++;
		}

		fclose( $handle );

		return $data;
	}

	/**
	 * @param      $cell
	 * @param bool $toLower
	 *
	 * @return bool|int|mixed|string
	 */
	protected function formatCell( $cell, $toLower = false ) {
		$cell = trim( $cell );
		$cell = str_replace( $this->newLines, "\n", mb_convert_encoding( $cell, 'UTF-8', mb_detect_encoding( $cell ) ) );

		if ( $toLower ) {
			$cell = mb_strtolower( $cell );
		}

		if ( $this->normalize ) {
			$cell = $this->normalizeValue( $cell );
		}

		return $cell;
	}

	/**
	 * @param $value
	 *
	 * @return bool|int|string
	 */
	protected function normalizeValue( $value ) {
		// Number
		if ( is_numeric( $value ) ) {
			return $value + 0;
		}

		// Boolean
		$valueLowered = mb_strtolower( $value );
		if ( $valueLowered === 'true' || $valueLowered === 'on' || $valueLowered === 'yes' ) {
			return true;
		}

		if ( $valueLowered === 'false' || $valueLowered === 'off' || $valueLowered === 'no' ) {
			return false;
		}

		return $value;
	}

	/**
	 * @param string $data
	 *
	 * @return string
	 */
	protected function escape( $data ) {
		$find    = array( '"' );
		$replace = array( '""' );

		return str_replace( $find, $replace, $data );
	}
}

