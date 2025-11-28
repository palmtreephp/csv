<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Palmtree\Csv\Cell\Cell;
use Palmtree\Csv\Reader;

$csv = new Reader(__DIR__ . '/../people.csv');

/**
 * @var array<Cell> $row
 */
foreach ($csv as $row) {
    echo $row['name'] . \PHP_EOL;
}
