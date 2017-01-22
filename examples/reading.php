<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Palmtree\Csv\Reader;

try {
    $csv = new Reader(__DIR__ . '/test.csv');
} catch (InvalidArgumentException $exception) {
    include('writing.php');
}

// Count the rows in the csv (loads the entire CSV file into memory)
$totalRows = count($csv);

// Iterate over the object directly:
foreach ($csv as $key => $row) {
    var_dump($key);
  //  var_dump($row['name']);
    //var_dump($row);

    //var_dump($row['name']);
}

// Use this instead of array_map:
$names = $csv->map(function ($row) {
    return $row['name'];
});

//var_dump($names);

var_dump('loop 2');
// Iterate over the object directly:
foreach ($csv as $key => $row) {
    var_dump($key);
    //var_dump($row['name']);
    //var_dump($row);
}


// Get all rows (don't do this for large files, loads the entire file into memory)
$rows = $csv->getRows();
