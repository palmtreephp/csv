<?php

declare(strict_types=1);

namespace Palmtree\Csv;

class InlineReader extends Reader
{
    public function __construct(string $data, bool $hasHeaders = true)
    {
        parent::__construct('php://temp', $hasHeaders);

        $this->getDocument()->fwrite($data);
    }

    protected function getOpenMode(): string
    {
        return 'r+';
    }
}
