<?php

require_once dirname(__DIR__) . '../vendor/autoload.php';

use Palmtree\Csv\Reader;

$csv = new Reader(__DIR__ . '/test.csv');

foreach ($csv as $key => $row) {
    var_dump($row['name']);
}
