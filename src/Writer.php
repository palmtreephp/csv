<?php
namespace Palmtree\Csv;

use Palmtree\ArgParser\ArgParser;

/**
 * Class Csv
 * @package    Palmtree
 * @subpackage Csv
 */
class Writer
{
    public static $defaultArgs = [
        'filename'  => '',
        'delimiter' => ',',
        'enclosure' => '"',
        'newLine'   => "\r\n",
        'escape'    => [
            '"' => '""',
        ],
    ];

    protected $args = [];

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

    public function __construct($args = [])
    {
        $this->args = $this->parseArgs($args);
    }

    public function setData($data, $headers = true)
    {
        if ($headers) {
            $this->addHeaders(array_keys(reset($data)));
        }

        $this->addRows($data);
    }

    /**
     * @param array $headers
     */
    public function addHeaders($headers)
    {
        foreach ($headers as $header) {
            $this->addHeader($header);
        }
    }

    /**
     * @param array $rows
     */
    public function addRows($rows)
    {
        foreach ($rows as $key => $row) {
            $this->addRow($row);
        }
    }

    /**
     * @param string $row
     */
    public function addRow($row)
    {
        $this->rows .= $this->args['enclosure'] .
                       implode($this->args['enclosure'] . $this->args['delimiter'] . $this->args['enclosure'],
                           $this->escape($row)) .
                       $this->args['enclosure'] .
                       $this->args['newLine'];
    }

    /**
     * @param string $header
     */
    public function addHeader($header)
    {
        $this->headers .= $this->args['enclosure'] .
                          $this->escape($header) .
                          $this->args['enclosure'] .
                          $this->args['delimiter'];
    }

    public function getHeaders()
    {
        if (empty($this->headers)) {
            return '';
        }

        $headers = rtrim($this->headers, $this->args['delimiter']);
        $headers .= $this->args['newLine'];

        return $headers;
    }

    public function getRows()
    {
        $rows = rtrim($this->rows);
        $rows .= $this->args['newLine'];

        return $rows;
    }

    public function getOutput()
    {
        if ($this->output === null) {
            $this->output = $this->getHeaders() . $this->getRows();
        }

        return $this->output;
    }

    /**
     * @throws \Exception
     */
    public function download()
    {
        if (headers_sent()) {
            throw new \Exception('Unable to start file download. Response headers already sent.');
        }

        header('Content-Type: application/octet-stream');
        header('Content-Description: File Transfer');
        header('Content-Transfer-Encoding: Binary');
        header('Content-Disposition: attachment; filename="' . $this->getFilename() . '"');

        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        $output = $this->getOutput();

        header('Content-Length: ' . mb_strlen($output));

        print $output;
    }

    public function write()
    {
        return file_put_contents($this->getFilename(), $this->getOutput());
    }

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    protected function escape($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->escape($value);
            }
        } else {
            $data = strtr($data, $this->args['escape']);
        }

        return $data;
    }

    public function getFilename()
    {
        $filename = (empty($this->filename)) ? time() . '.csv' : $this->filename;

        return $filename;
    }

    /**
     * @param mixed $filename
     *
     * @return Writer
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    protected function parseArgs($args = [])
    {
        $parser = new ArgParser($args, 'filename');
        $parser->parseSetters($this);

        return $parser->resolveOptions(self::$defaultArgs);
    }
}

