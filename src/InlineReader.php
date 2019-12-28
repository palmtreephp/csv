<?php

namespace Palmtree\Csv;

class InlineReader extends Reader
{
    public function __construct(string $data, bool $hasHeaders = true)
    {
        parent::__construct('php://temp', $hasHeaders);

        $this->getDocument()->fwrite($data);
    }

    public function getOpenMode(): string
    {
        return 'r+';
    }
}
