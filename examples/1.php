<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Palmtree\Csv\Writer;
use Palmtree\Csv\Reader;

$csv = new Reader('test.csv');

var_dump(count($csv));

foreach ($csv as $key => $row) {
    var_dump($row['name']);
    var_dump($row);
}

/*$csv = new CsvBuilder( 'test.csv' );

$csv->addHeaders( [ 'name', 'age', 'gender' ] );

$csv->addRow( [ 'Alice', '24', 'Female' ] );
$csv->addRow( [ 'Bob', '28', 'Male' ] );

$csv->write();*/
