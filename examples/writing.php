<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Palmtree\Csv\Writer;

$people   = [];
$people[] = [
    'name'  => 'Alice',
    'age'   => '24',
    'gener' => 'Female',
];
$people[] = [
    'name'  => 'Bob',
    'age'   => '28',
    'gener' => 'Male',
];

$csv = new Writer($people);

$csv->write('people.csv');
