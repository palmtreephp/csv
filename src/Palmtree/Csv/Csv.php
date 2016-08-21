<?php
namespace Palmtree\Csv;

/**
 * Class Csv
 * @package Palmtree
 */
class Csv {
	public static $defaultArgs = array(
		'filename'  => '',
		'delimiter' => ',',
		'enclosure' => '"',
		'newLine'   => "\r\n",
	);

	protected $args = array();

	/**
	 * @var string
	 */
	protected $rows = '';
	/**
	 * @var string
	 */
	protected $headers = '';

	protected $output;

	protected $filename;

	public function __construct( $args = array() ) {
		if ( is_string( $args ) ) {
			$args = array( 'filename' => $args );
		}

		$this->args = array_replace_recursive( self::$defaultArgs, $args );

		$this->setFilename( $this->args['filename'] );
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
		$this->rows .= $this->args['enclosure'] .
		               implode( $this->args['enclosure'] . $this->args['delimiter'] . $this->args['enclosure'], $this->escape( $row ) ) .
		               $this->args['enclosure'] .
		               $this->args['newLine'];
	}

	/**
	 * @param string $header
	 */
	public function addHeader( $header ) {
		$this->headers .= $this->args['enclosure'] .
		                  $this->escape( $header ) .
		                  $this->args['enclosure'] .
		                  $this->args['delimiter'];
	}

	public function getOutput() {
		if ( $this->output === null ) {
			$this->output = '';

			if ( ! empty( $this->headers ) ) {
				$this->output .= rtrim( $this->headers, $this->args['delimiter'] ) . $this->args['newLine'];
			}

			$this->output .= rtrim( $this->rows ) . $this->args['newLine'];
		}

		return $this->output;
	}

	/**
	 * @throws \Exception
	 */
	public function download() {
		$filename = ( empty( $this->filename ) ) ? time() . '.csv' : $this->filename;

		$output = $this->getOutput();

		if ( headers_sent() ) {
			throw new \Exception( 'Unable to start file download. Response headers already sent.' );
		}

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
	 * @param string $data
	 *
	 * @return string
	 */
	protected function escape( $data ) {
		$find    = array( '"' );
		$replace = array( '""' );

		return str_replace( $find, $replace, $data );
	}

	/**
	 * @param mixed $filename
	 *
	 * @return Csv
	 */
	public function setFilename( $filename ) {
		$this->filename = $filename;

		return $this;
	}
}

