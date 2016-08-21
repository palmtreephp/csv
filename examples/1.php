<?php

require dirname( __DIR__ ) . '/vendor/autoload.php';

use Palmtree\Csv\Csv;
use Palmtree\Csv\CsvParser;

$csv = new CsvParser( 'test.csv' );

foreach ( $csv as $key => $row ) {
	var_dump($row['name']);
	var_dump( $row );
}

$csv = new Csv();

$csv->setFilename( 'test2.csv' );
$csv->addHeaders( [ 'name', 'age', 'gender' ] );
$csv->addRow( [ 'Alice', '24', 'Female'] );
$csv->addRow( [ 'Bob', '28', 'Male' ] );

$csv->download();
