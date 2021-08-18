<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Palmtree\Csv\Cell\Cell;
use Palmtree\Csv\Reader;

$csv = new Reader(__DIR__ . '/../people.csv');

/**
 * @var mixed  $key
 * @var Cell[] $row
 */
foreach ($csv as $key => $row) {
    echo $row['name'] . \PHP_EOL;
}
