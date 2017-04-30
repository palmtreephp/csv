<?php

require_once dirname(__DIR__) . '/../vendor/autoload.php';

use Palmtree\Csv\Reader;
use Palmtree\Csv\Writer;

$people   = [];
$people[] = [
    'name'   => "Alice DJ",
    'age'    => '24 \\\\\", words',
    'gender' => 'Female',
];
$people[] = [
    'name'   => 'Bob "Cat" Bobbington',
    'age'    => '28',
    'gender' => 'Male',
];

//$writer = new Writer('../people.csv');
//$writer->setData($people);

Writer::write('../people.csv', $people);

$reader = new Reader('../people.csv');

foreach ($reader as $row) {
    //var_dump($row['age']);
    var_export($row['name']);
}
