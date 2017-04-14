<?php

namespace Palmtree\Csv\Formatter;

use Palmtree\Csv\Cell\Cell;

interface FormatterInterface
{
    const CELL_CLASS = Cell::class;

    /**
     * @param string $value
     *
     * @return mixed
     */
    public function format($value);
}
