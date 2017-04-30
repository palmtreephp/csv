<?php

require_once dirname(__DIR__) . '/../vendor/autoload.php';

use Palmtree\Csv\Cell\Cell;
use Palmtree\Csv\Reader;

$csv = new Reader(__DIR__ . '/../people.csv');

// Iterate over the object directly:
/**
 * @var mixed  $key
 * @var Cell[] $row
 */
foreach ($csv as $key => $row) {
    var_dump($key);
    foreach ($row as $cellKey => $cell) {
        echo "$cellKey: ";
        var_export($cell->getValue());
        echo "\n";
    }
    echo "----\n";
}
