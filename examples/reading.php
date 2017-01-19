<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Palmtree\Csv\Reader;

$csv = new Reader('test.csv');

var_dump(count($csv));

$names = $csv->map(function ($row) {
    return $row['name'];
});

var_dump($names);

foreach ($csv as $key => $row) {
    var_dump($row['name']);
    var_dump($row);
}
